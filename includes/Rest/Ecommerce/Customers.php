<?php

namespace WeDevs\WeMail\Rest\Ecommerce;

use WeDevs\WeMail\Core\Ecommerce\WooCommerce\WCCustomers;
use WeDevs\WeMail\Core\Ecommerce\EDD\EDDCustomers;
use WeDevs\WeMail\Rest\Middleware\WeMailMiddleware;
use WP_REST_Controller;
use WP_REST_Server;

class Customers extends WP_REST_Controller {

    public $namespace = 'wemail/v1';

    public $rest_base = '/ecommerce/(?P<source>[\w]+)/customers';

    public function __construct() {
        $this->register_routes();
    }

    public function register_routes() {
        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base,
            [
                [
                    'methods'             => WP_REST_Server::READABLE,
                    'permission_callback' => [ $this, 'permission' ],
                    'callback'            => [ $this, 'customers' ],
                ],
            ]
        );
    }

    /**
     * @param $request
     * @return bool
     */
    public function permission( $request ) {
        $middleware = new WeMailMiddleware( 'manage_settings' );

        return $middleware->handle( $request );
    }

    /*
     * Params                   | default
     * -----------------------------------------
     * last_synced_id           | null
     * limit                    | 50
     * orderby                  | id
     * page                     | 1
     */

    public function customers( $request ) {
        $source = $request->get_param( 'source' );

        switch ( $source ) {
            case 'woocommerce':
                return rest_ensure_response(
                    $this->wcCustomers( $request )
                );
            case 'edd':
                return rest_ensure_response(
                    $this->eddCustomers( $request )
                );
            default:
                return rest_ensure_response(
                    [
                        'data' => [],
                        'message' => __( 'Unknown source.', 'wemail' ),
                    ]
                );
        }
    }

    public function wcCustomers( $request ) {
        $customers = new WCCustomers();

        $args = array(
            'last_synced_id' => $request->get_param( 'last_synced_id' ),
            'orderby'        => $request->get_param( 'orderby' ),
            'limit'          => $request->get_param( 'limit' ),
            'page'           => $request->get_param( 'page' ),
        );

        return $customers->all( $args );
    }

    public function eddCustomers( $request ) {
        $customers = new EDDCustomers();

        $args = array(
            'last_synced_id' => $request->get_param( 'last_synced_id' ),
            'orderby'        => $request->get_param( 'orderby' ),
            'limit'          => $request->get_param( 'limit' ),
            'page'           => $request->get_param( 'page' ),
        );

        return $customers->all( $args );
    }

}
