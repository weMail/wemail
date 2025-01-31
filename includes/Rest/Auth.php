<?php

namespace WeDevs\WeMail\Rest;

use WeDevs\WeMail\RestController;

class Auth extends RestController {

    /**
     * REST Base
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $rest_base = '/auth';

    /**
     * Register routes
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function register_routes() {
        $this->post( '/site', 'site', 'manage_options' );
        $this->post( '/brand', 'brand', 'manage_options' );
        $this->get( '/validate-me', 'validate_me', 'permission_for_validate_me' );
    }

    /**
     * Authenticate WP Site
     *
     * @return \WP_REST_Response
     *@since 1.0.0
     */
    public function site( $request ) {
        $api_key = $request->get_param( 'api_key' );

        if ( empty( $api_key ) ) {
            return $this->respond(
                array(
					'success' => false,
					'message' => 'API key is required',
                ), self::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $authenticate = wemail()->auth->site( $request );

        if ( is_wp_error( $authenticate ) ) {
            return $authenticate;
        }

        return $this->respond(
            array(
				'success' => true,
				'data' => $authenticate,
            ), self::HTTP_CREATED
        );
    }

    /**
     * Create brand on SaaS and connect this site with the brand
     *
     * @since 1.0.0
     *
     * @return \WP_REST_Response
     */
    public function brand( $request ) {
		//         $key     = $request->get_param( 'api' );
		//         $authenticate = wemail()->auth->site( $key );
        // error_log(print_r($request->get_params(), true));

        $authenticate = wemail()->auth->brand( $request );

        if ( is_wp_error( $authenticate ) ) {
            return $authenticate;
        }

        return $this->respond(
            array(
				'success' => true,
				'data' => $authenticate,
            ), self::HTTP_CREATED
        );
    }

    /**
     * Permission callback for /auth/validate-me endpoint
     *
     * @since 1.0.0
     *
     * @param \WP_REST_Request $requests
     *
     * @return \WP_REST_Response
     */
    public function permission_for_validate_me( $request ) {
        $key = $request->get_header( 'X-WeMail-Key' );

        if ( ! empty( $key ) ) {
            $transient_key = get_transient( 'wemail_validate_me_key' );

            if ( $key === $transient_key ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Validate/ping site callback
     *
     * @since 1.0.0
     *
     * @return \WP_REST_Response
     */
    public function validate_me( $request ) {
        return $this->respond();
    }
}
