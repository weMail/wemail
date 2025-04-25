<?php

namespace WeDevs\WeMail\Rest\Help;

use WeDevs\WeMail\Core\Help\Services\PingService;
use WeDevs\WeMail\Rest\Middleware\WeMailMiddleware;
use WeDevs\WeMail\RestController;
use WeDevs\WeMail\Core\Help\SystemInfo;
use WP_REST_Server;
use WP_User_Query;

class Help extends RestController {

    public $rest_base = '/help';

    public function register_routes() {
        register_rest_route(
            $this->namespace,
            $this->rest_base . '/system-info',
            array(
                array(
                    'methods'             => WP_REST_Server::READABLE,
                    'permission_callback' => array( $this, 'can_view_wemail' ),
                    'callback'            => array( $this, 'index' ),
                ),
            )
        );

        register_rest_route(
            $this->namespace,
            $this->rest_base . '/tools/ping/send',
            array(
                array(
                    'methods'             => WP_REST_Server::CREATABLE,
                    'permission_callback' => array( $this, 'can_view_wemail' ),
                    'callback'            => array( $this, 'send_ping' ),
                ),
            )
        );

        register_rest_route(
            $this->namespace,
            $this->rest_base . '/tools/ping/receive',
            array(
                array(
                    'methods'             => WP_REST_Server::CREATABLE,
                    'permission_callback' => array( $this, 'can_view_wemail' ),
                    'callback'            => array( $this, 'receive_ping' ),
                ),
            )
        );

        register_rest_route(
            $this->namespace,
            $this->rest_base . '/tools/disconnect',
            array(
                array(
                    'methods'             => WP_REST_Server::DELETABLE,
                    'permission_callback' => array( $this, 'manage_options' ),
                    'callback'            => array( $this, 'disconnect_wemail' ),
                ),
            )
        );
        register_rest_route(
            $this->namespace,
            $this->rest_base . '/admin/users',
            array(
                array(
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'users' ),
                    'permission_callback' => array( $this, 'permission' ),
                ),
            )
        );
    }

    /**
     * Get all system infos
     * @return \WP_Error|\WP_HTTP_Response|\WP_REST_Response
     */
    public function index() {
        $system = new SystemInfo();

        return rest_ensure_response(
            array(
                'data' => $system->allInfo(),
            )
        );
    }

    /**
     * @param $request
     * @return \WP_Error|\WP_REST_Response
     */
    public function send_ping( $request ) {
        $ping = new PingService();

        $callback_url = get_rest_url() . $this->namespace . $this->rest_base . '/tools/ping/receive';

        return rest_ensure_response( $ping->request_send( $request, $callback_url ) );
    }

    /**
     * @param $request
     * @return \WP_Error|\WP_REST_Response
     */
    public function receive_ping( $request ) {
        $ping = new PingService();

        return rest_ensure_response( $ping->request_receive( $request ) );
    }

    /**
     * @return \WP_REST_Response
     */
    public function disconnect_wemail() {
        delete_metadata( 'user', 0, 'wemail_api_key', '', true );
        delete_metadata( 'user', 0, 'wemail_user_data', '', true );

        return new \WP_REST_Response(
            array(
				'status' => 'success',
			)
        );
    }

    public function users() {
        $args = array(
            'role' => 'administrator',
        );

        $users = get_users( $args );

        $emails = array_map(
            function ( $user ) {
                return $user->user_email;
            },
            $users
        );

        return rest_ensure_response( $emails );
    }

    public function permission( $request ) {
        $api_key = $request->get_header( 'X-WeMail-Key' );

        if ( ! empty( $api_key ) ) {
            $query = new WP_User_Query(
                array(
                    'fields'        => 'ID',
                    'meta_key'      => 'wemail_api_key',
                    'meta_value'    => $api_key,
                )
            );
            return (bool) $query->get_total();
        }
        return false;
    }
}
