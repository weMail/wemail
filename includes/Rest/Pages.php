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
        register_rest_route( $this->namespace, '/' . $this->rest_base, [
            [
                'methods'             => WP_REST_Server::READABLE,
                'permission_callback' => [ $this, 'permission' ],
                'callback'            => [ $this, 'pages' ],
            ]
        ] );
    }

    public function permission( $request ) {
        return true;
    }

    public function pages() {
        $args = array(
            'sort_order' => 'asc',
            'sort_column' => 'post_title',
            'post_type' => 'page',
            'post_status' => 'publish'
        );

        $pages = get_pages($args);
        $results = [
            [
                'title' => 'Home Page',
                'permalink' => get_home_url()
            ]
        ];
        foreach ($pages as $page) {
            array_push($results, [
                'title' => $page->post_title,
                'permalink' => $page->guid
            ]);
        }

        $response = rest_ensure_response( [
            'data' => $results
        ] );

        return $response;
    }

}
