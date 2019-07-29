<?php

namespace WeDevs\WeMail\Rest;

use WP_REST_Controller;
use WP_REST_Server;

class Pages extends WP_REST_Controller
{
    public $namespace = 'wemail/v1';

    public $rest_base = '/pages';

    public function __construct() {
        $this->register_routes();
    }

    public function register_routes() {
        register_rest_route($this->namespace, $this->rest_base, [
            'methods'    => WP_REST_Server::READABLE,
            'callback'   => [ $this, 'get_pages' ]
        ]);
    }

    public function get_pages() {

        return rest_ensure_response([
            'pages' => array_map(function ($page){
                return [
                    'id'    => $page->ID,
                    'title' => $page->post_title,
                ];
            }, get_pages())
        ]);
    }
}
