<?php

namespace WeDevs\WeMail\Rest;

use WP_REST_Controller;
use WP_REST_Server;

class Customizer extends WP_REST_Controller {

    public $namespace = 'wemail/v1';

    public $rest_base = '/static/customizer/(?P<context>campaign|rss)';

    public function __construct() {
        $this->register_routes();
    }

    public function register_routes() {
        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base,
            array(
                'args' => array(
                    'context' => array(
                        'description' => __( 'Customizer context like campaign, wp, woocommerce etc', 'wemail' ),
                        'type'        => 'string',
                        'required'    => true,
                    ),
                ),
                array(
                    'methods'             => WP_REST_Server::READABLE,
                    'permission_callback' => array( $this, 'permission' ),
                    'callback'            => array( $this, 'data' ),
                ),
            )
        );
    }

    public function permission( $request ) {
        return wemail()->user->can( 'view_wemail' );
    }

    public function data( $request ) {
        return rest_ensure_response(
            array(
				'data' => wemail()->campaign->editor->get_customizer_data( $request['context'] ),
			)
        );
    }
}
