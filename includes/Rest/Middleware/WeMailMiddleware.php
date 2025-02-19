<?php

namespace WeDevs\WeMail\Rest\Middleware;

use WP_User_Query;

class WeMailMiddleware {

    protected $permission;

    /**
     * WeMail constructor.
     * @param $permission
     */
    public function __construct( $permission ) {
        $this->permission = $permission;
    }

    /**
     * @param $request
     * @return bool
     */
    public function handle( $request ) {
        $api_key = $request->get_header( 'X-WeMail-Key' );

        if ( ! empty( $api_key ) ) {
            $wemail_api_key = get_option( 'wemail_api_key' );
            if ( $api_key === $wemail_api_key ) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $permission
     * @param \WP_REST_Request $request
     * @return bool
     */
    public static function check( $permission, $request ) {
        return ( new self( $permission ) )->handle( $request );
    }
}
