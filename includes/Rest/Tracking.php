<?php

namespace WeDevs\WeMail\Rest;

use WeDevs\WeMail\RestController;
use WP_REST_Server;

class Tracking extends RestController {

    public $rest_base = '/track';

    public function register_routes() {
        // Custom route registration to support UUID and special characters like colon (:)
        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base . '/(?P<campaignUrlId>[a-zA-Z0-9\:\-]+)/(?P<subscriberId>[a-zA-Z0-9\-]+)',
            array(
                array(
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'redirect_to_tracking' ),
                    'permission_callback' => array( $this, 'public_api' ),
                ),
            )
        );

        // Route without subscriberId (optional parameter)
        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base . '/(?P<campaignUrlId>[a-zA-Z0-9\:\-]+)',
            array(
                array(
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'redirect_to_tracking' ),
                    'permission_callback' => array( $this, 'public_api' ),
                ),
            )
        );
    }

    /**
     * Redirect to Laravel tracking URL
     *
     * @since 2.0.3
     *
     * @param \WP_REST_Request $request
     *
     * @return \WP_REST_Response
     */
    public function redirect_to_tracking( $request ) {
        $accept_header = $request->get_header( 'accept' );
        if ( $request->is_json_content_type() ||
             ( $accept_header && strpos( $accept_header, 'application/json' ) !== false ) ) {
            return $this->respond( array( 'success' => true ) );
        }

        $campaign_url_id = sanitize_text_field( $request->get_param( 'campaignUrlId' ) );
        $subscriber_id = $request->get_param( 'subscriberId' );

        // Get weMail API base URL
        $wemail_api_base = wemail()->wemail_api;
        // remove the 'v1' segment if it exists
        $wemail_api_base = untrailingslashit( preg_replace( '#/v[0-9]+$#', '', $wemail_api_base ) );

        // Build the Laravel tracking URL
        $redirect_url = $wemail_api_base . '/redirect-to/' . $campaign_url_id;

        if ( ! empty( $subscriber_id ) ) {
            $redirect_url .= '/' . sanitize_text_field( $subscriber_id );
        }

        // Get all query parameters
        $query_params = $request->get_query_params();

        // Remove route parameters from query params if they exist
        unset( $query_params['campaignUrlId'] );
        unset( $query_params['subscriberId'] );

        // Append query parameters to redirect URL
        if ( ! empty( $query_params ) ) {
            $redirect_url = add_query_arg( $query_params, $redirect_url );
        }

        // Return redirect response
        return $this->respond(
            array(
                'redirect' => $redirect_url,
            ),
            self::HTTP_FOUND,
            array(
                'Location' => $redirect_url,
            )
        );
    }
}
