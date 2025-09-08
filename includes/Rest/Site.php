<?php

namespace WeDevs\WeMail\Rest;

use WeDevs\WeMail\Core\Ecommerce\EDD\EDDSettings;
use WeDevs\WeMail\Core\Ecommerce\Requests\Settings;
use WeDevs\WeMail\Core\Ecommerce\WooCommerce\WCSettings;
use WeDevs\WeMail\RestController;

class Site extends RestController {

    public $rest_base = '/site';

    public function register_routes() {
        $this->post( '/settings', 'save_settings', 'can_manage_settings' );
        $this->post( '/settings/save-options', 'save_options', 'manage_options' );
        $this->delete( '', 'delete_site', 'can_manage_settings' );
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
     * @param $request \WP_REST_Request
     *
     * @return mixed|\WP_Error|\WP_HTTP_Response|\WP_REST_Response
     */
    public function save_options( $request ) {
        $settings = $request->get_param( 'settings' );

        foreach ( $settings as $key => $setting ) {
            if ( rest_is_boolean( $setting ) ) {
                update_option( "wemail_{$key}", wemail_validate_boolean( $setting ) ? 1 : 0 );

                // Update site settings data on server
                $this->update_site_settings_on_server( $key );
                continue;
            }

            update_option( "wemail_{$key}", $setting );
        }

        return rest_ensure_response( array( 'success' => true ) );
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
        $keys = implode( "','", array_map( 'esc_sql', $keys ) );
        $wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->usermeta WHERE meta_key IN ('" . $keys . "')" ) );
    }

    /**
     * @param $key
     */
    public function update_site_settings_on_server( $key ) {
        switch ( $key ) {
            case 'is_woocommerce_synced':
                $setting = new WCSettings();
                break;
            case 'is_edd_synced':
                $setting = new EDDSettings();
                break;
            default:
                return;
        }

        $settings = $setting->get();
        $setting = new Settings();
        $setting->update( $settings );

        return;
    }
}
