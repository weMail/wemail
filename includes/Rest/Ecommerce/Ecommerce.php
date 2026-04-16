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
        $this->get( '/(?P<source>woocommerce)/status', 'status', 'permission' );
        $this->post( '/coupons', 'create_coupon', 'permission' );
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
				'currency'              => $platform->currency(),
				'currency_symbol'       => $platform->currency_symbol(),
				'platform'              => $platform->get_name(),
				'subscriptions_enabled' => $platform->is_subscriptions_active(),
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
     * Platform status endpoint
     *
     * @param \WP_REST_Request $request
     */
    public function status( $request ) {
        $platform = $request->get_param( 'source' );

        /** @var PlatformInterface $platform */
        $platform = wemail()->ecommerce->platform( $platform );

        return new \WP_REST_Response(
            array(
                'is_active'             => $platform->is_active(),
                'subscriptions_enabled' => $platform->is_subscriptions_active(),
            )
        );
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
     * Create a WooCommerce coupon
     *
     * @param \WP_REST_Request $request
     *
     * @return \WP_REST_Response|\WP_Error
     */
    public function create_coupon( $request ) {
        if ( ! function_exists( 'WC' ) ) {
            return $this->respond_error( 'WooCommerce is not active.', 'woocommerce_inactive', self::HTTP_UNPROCESSABLE_ENTITY );
        }

        $data = $request->get_json_params();

        if ( empty( $data['code'] ) ) {
            return $this->respond_error( 'Coupon code is required.', 'missing_coupon_code', self::HTTP_UNPROCESSABLE_ENTITY );
        }

        $existing = wc_get_coupon_id_by_code( $data['code'] );

        if ( $existing ) {
            return $this->respond_error( 'Coupon code already exists.', 'coupon_exists', self::HTTP_CONFLICT );
        }

        $coupon = new \WC_Coupon();

        $coupon->set_code( sanitize_text_field( $data['code'] ) );
        $coupon->set_discount_type( isset( $data['discount_type'] ) ? sanitize_text_field( $data['discount_type'] ) : 'percent' );
        $coupon->set_amount( isset( $data['amount'] ) ? floatval( $data['amount'] ) : 0 );
        $coupon->set_usage_limit( isset( $data['usage_limit'] ) ? absint( $data['usage_limit'] ) : 1 );
        $coupon->set_individual_use( ! empty( $data['individual_use'] ) );
        $coupon->set_free_shipping( ! empty( $data['free_shipping'] ) );

        if ( ! empty( $data['date_expires'] ) ) {
            $coupon->set_date_expires( $data['date_expires'] );
        }

        if ( ! empty( $data['email_restrictions'] ) && is_array( $data['email_restrictions'] ) ) {
            $coupon->set_email_restrictions( array_map( 'sanitize_email', $data['email_restrictions'] ) );
        }

        $coupon->save();

        return $this->respond(
            array(
                'id'   => $coupon->get_id(),
                'code' => $coupon->get_code(),
            ),
            self::HTTP_CREATED
        );
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
