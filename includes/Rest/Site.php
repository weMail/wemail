<?php

namespace WeDevs\WeMail\Rest;

use WeDevs\WeMail\RestController;

class Site extends RestController {

    public $rest_base = '/site';

    public function register_routes() {
        $this->post( '/settings', 'save_settings', 'can_manage_settings' );
        $this->post( '/settings/transactionalemails', 'save_transactional_emails_settings', 'manage_options' );
        $this->post( '/settings/save-options', 'save_options', 'manage_options' );
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

    public function save_transactional_emails_settings( $request ) {
        $status = $request->get_param( 'status' );

        if ( $status == 'true' ) {
            update_option( 'wemail_transactional_emails', true );
        } else {
            delete_option( 'wemail_transactional_emails' );
        }

        return rest_ensure_response( ['success' => true] );
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
                continue;
            }

            update_option( "wemail_{$key}", $setting );
        }

        return rest_ensure_response( ['success' => true] );
    }

}
