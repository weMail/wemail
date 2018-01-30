<?php

namespace WeDevs\WeMail\Rest;

use WP_REST_Controller;
use WP_REST_Server;

class Customizer extends WP_REST_Controller {

    public $namespace = 'wemail/v1';

    public $rest_base = '/static/customizer/(?P<context>[\w-]+)';

    public function __construct() {
        $this->register_routes();
    }

    public function register_routes() {
        register_rest_route( $this->namespace, '/' . $this->rest_base, [
            'args' => [
                'context' => [
                    'description' => __( 'Customizer context like campaign, wp, woocommerce etc' ),
                    'type'        => 'string',
                    'required'    => true
                ]
            ],
            [
                'methods'             => WP_REST_Server::READABLE,
                'permission_callback' => [ $this, 'permission' ],
                'callback'            => [ $this, 'data' ],
            ]
        ] );
    }

    public function permission( $request ) {
        return wemail()->user->can( 'view_wemail' );
    }

    public function data( $request ) {
        switch ( $request['context'] ) {
            case 'campaign':
            default:
                $customizer = wemail()->campaign->editor->get_customizer_data();
                break;
        }

        $response = rest_ensure_response( [ 'data' => $customizer ] );

        return $response;
    }

}
