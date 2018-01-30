<?php

namespace WeDevs\WeMail\Rest;

use WP_REST_Controller;
use WP_REST_Server;

class Countries extends WP_REST_Controller {

    public $namespace = 'wemail/v1';

    public $rest_base = '/static/countries';

    public function __construct() {
        $this->register_routes();
    }

    public function register_routes() {
        register_rest_route( $this->namespace, '/' . $this->rest_base, [
            [
                'methods'             => WP_REST_Server::READABLE,
                'permission_callback' => [ $this, 'permission' ],
                'callback'            => [ $this, 'countries' ],
            ]
        ] );
    }

    public function permission( $request ) {
        return wemail()->user->can( 'view_wemail' );
    }

    public function countries() {
        $response = rest_ensure_response( [
            'data' => wemail_get_countries()
        ] );

        return $response;
    }

}
