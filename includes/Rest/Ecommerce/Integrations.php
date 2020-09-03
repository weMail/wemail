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
                'callback'            => [ $this, 'index' ],
            ]
        ] );

        register_rest_route( $this->namespace, $this->rest_base . '/(?P<integration>[\w]+)', [
            [
                'methods'             => WP_REST_Server::READABLE,
                'permission_callback' => [ $this, 'permission' ],
                'callback'            => [ $this, 'show' ],
            ]
        ] );
    }

    public function permission() {
        return wemail()->user->can( 'view_wemail' );
    }

    /**
     * Get all ecommerce integrations status
     * @return \WP_Error|\WP_HTTP_Response|\WP_REST_Response
     */
    public function index() {
        $woocommerceIntegration = new WCIntegration();

        return rest_ensure_response([
            'data' => [
                $woocommerceIntegration->status()
            ]
        ]);
    }

    /**
     * @param $request
     * Get specific ecommerce integration data with params
     * @return \WP_Error|\WP_HTTP_Response|\WP_REST_Response
     */
    public function show( $request ) {
        $integration = $request->get_param( 'integration' );

        $woocommerceIntegration = new WCIntegration();

        // Add other integrations below this
        if ($integration == 'woocommerce') {
            return rest_ensure_response([
                'data' => $woocommerceIntegration->status()
            ]);
        } else {
            return rest_ensure_response([
                'data' => [],
                'message' => __('Unknown source.'),
            ]);
        }
    }

}
