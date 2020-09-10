<?php

namespace WeDevs\WeMail\Rest\Help;

use WeDevs\WeMail\RestController;
use WeMail\WPInfo\SystemInfo;
use WP_REST_Server;

class Help extends RestController {

    public $rest_base = '/help';

    public function register_routes() {
        register_rest_route(
            $this->namespace,
            $this->rest_base . '/system_info',
            [
                [
                    'methods'             => WP_REST_Server::READABLE,
                    'permission_callback' => [ $this, 'permission' ],
                    'callback'            => [ $this, 'index' ],
                ],
            ]
        );
    }

    /**
     * @param $request
     * @return bool
     */
    public function permission( $request ) {
        return true;
    }

    /**
     * Get all system infos
     * @return \WP_Error|\WP_HTTP_Response|\WP_REST_Response
     */
    public function index() {
        $system = new SystemInfo();

        return rest_ensure_response(
            [
                'data' => [
                    $system->allInfo(),
                ],
            ]
        );
    }

}
