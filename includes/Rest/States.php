<?php

namespace WeDevs\WeMail\Rest;

use WP_REST_Controller;
use WP_REST_Server;

class States extends WP_REST_Controller {

    public $namespace = 'wemail/v1';

    public $rest_base = '/static/states/(?P<country>[\w-]+)';

    public function __construct() {
        $this->register_routes();
    }

    public function register_routes() {
        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base,
            array(
                'args' => array(
                    'country' => array(
                        'description' => __( 'ISO 3166-1 alpha-2 country code', 'wemail' ),
                        'type'        => 'string',
                        'required'    => true,
                    ),
                ),
                array(
                    'methods'             => WP_REST_Server::READABLE,
                    'permission_callback' => array( $this, 'permission' ),
                    'callback'            => array( $this, 'states' ),
                ),
            )
        );
    }

    public function permission( $request ) {
        return wemail()->user->can( 'view_wemail' );
    }

    public function states( $request ) {
        $response = rest_ensure_response(
            array(
                'data' => wemail_get_country_states( $request['country'] ),
            )
        );

        return $response;
    }
}
