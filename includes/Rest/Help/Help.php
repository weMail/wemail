<?php

namespace WeDevs\WeMail\Rest\Help;

use WeDevs\WeMail\Core\Help\Services\PingService;
use WeDevs\WeMail\RestController;
use WeMail\WPInfo\SystemInfo;
use WP_REST_Server;

class Help extends RestController {

    public $rest_base = '/help';

    public function register_routes() {
        register_rest_route(
            $this->namespace,
            $this->rest_base . '/system-info',
            [
                [
                    'methods'             => WP_REST_Server::READABLE,
                    'permission_callback' => [ $this, 'permission' ],
                    'callback'            => [ $this, 'index' ],
                ],
            ]
        );

        register_rest_route(
            $this->namespace,
            $this->rest_base . '/tools/ping/send',
            [
                [
                    'methods'             => WP_REST_Server::CREATABLE,
                    'permission_callback' => [ $this, 'permission' ],
                    'callback'            => [ $this, 'send_ping' ],
                ],
            ]
        );

        register_rest_route(
            $this->namespace,
            $this->rest_base . '/tools/ping/receive',
            [
                [
                    'methods'             => WP_REST_Server::CREATABLE,
                    'permission_callback' => [ $this, 'permission' ],
                    'callback'            => [ $this, 'receive_ping' ],
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
                'data' => $system->allInfo(),
            ]
        );
    }

    public function send_ping( $request ) {
        $ping = new PingService();

        $callback_url = get_rest_url() . $this->namespace . $this->rest_base . '/tools/ping/receive';

        return rest_ensure_response( $ping->request_send( $request, $callback_url ) );
    }

    public function receive_ping( $request ) {
        $ping = new PingService();

        return rest_ensure_response( $ping->request_receive( $request ) );
    }




}
