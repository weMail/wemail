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
            $query = new WP_User_Query(
                [
                    'fields'        => 'ID',
                    'meta_key'      => 'wemail_api_key',
                    'meta_value'    => $api_key,
                ]
            );

            if ( $query->get_total() ) {
                $results = $query->get_results();
                $user_id = array_pop( $results );

                wp_set_current_user( $user_id );

                return wemail()->user->can( 'manage_settings' );
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
