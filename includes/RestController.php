<?php

namespace WeDevs\WeMail;

use WP_Error;
use WP_REST_Controller;
use WP_REST_Response;
use WP_REST_Server;
use WP_User_Query;

abstract class RestController extends WP_REST_Controller {

    /**
     * HTTP response codes
     * Source: \Symfony\Component\HttpFoundation\Response
     */
    const HTTP_CONTINUE = 100;
    const HTTP_SWITCHING_PROTOCOLS = 101;
    const HTTP_PROCESSING = 102;            // RFC2518
    const HTTP_OK = 200;
    const HTTP_CREATED = 201;
    const HTTP_ACCEPTED = 202;
    const HTTP_NON_AUTHORITATIVE_INFORMATION = 203;
    const HTTP_NO_CONTENT = 204;
    const HTTP_RESET_CONTENT = 205;
    const HTTP_PARTIAL_CONTENT = 206;
    const HTTP_MULTI_STATUS = 207;          // RFC4918
    const HTTP_ALREADY_REPORTED = 208;      // RFC5842
    const HTTP_IM_USED = 226;               // RFC3229
    const HTTP_MULTIPLE_CHOICES = 300;
    const HTTP_MOVED_PERMANENTLY = 301;
    const HTTP_FOUND = 302;
    const HTTP_SEE_OTHER = 303;
    const HTTP_NOT_MODIFIED = 304;
    const HTTP_USE_PROXY = 305;
    const HTTP_RESERVED = 306;
    const HTTP_TEMPORARY_REDIRECT = 307;
    const HTTP_PERMANENTLY_REDIRECT = 308;  // RFC7238
    const HTTP_BAD_REQUEST = 400;
    const HTTP_UNAUTHORIZED = 401;
    const HTTP_PAYMENT_REQUIRED = 402;
    const HTTP_FORBIDDEN = 403;
    const HTTP_NOT_FOUND = 404;
    const HTTP_METHOD_NOT_ALLOWED = 405;
    const HTTP_NOT_ACCEPTABLE = 406;
    const HTTP_PROXY_AUTHENTICATION_REQUIRED = 407;
    const HTTP_REQUEST_TIMEOUT = 408;
    const HTTP_CONFLICT = 409;
    const HTTP_GONE = 410;
    const HTTP_LENGTH_REQUIRED = 411;
    const HTTP_PRECONDITION_FAILED = 412;
    const HTTP_REQUEST_ENTITY_TOO_LARGE = 413;
    const HTTP_REQUEST_URI_TOO_LONG = 414;
    const HTTP_UNSUPPORTED_MEDIA_TYPE = 415;
    const HTTP_REQUESTED_RANGE_NOT_SATISFIABLE = 416;
    const HTTP_EXPECTATION_FAILED = 417;
    const HTTP_I_AM_A_TEAPOT = 418;                                               // RFC2324
    const HTTP_MISDIRECTED_REQUEST = 421;                                         // RFC7540
    const HTTP_UNPROCESSABLE_ENTITY = 422;                                        // RFC4918
    const HTTP_LOCKED = 423;                                                      // RFC4918
    const HTTP_FAILED_DEPENDENCY = 424;                                           // RFC4918
    const HTTP_RESERVED_FOR_WEBDAV_ADVANCED_COLLECTIONS_EXPIRED_PROPOSAL = 425;   // RFC2817
    const HTTP_UPGRADE_REQUIRED = 426;                                            // RFC2817
    const HTTP_PRECONDITION_REQUIRED = 428;                                       // RFC6585
    const HTTP_TOO_MANY_REQUESTS = 429;                                           // RFC6585
    const HTTP_REQUEST_HEADER_FIELDS_TOO_LARGE = 431;                             // RFC6585
    const HTTP_UNAVAILABLE_FOR_LEGAL_REASONS = 451;
    const HTTP_INTERNAL_SERVER_ERROR = 500;
    const HTTP_NOT_IMPLEMENTED = 501;
    const HTTP_BAD_GATEWAY = 502;
    const HTTP_SERVICE_UNAVAILABLE = 503;
    const HTTP_GATEWAY_TIMEOUT = 504;
    const HTTP_VERSION_NOT_SUPPORTED = 505;
    const HTTP_VARIANT_ALSO_NEGOTIATES_EXPERIMENTAL = 506;                        // RFC2295
    const HTTP_INSUFFICIENT_STORAGE = 507;                                        // RFC4918
    const HTTP_LOOP_DETECTED = 508;                                               // RFC5842
    const HTTP_NOT_EXTENDED = 510;                                                // RFC2774
    const HTTP_NETWORK_AUTHENTICATION_REQUIRED = 511;                             // RFC6585

    /**
     * Route namespace
     *
     * @since 1.0.0
     *
     * @var string
     */
    protected $namespace = 'wemail/v1';

    /**
     * Route base url
     *
     * @since 1.0.0
     *
     * @var string
     */
    protected $rest_base;

    /**
     * Current authenticate user
     *
     * @since 1.0.0
     *
     * @var null|\WP_User
     */
    protected $current_user = null;

