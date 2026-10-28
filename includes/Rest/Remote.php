<?php

namespace WeDevs\WeMail\Rest;

use WeDevs\WeMail\Core\Ecommerce\EDD\EDDSettings;
use WeDevs\WeMail\Core\Ecommerce\Requests\Settings;
use WeDevs\WeMail\Core\Ecommerce\WooCommerce\WCSettings;
use WeDevs\WeMail\RestController;

class Remote extends RestController {

    public $rest_base = '/remote';

    /**
     * The weMail API paths whose payload is persisted by the SaaS and later used
     * as the site credential for server-to-WP callbacks.
     *
     * Exact matches only — third-party import endpoints (e.g. /import/mailchimp/*)
     * carry the user's own external api_key and must never be overwritten.
     *
     * @since 2.1.7
     *
     * @var array
     */
    private $site_key_paths = array(
        '/import/csv/start',
        '/import/mail-poet/start',
    );

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
     * Stamp the site API key into the payload of allow-listed proxy paths
     *
     * @since 2.1.7
     *
     * @param string $path Proxied weMail API path
     * @param array  $data Request payload
     *
     * @return array
     */
    private function stamp_site_api_key( $path, $data ) {
        $parts    = explode( '?', $path );
        $endpoint = untrailingslashit( $parts[0] );

        if ( in_array( $endpoint, $this->site_key_paths, true ) ) {
            $data['api_key'] = wemail()->api->get_api_key();
        }

        return $data;
    }

    /**
     * Call remote api | weMail api
     * This is proxy to call weMail api
     *
     * @since 2.0.0
     * @since 2.1.7 Stamps site api_key for allow-listed import paths.
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
            $data = $this->stamp_site_api_key( $path, $data );
            $response = wemail()->api->url( $path )->send_json()->{$method}( $data, array() );
        }

        return $response;
    }
}
