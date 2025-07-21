<?php

namespace WeDevs\WeMail\Rest;

use WeDevs\WeMail\Core\Ecommerce\EDD\EDDSettings;
use WeDevs\WeMail\Core\Ecommerce\Requests\Settings;
use WeDevs\WeMail\Core\Ecommerce\WooCommerce\WCSettings;
use WeDevs\WeMail\RestController;

class Remote extends RestController {

    public $rest_base = '/remote';

    public function register_routes() {
        $this->post( '/', 'call_remote', 'manage_options' );
    }

    /**
     * Call remote api | weMail api
     * This is proxy to call weMail api
     *
     * @since 2.0.0
     *
     * @param \WP_REST_Request $request
     *
     * @return \WP_REST_Response|mixed
     */
    public function call_remote( $request ) {
        $url = $request->get_param( 'url' );
        $method = strtolower( sanitize_text_field( $request->get_param( 'method' ) ) );
        $data = $request->get_param( 'data' );
        $wemail_api_base = wemail()->wemail_api;
        $path = str_replace( $wemail_api_base, '', $url );
        $query = $request->get_param( 'query' );

        if ( $method === 'get' ) {
            $response = wemail()->api->send_json()->get( $path, $query );
        } else {
            $data = array_merge( count($data) ? $data : array(), count($query) ? $query : array() );
            $response = wemail()->api->url( $path )->send_json()->{$method}( $data, array() );
        }

        return $response;
    }
}
