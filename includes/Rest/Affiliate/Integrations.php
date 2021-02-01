<?php

namespace WeDevs\WeMail\Rest\Affiliate;

use WeDevs\WeMail\RestController;
use WP_REST_Server;

class Integrations extends RestController {

    public $rest_base = '/affiliates';

    public function register_routes() {
        $this->get( '/settings/affiliate-wp', 'getAffiliateWpSettings', 'manage_options' );
        $this->post( '/settings/affiliate-wp', 'postAffiliateWpSettings', 'manage_options' );
    }

    /**
     * @param $request
     * @return bool
     */
    public function permission( $request ) {
    }

    /**
     * Get affiliatewp settings
     * @return \WP_Error|\WP_HTTP_Response|\WP_REST_Response
     */
    public function getAffiliateWpSettings() {
        $enabled = get_option( 'wemail_affiliatewp_enabled', false );
        $rest_url = rest_url( '', 'json' );
        return rest_ensure_response(
            [
                'data' => [
                    'enabled' => $enabled,
                    'rest_url' => $rest_url,
                ],
            ]
        );
    }

    /**
     * Post affiliatewp settings
     * @return \WP_Error|\WP_HTTP_Response|\WP_REST_Response
     */
    public function postAffiliateWpSettings( $request ) {
        $checked = $request->get_param( 'checked' );
        update_option( 'wemail_affiliatewp_enabled', $checked );
        return rest_ensure_response(
            [
                'data' => [
                    'checked'  => $checked,
                ],
            ]
        );
    }
}
