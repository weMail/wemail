<?php

namespace WeDevs\WeMail;

class Api {

    /**
     * API root
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $root;

    /**
     * API Key
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $key;

    /**
     * Class constructor
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function __construct() {
        $root = apply_filters( 'wemail-api-root', 'https://api.wemail.com' );

        $this->root = untrailingslashit( $root );
        $this->key  = get_option( 'wemail-api-key', 'apiKey' );
    }

    /**
     * Get class properties
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function get_props() {
        return [
            'root' => $this->root,
            'key'  => $this->key
        ];
    }

    /**
     * API GET request caller
     *
     * @since 1.0.0
     *
     * @param string $url   API resource
     * @param array  $query Additional query
     * @param array  $args  wp_remote_get argument overrides
     *
     * @return array|boolean Array on success, false on failure
     */
    public function get( $url, $query = [], $args = [] ) {
        // global $wp_version;

        $defaults = [
            // 'timeout'     => 5,
            // 'redirection' => 5,
            // 'httpversion' => '1.0',
            // 'user-agent'  => 'WordPress/' . $wp_version . '; ' . home_url(),
            // 'blocking'    => true,
            // 'headers'     => array(),
            // 'cookies'     => array(),
            // 'body'        => null,
            // 'compress'    => false,
            // 'decompress'  => true,
            // 'sslverify'   => true,
            // 'stream'      => false,
            // 'filename'    => null
        ];

        $args = wp_parse_args( $args, $defaults );

        $url = $this->root . $url;

        if ( ! empty( $query ) ) {
            $url = $url . '?' . http_build_query( $query );
        }

        $response = wp_remote_get( $url, $args );

        if ( ! empty( $response['body'] ) ) {
            return json_decode( $response['body'], true );
        }

        return false;
    }

    /**
     * API POST request caller
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
        $defaults = [
            // 'timeout'     => 5,
            // 'redirection' => 5,
            // 'httpversion' => '1.0',
            // 'user-agent'  => 'WordPress/' . $wp_version . '; ' . home_url(),
            // 'blocking'    => true,
            // 'headers'     => array(),
            // 'cookies'     => array(),
            // 'body'        => null,
            // 'compress'    => false,
            // 'decompress'  => true,
            // 'sslverify'   => true,
            // 'stream'      => false,
            // 'filename'    => null
        ];

        $args = wp_parse_args( $args, $defaults );

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
