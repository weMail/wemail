<?php

namespace WeDevs\WeMail\Rest\Ecommerce;

use WeDevs\WeMail\Core\Ecommerce\WooCommerce\WCOrders;
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
        register_rest_route( $this->namespace, '/' . $this->rest_base, [
            [
                'methods'             => WP_REST_Server::READABLE,
                'permission_callback' => [ $this, 'permission' ],
                'callback'            => [ $this, 'products' ],
            ]
        ] );
    }

    /**
     * @param $request
     * @return bool
     */
    public function permission( $request ) {
        $weMailMiddleware = new WeMailMiddleware('manage_settings');

        return $weMailMiddleware->handle( $request );
    }

    /*
       * Params                   | default
       * -----------------------------------------
       * page                     | 1
       * limit                    | 50
       * status                   | publish
       * order                    | DESC
       * orderby                  | date
   */

    public function products( $request ) {
        $source = $request->get_param( 'source' );

        // Pass specific integrations orders by mentioning source
        if ($source === 'woocommerce') {
            return rest_ensure_response(
                $this->wcProducts( $request )
            );
        } else {
            return rest_ensure_response([
                'data' => [],
                'message' => __('Unknown source.')
            ]);
        }
    }

    public function wcProducts( $request )
    {
        $wcProducts = new WCProducts();

        $args = array(
            'orderby'  => $request->get_param('orderby'),
            'order'    => $request->get_param('order'),
            'status'   => $request->get_param('status'),
            'limit'    => $request->get_param('limit'),
            'page'     => $request->get_param('page')
        );

        return $wcProducts->all( $args );
    }

}
