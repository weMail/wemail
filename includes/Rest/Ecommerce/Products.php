<?php

namespace WeDevs\WeMail\Rest\Ecommerce;

use WeDevs\WeMail\Core\Ecommerce\EDD\EDDProducts;
use WeDevs\WeMail\Rest\Middleware\WeMailMiddleware;
use WP_REST_Controller;
use WP_REST_Server;
use WeDevs\WeMail\Core\Ecommerce\WooCommerce\WCProducts;

class Products extends WP_REST_Controller {

    public $namespace = 'wemail/v1';

    public $rest_base = '/ecommerce/(?P<source>[\w]+)/products';

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
                    'callback'            => [ $this, 'products' ],
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
     * Page                     | 1
     * Limit                    | 50
     * Status                   | publish
     * Order                    | DESC
     * Orderby                  | date
     */

    public function products( $request ) {
        $source = $request->get_param( 'source' );

        // Pass specific integrations orders by mentioning source

        switch ($source) {
            case 'woocommerce':
                return rest_ensure_response(
                    $this->wcProducts( $request )
                );
            case 'edd':
                return rest_ensure_response(
                    $this->eddProducts( $request )
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

    public function wcProducts( $request ) {
        $wc_products = new WCProducts();

        $args = array(
            'last_synced_id' => $request->get_param( 'last_synced_id' ),
            'orderby'        => $request->get_param( 'orderby' ),
            'order'          => $request->get_param( 'order' ),
            'status'         => $request->get_param( 'status' ),
            'limit'          => $request->get_param( 'limit' ),
            'page'           => $request->get_param( 'page' ),
        );

        return $wc_products->all( $args );
    }

    public function eddProducts( $request ) {
        $edd_products = new EDDProducts();

        $args = array(
            'orderby'  => $request->get_param( 'orderby' ),
            'order'    => $request->get_param( 'order' ),
            'status'   => $request->get_param( 'status' ),
            'limit'    => $request->get_param( 'limit' ),
            'page'     => $request->get_param( 'page' ),
        );

        return $edd_products->all( $args );
    }

}
