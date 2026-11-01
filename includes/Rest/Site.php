<?php

namespace WeDevs\WeMail\Rest;

use WeDevs\WeMail\RestController;

class Site extends RestController {

    public $rest_base = '/site';

    public function register_routes() {
        $this->post( '/settings', 'save_settings', 'manage_options' );
        $this->post( '/settings/save-options', 'save_options', 'manage_options_or_service' );
        $this->delete( '', 'delete_site', 'check_delete_permission' );
        $this->get( '/authentication', 'check_after_update', 'manage_options' );
    }

    /**
     * Save site settings
     *
     * We're not going to save all settings in WP database, only a few.
     * Use this endpoint only when we need to save a settings in WP too.
     *
     * @since 1.0.0
     *
     * @param \WP_REST_Request $request
     *
     * @return \WP_REST_Response|mixed
     */
    public function save_settings( $request ) {
        $name = $request->get_param( 'name' );

        if ( ! is_string( $name ) || ! in_array( $name, $this->allowed_setting_names(), true ) ) {
            return $this->respond_error( __( 'Invalid settings name.', 'wemail' ), 'invalid_settings_name' );
        }

        $settings = $request->get_param( 'settings' );

        $response = wemail()->api->settings()->$name()->post( $settings );

        if ( is_wp_error( $response ) ) {
            return $this->respond_error( $response->get_error_message(), 'failed_to_save_settings' );
        }

        update_option( "wemail_{$name}", $settings );

        do_action( "wemail_saved_settings_{$name}", $settings );

        return rest_ensure_response( $response );
    }

    /**
     * Setting names the /site/settings endpoint may persist locally
     *
     * @since 2.1.7
     *
     * @return array
     */
    private function allowed_setting_names() {
        $allowed = apply_filters(
            'wemail_site_settings_allowed_names',
            array( 'general', 'transactional_emails', 'sync_subscriber_wp', 'sync_subscriber_erp_contacts' )
        );

        // Never let a filter re-open credential / ACL / integration options.
        $blocked = array( 'api_key', 'accessible_roles', 'ecommerce', 'site_slug', 'version' );

        return array_values( array_diff( (array) $allowed, $blocked ) );
    }

    /**
     * Save local options
     *
     * @since 2.1.7 The response reports how many keys were saved and which were
     * refused, so a request where nothing was saved no longer reports success.
     *
     * @param $request \WP_REST_Request
     *
     * @return mixed|\WP_Error|\WP_HTTP_Response|\WP_REST_Response
     */
    public function save_options( $request ) {
        $settings = $request->get_param( 'settings' );

        if ( ! is_array( $settings ) ) {
            return $this->respond_error( __( 'Invalid settings.', 'wemail' ), 'invalid_settings' );
        }

        $blocked  = array( 'api_key', 'accessible_roles', 'site_slug', 'version', 'ecommerce' );
        $saved    = 0;
        $rejected = array();

        foreach ( $settings as $key => $setting ) {
            if ( ! is_string( $key ) ) {
                continue;
            }

            // Canonical name: one normalized form for the blocklist compare and
            // the written option, so " Api_Key " cannot bypass the blocklist.
            $name = strtolower( trim( $key ) );

            if ( in_array( $name, $blocked, true ) ) {
                $rejected[] = $name;
                continue;
            }

            if ( rest_is_boolean( $setting ) ) {
                update_option( "wemail_{$name}", wemail_validate_boolean( $setting ) ? 1 : 0 );
                ++$saved;
                continue;
            }

            update_option( "wemail_{$name}", $setting );
            ++$saved;
        }

        return rest_ensure_response(
            array(
                'success'  => $saved > 0,
                'saved'    => $saved,
                'rejected' => $rejected,
            )
        );
    }

    public function delete_site() {
        $this->delete_option_by_prefix( 'wemail_form_integration_' );
        $this->delete_user_metadata( array( 'wemail_api_key', 'wemail_user_data' ) );

        $wemail_options = array(
            'wemail_version',
            'wemail_site_slug',
            'wemail_accessible_roles',
            'wemail_transactional_emails',
            'wemail_general',
            'wemail_edd_integrated',
            'wemail_is_edd_synced',
            'wemail_woocommerce_integrated',
            'wemail_is_woocommerce_synced',
            'wemail_api_key',
            'wemail_site_connection_notice',
        );

        foreach ( $wemail_options as $option ) {
            delete_option( $option );
        }

        return rest_ensure_response( array( 'success' => true ) );
    }

    public function delete_option_by_prefix( $prefix ) {
        global $wpdb;

        $wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->options WHERE option_name LIKE %s", $prefix . '%' ) );
    }

    public function delete_user_metadata( $keys ) {
        global $wpdb;

        if ( empty( $keys ) ) {
            return;
        }

        $placeholders = implode( ', ', array_fill( 0, count( $keys ), '%s' ) );
        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- {$wpdb->usermeta} is a core table property, values prepared
        $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->usermeta} WHERE meta_key IN ($placeholders)", $keys ) );
    }

    public function check_after_update() {
        $api_key = get_option( 'wemail_api_key' );

        if ( empty( $api_key ) ) {
            // Get user meta API key for header
            $user_api_key = get_user_meta( get_current_user_id(), 'wemail_api_key', true );

            // Set the API key in the API instance for proper header inclusion
            if ( ! empty( $user_api_key ) ) {
                wemail()->api->set_api_key( $user_api_key );
            }

            $response = wemail()->api->site()->authentication()->send_json()->post( array( 'site_url' => get_site_url() ) );

            if ( is_wp_error( $response ) ) {
                return rest_ensure_response(
                    array(
                        'success' => false,
                        'redirect_url' => admin_url( 'admin.php?page=wemail' ),
                        'message' => $response->get_error_message(),
                    )
                );
            }

            if ( empty( $response['key'] ) || ! is_string( $response['key'] ) ) {
                return rest_ensure_response(
                    array(
                        'success' => false,
                        'redirect_url' => admin_url( 'admin.php?page=wemail' ),
                        'message' => __( 'weMail could not connect this site. Please try again.', 'wemail' ),
                    )
                );
            }

            update_option( 'wemail_api_key', $response['key'] );
            wemail()->api->set_api_key( $response['key'] );

            return rest_ensure_response(
                array(
                    'success' => true,
                    'redirect_url' => admin_url( 'admin.php?page=wemail' ),
                    'message' => __( 'Site connected successfully!', 'wemail' ),
                )
            );
		}
	}

    /**
     * Check delete site permission
     *
     * @since 2.0.4
     *
     * @param \WP_REST_Request $request
     *
     * @return bool
     */
    public function check_delete_permission( $request ) {
        $api_key    = $request->get_header( 'x-wemail-key' );
        $wp_api_key = get_option( 'wemail_api_key' );

        if ( empty( $wp_api_key ) || empty( $api_key ) || ! is_string( $api_key ) || ! is_string( $wp_api_key ) ) {
            return false;
        }

        return hash_equals( $wp_api_key, $api_key );
    }
}
