<?php

namespace WeDevs\WeMail\Core\Api;

use Illuminate\Support\Pluralizer;
use Stringy\StaticStringy;
use WP_Error;
use WeDevs\WeMail\Traits\Hooker;
use WeDevs\WeMail\Traits\Singleton;

class Api {

    use Singleton;

    use Hooker;

    /**
     * API root
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $root;

    /**
     * User x-api-key
     *
     * @since 1.0.0
     *
     * @var string
     */
    private $api_key;

    /**
     * Holds the generated api url
     *
     * @since 1.0.0
     *
     * @var string
     */
    private $url = '';

    /**
     * API resource query for URL to build
     *
     * @since 1.0.0
     *
     * @var string
     */
    private $query = [];

    /**
     * Executes during instance creation
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function boot() {
        $this->root = wemail()->wemail_api;
        $api_key = get_user_meta( get_current_user_id(), 'wemail_api_key', true );
        $this->set_api_key( $api_key );
    }

    /**
     * Get the api key
     *
     * @since 1.0.0
     *
     * @return string
     */
    public function get_api_key() {
        /**
         * Filter the current user api key
         *
         * @since 1.0.0
         *
         * @param string $api_key
         */
        return apply_filters( 'wemail_api_key', $this->api_key );
    }

    /**
     * Set the api key
     *
     * @since 1.0.0
     *
     * @param string $api_key
     *
     * @return void
     */
    public function set_api_key( $api_key ) {
        $this->api_key = $api_key;

        return $this;
    }

    /**
     * Magic method to set resource and endpoints
     *
     * @since 1.0.0
     *
     * @param string $name
     * @param array $args
     *
     * @return WeDevs\WeMail\Core\Api\Api|void
     */
    public function __call( $name, $args ) {
        if ( ! method_exists( $this, $name ) ) {
            $this->url .= '/' . StaticStringy::dasherize( $name );

            if ( $args ) {
                $this->url .= '/' . array_pop( $args );
            }

            return $this;
        }
    }

    /**
     * Get selective class properties
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function get_props() {
        return [
            'root'     => $this->root,
            'api_key'  => $this->get_api_key()
        ];
    }

    /**
     * The arguments for wp_remote_get or wp_remote_post
     *
     * @since 1.0.0
     *
     * @param array $args
     *
     * @return array
     */
    private function args( $args ) {
        $defaults = [
            'headers' => [
                'x-api-key' => $this->get_api_key()
            ]
        ];

        return wp_parse_args( $args, $defaults );
    }

    /**
     * Set the query for URL to build
     *
     * @since 1.0.0
     *
     * @param array $query Associative array
     *
     * @return WeDevs\WeMail\Core\Api
     */
    public function query( $query ) {
        $this->query = array_merge( $query, $this->query );

        return $this;
    }

    /**
     * Build API URL from resource, endpoint, params and queries
     *
     * If $url and/or $query are provided explicitly, it will use them
     * instead of resource, endpoints etc
     *
     * @since 1.0.0
     *
     * @param string $url
     * @param array $query
     *
     * @return string
     */
    private function build_url( $url = '', $query = [] ) {
        if ( $url ) {
            $url = $this->root . $url;

        } else if ( $this->url ) {
            $url = $this->root . $this->url;
        }

        if ( ! empty( $query ) ) {
            $this->query = array_merge( $query, $this->query );
        }

        if ( ! empty( $this->query ) ) {
            $url .= '?' . http_build_query( $this->query );
        }

        $this->url   = '';
        $this->query = [];

        return $url;
    }

    /**
     * API - GET request caller
     *
     * @since 1.0.0
     *
     * @param string $url   API resource
     * @param array  $query Additional query
     * @param array  $args  wp_remote_get argument overrides
     *
     * @return mixed
     */
    public function get( $url = '', $query = [], $args = [] ) {
        $args = $this->args( $args );

        $url = $this->build_url( $url, $query );

        $response = wp_remote_get( $url, $args );

        return $this->response( $response );
    }

    /**
     * API - POST request caller
     *
     * @since 1.0.0
     *
     * @param string $url  API resource
     * @param array  $data POST data
     * @param array  $args wp_remote_post argument overrides
     *
     * @return mixed
     */
    public function post( $data, $args = [] ) {
        $args = $this->args( $args );

        $args['body'] = ! empty( $data ) ? $data : null;

        $url = $this->build_url();

        $response = wp_remote_post( $url, $args );

        return $this->response( $response );
    }

    /**
     * API - PUT request caller
     *
     * @since 1.0.0
     *
     * @param array  $data PUT data
     * @param array  $args wp_remote_request argument overrides
     *
     * @return mixed
     */
    public function put( $data, $args = [] ) {
        $data['_method'] = 'put';

        return $this->post( $data, $args );
    }

    /**
     * API - DELETE request caller
     *
     * @since 1.0.0
     *
     * @param [type] $data Additional query data
     * @param array $args wp_remote_request argument overrides
     *
     * @return mixed
     */
    public function delete( $data, $args = [] ) {
        $args = $this->args( $args );

        $args['method'] = 'delete';

        $args['body'] = ! empty( $data ) ? $data : null;

        $url = $this->build_url();

        $response = wp_remote_request( $url, $args );

        return $this->response( $response );
    }

    /**
     * Response handler for API calls
     *
     * @since 1.0.0
     *
     * @param array|WP_Error $response
     *
     * @return array|WP_Error
     */
    private function response( $response ) {
        if ( is_wp_error( $response ) ) {
            return $response;
        }

        $response_code = wp_remote_retrieve_response_code($response);
        $body = json_decode( $response['body'], true );

        if ( $response_code >= 200 && $response_code <= 299 ) {
            return $body;

        } else {
            $message = is_array( $body ) && array_key_exists( 'message', $body )
                ? $body['message']
                : __( 'Something went wrong', 'wemail' );

            return new WP_Error( 'error', $message, [ 'status' => $response_code ] );
        }
    }

}
