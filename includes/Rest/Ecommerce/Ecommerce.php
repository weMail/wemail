<?php

namespace WeDevs\WeMail\Rest\Ecommerce;

use WeDevs\WeMail\RestController;
use WeDevs\WeMail\Core\Ecommerce\Settings;
use WeDevs\WeMail\Rest\Middleware\WeMailMiddleware;
use WeDevs\WeMail\Core\Ecommerce\Platforms\PlatformInterface;

class Ecommerce extends RestController {

    /**
     * @inheritdoc
     */
    protected $rest_base = 'ecommerce';

    /**
     * @inheritdoc
     */
    public function register_routes() {
        $this->post( '/(?P<source>woocommerce|edd)/settings', 'settings' );
        $this->get( '/(?P<source>woocommerce|edd)/(?P<resource>products|orders|categories)', 'resource', 'permission' );
        $this->delete( '/disconnect', 'disconnect', 'permission' );
    }

    /**
     * Settings endpoint
     *
     * @param \WP_REST_Request $request
     */
    public function settings( $request ) {
        $body = $request->get_json_params();
        $platform = $request->get_param( 'source' );

        /** @var PlatformInterface $platform */
        $platform = wemail()->ecommerce->platform( $platform );

        $body = array_merge(
            $body, array(
				'currency' => $platform->currency(),
				'currency_symbol' => $platform->currency_symbol(),
				'platform' => $platform->get_name(),
			)
        );
        // Update settings to wp options
        Settings::instance()->update( $body );

        $response = wemail()->api
            ->send_json()
            ->ecommerce()
            ->settings()
            ->post( $body );

        if ( is_wp_error( $response ) ) {
            return $response;
        }

        return new \WP_REST_Response( $response );
    }

    /**
     * Access products, orders or customers resource
     *
     * @param \WP_REST_Request $request
     */
    public function resource( $request ) {
        $platform = $request->get_param( 'source' );
        $resource = $request->get_param( 'resource' );

        $query = $request->get_query_params();

        /** @var PlatformInterface $platform */
        $platform = wemail()->ecommerce->platform( $platform );

        if ( ! $platform->is_active() ) {
            return new \WP_REST_Response(
                array(
					'message' => sprintf( '%s plugin is not active', $platform->get_name() ),
				), 422
            );
        }

        $data = $platform->{$resource}( $query );

        return new \WP_REST_Response( $data );
    }

    /**
     * Disconnect action
     *
     * @return \WP_REST_Response
     */
    public function disconnect() {
        // Delete option from WP
        Settings::instance()->delete();

        $response = wemail()->api->ecommerce()->disconnect()->post(
            array(
				'_method' => 'delete',
			)
        );

        if ( is_wp_error( $response ) ) {
            return new \WP_REST_Response( $response, 422 );
        }

        return new \WP_REST_Response( $response );
    }

    /**
     * Rest permission
     *
     * @param \WP_REST_Request $request
     *
     * @return bool
     */
    public function permission( $request ) {
        return $this->manage_options( $request ) || WeMailMiddleware::check( 'manage_settings', $request );
    }

    /**
     * Overwrite manage_options
     *
     * @param \WP_REST_Request $request
     * @return bool
     */
    public function manage_options( $request ) {
        return parent::manage_options( $request ) || WeMailMiddleware::check( 'manage_settings', $request );
    }
}
