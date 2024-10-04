<?php

namespace WeDevs\WeMail\Rest;

use WP_REST_Controller;
use WP_REST_Server;

class Pages extends WP_REST_Controller {

    public $namespace = 'wemail/v1';

    public $rest_base = '/static/pages';

    public function __construct() {
        $this->register_routes();
    }

    public function register_routes() {
        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base,
            array(
                array(
                    'methods'             => WP_REST_Server::READABLE,
                    'permission_callback' => array( $this, 'permission' ),
                    'callback'            => array( $this, 'index' ),
                ),
            )
        );
    }

    public function permission( $request ) {
        return true;
    }

    public function index() {
        $args = array(
            'sort_order' => 'asc',
            'sort_column' => 'post_title',
            'post_type' => 'page',
            'post_status' => 'publish',
        );

        return rest_ensure_response(
            array(
                'data' => array_map(
                    function ( $page ) {
                        return array(
                            'id'        => $page->ID,
                            'title'     => $page->post_title,
                            'permalink' => get_permalink( $page->ID ),
                        );
                    },
                    get_pages( $args )
                ),
            )
        );
    }
}
