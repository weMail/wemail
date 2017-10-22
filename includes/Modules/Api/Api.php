<?php

namespace WeDevs\WeMail\Modules\Api;

use Illuminate\Support\Pluralizer;
use Stringy\StaticStringy;
use WeDevs\WeMail\Framework\Traits\Hooker;

class Api {

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
     * Site authentication key
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $site;

    /**
     * User authentication key
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $user;

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
     * Class constructor
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function __construct() {
        $root = apply_filters( 'wemail_api_root', 'https://api.wemail.com' );
        $this->root  = untrailingslashit( $root );

        $site_key    = get_option( 'wemail_api_site_key', 'siteKey' );
        $this->site  = apply_filters( 'wemail_api_site_key', $site_key );

        $user_key    = get_user_meta( get_current_user_id(), 'wemail_api_user_key' );
        $this->user  = apply_filters( 'wemail_api_user_key', $user_key );
    }

    /**
     * Magic method to set resource and endpoints
     *
     * @since 1.0.0
     *
     * @param string $name
     * @param array $args
     *
     * @return WeDevs\WeMail\Modules\Api\Api|void
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
            'root'  => $this->root,
            'site'  => $this->site,
            'user'  => $this->user
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
                'Authorization' => 'Bearer ' . $this->user
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
     * @return WeDevs\WeMail\Modules\Api
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
    private function build_url( $url, $query ) {
        if ( $url ) {
            $url = $this->root . $url;

        } else if ( $this->url ) {
            $url = $this->root . $this->url;
        }

        if ( ! empty( $query ) ) {
            $this->query = array_merge( $query, $this->query );
        }

        $url .= '?' . http_build_query( $this->query );

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
     * @return array|boolean Array on success, false on failure
     */
    public function get( $url = '', $query = [], $args = [] ) {
        $args = $this->args( $args );

        $url = $this->build_url( $url, $query );

        $response = wp_remote_get( $url, $args );

        if ( ! is_wp_error( $response ) && ! empty( $response['body'] ) ) {
            return json_decode( $response['body'], true );
        }

        return false;
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
     * @return array|boolean Array on success, false on failure
     */
    public function post( $url, $data, $args = [] ) {
        $args = $this->args( $args );

        $args['body'] = ! empty( $data ) ? $data : null;

        $url = $this->root . $url;

        $response = wp_remote_post( $url, $args );

        if ( is_wp_error( $response ) ) {
            $error_message = $response->get_error_message();
        } else {
            if ( ! empty( $response['body'] ) ) {
                return json_decode( $response['body'], true );
            }
        }

        return false;
    }

}
