<?php

namespace WeDevs\WeMail\Core\Api;

use WeDevs\WeMail\Traits\Stringy;
use WP_Error;
use WeDevs\WeMail\Traits\Hooker;
use WeDevs\WeMail\Traits\Singleton;

class Api {

    use Singleton;
    use Stringy;

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
    private $query = array();

    /**
     * Content type json or not
     *
     * @since 1.8.0
     *
     * @var bool
     */
    private $json = false;

    /**
     * @var array
     */
    private $roles;

    /**
     * Executes during instance creation
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function boot() {
        $this->root = wemail()->wemail_api;
        $user = wp_get_current_user();
        $roles = json_encode($user->roles);
        $api_key = get_option( 'wemail_api_key' );
        $this->set_api_key( $api_key );
        $this->set_roles( $roles );
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
     * @param string $api_key
     *
     * @return Api
     * @since 1.0.0
     */
    public function set_api_key( $api_key ) {
        $this->api_key = $api_key;

        return $this;
    }

    /**
     * Set user roles
     * @since 1.14.10
     * @param array $roles
     * @return Api
     */
    public function set_roles( $roles ) {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Get user roles
     * @since 1.14.10
     * @return array
     */
    public function get_roles() {
        return $this->roles;
    }

    /**
     * Is api key set on this instance
     * @since
     * @return bool
     */
    public function has_api_key() {
        return (bool) $this->api_key;
    }

    /**
     * Magic method to set resource and endpoints
     *
     * @since 1.0.0
     *
     * @param string $name
     * @param array $args
     *
     * @return \WeDevs\WeMail\Core\Api\Api|void
     */
    public function __call( $name, $args ) {
        if ( ! method_exists( $this, $name ) ) {
            $this->url .= '/' . $this->dasherize( $name );

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
        return array(
            'root'     => $this->root,
            'api_key'  => $this->get_api_key(),
            'user_roles' => $this->get_roles(),
        );
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
        $defaults = array(
            'headers' => array(
                'x-api-key' => $this->get_api_key(),
                'x-user-roles' => $this->get_roles(),
            ),
        );

        return wp_parse_args( $args, $defaults );
    }

    /**
     * Set the query for URL to build
     *
     * @param array $query Associative array
     *
     * @return Api
     * @since 1.0.0
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
    private function build_url( $url = '', $query = array() ) {
        if ( $url ) {
            $url = $this->root . $url;
        } elseif ( $this->url ) {
            $url = $this->root . $this->url;
        }

        if ( ! empty( $query ) ) {
            $this->query = array_merge( $query, $this->query );
        }

        if ( ! empty( $this->query ) ) {
            $url .= '?' . http_build_query( $this->query );
        }

        $this->url   = '';
        $this->query = array();

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
    public function get( $url = '', $query = array(), $args = array() ) {
        $args = $this->args( $args );
        $args['timeout'] = 50;

        $url = $this->build_url( $url, $query );

        $response = wp_remote_get( $url, $args );

        return $this->response( $response );
    }

    /**
     * Get raw response
     *
     * @param string $url
     *
     * @return array|WP_Error
     */
    public function get_response( $url = '' ) {
        $url = $this->build_url( $url );

        $args = $this->args( array() );

        return wp_remote_get( $url, $args );
    }

    /**
     * API - POST request caller
     *
     * @param array $data POST data
     * @param array $args wp_remote_post argument overrides
     *
     * @return mixed
     * @since 1.0.0
     */
    public function post( $data = array(), $args = array() ) {
        $args = $this->args( $args );
        $args['timeout'] = 50;

        $args['body'] = ! empty( $data ) ? $data : null;

        $url = $this->build_url();

        if ( $this->json ) {
            $args['headers']['Content-Type'] = 'application/json';
            $args['headers']['Accept'] = 'application/json';
            $args['data_format'] = 'body';
            $args['body'] = wp_json_encode( $args['body'] );
        }

        $response = wp_remote_post( $url, $args );

        return $this->response( $response );
    }

    /**
     * Determine if we need to send data as JSON
     *
     * @param bool $json
     *
     * @since 1.8.0
     *
     * @return $this
     */
    public function send_json( $json = true ) {
        $this->json = $json;

        return $this;
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
    public function put( $data, $args = array() ) {
        $data['_method'] = 'put';

        return $this->post( $data, $args );
    }

    /**
     * API - DELETE request caller
     *
     * @since 1.0.0
     *
     * @param array $data Additional query data
     * @param array $args wp_remote_request argument overrides
     *
     * @return mixed
     */
    public function delete( $data = array(), $args = array() ) {
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

        $response_code = wp_remote_retrieve_response_code( $response );
        $body = json_decode( $response['body'], true );

        if ( $response_code >= 200 && $response_code <= 299 ) {
            return $body;
        } else {
            $message = is_array( $body ) && array_key_exists( 'message', $body )
                ? $body['message']
                : __( 'Something went wrong', 'wemail' );

            $error_data = array(
                'status' => $response_code,
            );

            if (
                isset( $body['errors'] ) &&
                ! empty( $body['errors'] ) &&
                is_array( $body['errors'] )
            ) {
                $error_data['errors'] = $body['errors'];
            }

            return new WP_Error( 'error', $message, $error_data );
        }
    }
}
