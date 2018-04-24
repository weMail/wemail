<?php

namespace WeDevs\WeMail\Rest;

use WP_Error;
use WP_REST_Controller;
use WP_REST_Server;
use WP_User_Query;

class Site extends WP_REST_Controller {

    public $namespace = 'wemail/v1';

    public $rest_base = '/site';

    public function __construct() {
        $this->register_routes();
    }

    public function register_routes() {
        register_rest_route( $this->namespace, '/' . $this->rest_base . '/settings', [
            'methods'             => WP_REST_Server::CREATABLE,
            'permission_callback' => [ $this, 'can_manage_settings' ],
            'callback'            => [ $this, 'save_settings' ],
        ] );
    }

    public function can_manage_settings( $request ) {
        return wemail()->user->can( 'manage_settings' );
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

        $response = wemail()->api->settings()->$name()->post($settings);

        if ( is_wp_error( $response ) ) {
            return new WP_Error( 'failed_to_save_settings', $response->get_error_message() );
        }

        update_option( "wemail_{$name}", $settings );

        return rest_ensure_response( $response );
    }

}