    /**
     * Class constructor
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function __construct() {
        $this->register_routes();
    }

    /**
     * Magic call method
     *
     * @since 1.0.0
     *
     * @param  string     $name
     * @param  array      $args
     *
     * @return bool|void
     */
    public function __call( $name, $args ) {
        /**
         * This will handle the weMail permission callback starts with can_.
         * For example 'can_create_campaign'
         */
        if ( strpos( $name, 'can_' ) === 0 ) {
            $this->set_current_user( $args[0] );

            $permission = str_replace( 'can_', '', $name );

            return wemail()->user->can( $permission );
        }
    }

    /**
     * Permission callback to allow the resource open for all
     *
     * @since 1.0.0
     *
     * @param  \WP_REST_Request $request
     *
     * @return bool
     */
    public function public_api( $request ) {
        return true;
    }

    /**
     * Permission callback to check user has manage_option capability
     *
     * @since 1.0.0
     *
     * @param  \WP_REST_Request $request
     *
     * @return bool
     */
    public function manage_options( $request ) {
        return current_user_can( 'manage_options' );
    }

    /**
     * Request response
     *
     * @since 1.0.0
     *
     * @param  mixed $data
     * @param  int   $status
     * @param  array $headers
     *
     * @return \WP_REST_Response
     */
    public function respond( $data = null, $status = self::HTTP_OK, $headers = array() ) {
        return new WP_REST_Response( $data, $status, $headers );
    }

    /**
     * Send error response
     *
     * @since 1.0.0
     *
     * @param string $message
     * @param string $code
     * @param int    $status
     * @param array  $args
     *
     * @return \WP_Error
     */
    public function respond_error( $message = '', $code = '', $status = self::HTTP_UNPROCESSABLE_ENTITY, $args = array() ) {
        $code = ! empty( $code ) ? $code : 'unprocessable_error';
        $data = array( 'status' => $status );

        $data['data'] = ! empty( $args['data'] ) ? $args['data'] : array();

        return new WP_Error( $code, $message, $data );
    }

    /**
     * Register GET route
     *
     * @since 1.0.0
     *
     * @param  string $endpoint
     * @param  string $callback
     * @param  string $permission_cb
     *
     * @return void
     */
    protected function get( $endpoint, $callback, $permission_cb = 'manage_options' ) {
        $this->register_route( 'get', $endpoint, $callback, $permission_cb );
    }

    /**
     * Register POST route
     *
     * @since 1.0.0
     *
     * @param  string $endpoint
     * @param  string $callback
     * @param  string $permission_cb
     *
     * @return void
     */
    protected function post( $endpoint, $callback, $permission_cb = 'manage_options' ) {
        $this->register_route( 'post', $endpoint, $callback, $permission_cb );
    }

    /**
     * Register DELETE route
     *
     * @since 1.0.0
     *
     * @param  string $endpoint
     * @param  string $callback
     * @param  string $permission_cb
     *
     * @return void
     */
    protected function delete( $endpoint, $callback, $permission_cb = 'manage_options' ) {
        $this->register_route( 'delete', $endpoint, $callback, $permission_cb );
    }

    /**
     * Register route endpoints
     *
     * @since 1.0.0
     *
     * @param  string $method
     * @param  string $endpoint
     * @param  string $callback
     * @param  string $permission_cb
     *
     * @return void
     */
    protected function register_route( $method, $endpoint, $callback, $permission_cb ) {
        switch ( $method ) {
            case 'post':
                $methods = WP_REST_Server::CREATABLE;
                break;

            case 'delete':
                $methods = WP_REST_Server::DELETABLE;
                break;

            case 'get':
            default:
                $methods = WP_REST_Server::READABLE;
                break;
        }

        $route = $this->route( $this->rest_base . $endpoint );

        register_rest_route(
            $this->namespace,
            '/' . $route,
            array(
                array(
                    'methods'             => $methods,
                    'callback'            => array( $this, $callback ),
                    'permission_callback' => array( $this, $permission_cb ),
                ),
            )
        );
    }

    /**
     * Set authenticate user as WP User
     *
     * @since 1.0.0
     *
     * @param \WP_REST_Request $request
     *
     * @return bool
     */
    protected function set_current_user( $request ) {
        if ( $this->current_user ) {
            return true;
        }

        $api_key = $request->get_header( 'x-wemail-key' );

        if ( ! empty( $api_key ) ) {
            $wpApiKey = get_option( 'wemail_api_key' );
            if ( $api_key !== $wpApiKey ) {
                return false;
            }

            $userEmail = $request->get_header( 'x-wemail-user' );

            if ( ! empty( $userEmail ) ) {
                $user = get_user_by( 'email', $userEmail );

                if ( $user ) {
                    $this->current_user = wp_set_current_user( $user->ID, $user->user_login );

                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Replace route params with WordPress compatible regex
     *
     * @since 1.0.0
     *
     * @param  string $route
     *
     * @return string
     */
    private function route( $route ) {
        $route       = untrailingslashit( $route );
        $pattern     = '/\{([a-zA-Z0-9_]+?)\}/';
        $replacement = '(?P<${1}>[\w-]+)'; // (?P<param>[\w-]+) string type param

        return preg_replace( $pattern, $replacement, $route );
    }
}
