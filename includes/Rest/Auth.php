<?php

namespace WeDevs\WeMail\Rest;

use WP_REST_Controller;
use WP_REST_Server;

class Auth extends WP_REST_Controller {

    public $namespace = 'wemail/v1';

    public $rest_base = '/auth';

    public function __construct() {
        $this->register_routes();
    }

    public function register_routes() {
        register_rest_route( $this->namespace, '/' . $this->rest_base . '/site', [
            [
                'methods'             => WP_REST_Server::CREATABLE,
                'permission_callback' => [ $this, 'permission' ],
                'callback'            => [ $this, 'site' ],
            ]
        ] );
    }

    public function permission( $request ) {
        return current_user_can( 'manage_options' );
    }

    public function site() {
        $authenticate = wemail()->auth->site();

        if ( is_wp_error( $authenticate ) ) {
            return new \WP_Rest_Response( $authenticate, 422 );
        }

        $response = rest_ensure_response( [
            'data' => []
        ] );

        return $response;
    }

}
