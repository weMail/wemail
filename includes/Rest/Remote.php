<?php

namespace WeDevs\WeMail\Rest;

use WeDevs\WeMail\Core\Ecommerce\EDD\EDDSettings;
use WeDevs\WeMail\Core\Ecommerce\Requests\Settings;
use WeDevs\WeMail\Core\Ecommerce\WooCommerce\WCSettings;
use WeDevs\WeMail\RestController;

class Remote extends RestController {

    public $rest_base = '/remote';

    public function register_routes() {
        $this->post( '/', 'call_remote', 'can_remote_call' );
    }

    /**
     * Recursively sanitize input data (array/object/string)
     */
    private function wemail_recursive_sanitize( $data ) {
        if ( is_array( $data ) ) {
            foreach ( $data as $key => $value ) {
                $data[ $key ] = $this->wemail_recursive_sanitize( $value );
            }
            return $data;
        } elseif ( is_bool( $data ) ) {
            return $data;
        } elseif ( is_email( $data ) ) {
            return sanitize_email( $data );
        } elseif ( is_numeric( $data ) ) {
            return absint( $data );
        } elseif ( filter_var( $data, FILTER_VALIDATE_URL ) ) {
            return esc_url_raw( $data );
        } else {
            return sanitize_text_field( $data );
        }
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
        $url = esc_url_raw( $request->get_param( 'url' ) );
        $method = strtolower( sanitize_text_field( $request->get_param( 'method' ) ) );

        $data = $request->get_param( 'data' );
        if ( is_array( $data ) ) {
            $data = $this->wemail_recursive_sanitize( $data );
        }

        $query = $request->get_param( 'query' );
        if ( is_array( $query ) ) {
            $query = $this->wemail_recursive_sanitize( $query );
        }

        $wemail_api_base = wemail()->wemail_api;
        $path = str_replace( $wemail_api_base, '', $url );

        if ( $method === 'get' ) {
            $response = wemail()->api->send_json()->get( $path, $query );
        } else {
            $data = array_merge( $data ? $data : array(), $query ? $query : array() );
            $response = wemail()->api->url( $path )->send_json()->{$method}( $data, array() );
        }

        return $response;
    }
}
