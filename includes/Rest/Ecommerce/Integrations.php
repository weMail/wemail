<?php

namespace WeDevs\WeMail\Rest\Ecommerce;

use WeDevs\WeMail\RestController;
use WP_REST_Server;
use WeDevs\WeMail\Core\Ecommerce\WooCommerce\WCIntegration;

class Integrations extends RestController {

    public $rest_base = '/ecommerce/integrations';

    public function register_routes() {
        register_rest_route( $this->namespace, $this->rest_base, [
            [
                'methods'             => WP_REST_Server::READABLE,
                'permission_callback' => [ $this, 'permission' ],
                'callback'            => [ $this, 'status' ],
            ]
        ] );
    }

    public function permission() {
        return wemail()->user->can( 'view_wemail' );
    }

    /**
     * @param $request
     * Get specific ecommerce integration data with params
     * @return \WP_Error|\WP_HTTP_Response|\WP_REST_Response
     */
    public function status( $request ) {
        $source = $request->get_param( 'source' );

        $woocommerceIntegration = new WCIntegration();

        if ($source == 'woocommerce') {
            return rest_ensure_response([
                'woocommerce' => $woocommerceIntegration->status()
            ]);
        }

        return rest_ensure_response([
            'integrations' => [
                'woocommerce' => $woocommerceIntegration->status()
            ]
        ]);
    }
}
