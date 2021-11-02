<?php

namespace WeDevs\WeMail\Core\Ecommerce\Platforms;

use WeDevs\WeMail\Traits\Singleton;
use WeDevs\WeMail\Core\Ecommerce\Settings;
use WeDevs\WeMail\Rest\Resources\Ecommerce\WooCommerce\OrderResource;
use WeDevs\WeMail\Rest\Resources\Ecommerce\WooCommerce\ProductResource;

class WooCommerce implements PlatformInterface {
    use Singleton;

    /**
     * Get currency
     *
     * @return string
     */
    public function currency() {
        return get_woocommerce_currency();
    }

    /**
     * Get store currency symbol
     *
     * @return string
     */
    public function currency_symbol() {
        return get_woocommerce_currency_symbol();
    }

    /**
     * Get products from WooCommerce store
     *
     * @param array $args
     *
     * @return array
     */
    public function products( array $args = [] ) {
        $args = wp_parse_args(
            $args,
            [
                'limit'     => isset( $args['limit'] ) ? intval( $args['limit'] ) : 50,
                'page'      => isset( $args['page'] ) ? intval( $args['page'] ) : 1,
                'status'    => isset( $args['status'] ) ? $args['status'] : null,
                'paginate'  => true,
            ]
        );

        $products = wc_get_products( $args );

        return [
            'data'          => ProductResource::collection( $products->products ),
            'total'         => $products->total,
            'current_page'  => intval( $args['page'] ),
            'total_page'    => $products->max_num_pages,
        ];
    }

    /**
     * Get orders from WooCommerce store
     *
     * @param array $args
     *
     * @return array
     */
    public function orders( array $args = [] ) {
        $args = wp_parse_args(
            $args,
            [
                'limit'         => isset( $args['limit'] ) ? intval( $args['limit'] ) : 50,
                'page'          => isset( $args['page'] ) ? intval( $args['page'] ) : 1,
                'paginate'      => true,
                'status'        => [ 'completed', 'refunded', 'on-hold', 'processing', 'cancelled', 'failed' ],
            ]
        );

        if ( isset( $args['after_updated'] ) ) {
            $args['date_modified'] = '>=' . $args['after_updated'];
            unset( $args['after_updated'] );
        }

        $data = wc_get_orders( $args );

        return [
            'data'          => OrderResource::collection( $data->orders ),
            'total'         => $data->total,
            'current_page'  => intval( $args['page'] ),
            'total_page'    => $data->max_num_pages,
        ];
    }

    public function customers() {
    }

    /**
     * Register post update hooks
     */
    public function register_hooks() {
        add_action( 'woocommerce_order_status_changed', [ $this, 'handle_order' ], 10, 4 );
        add_action( 'woocommerce_order_refunded', [ $this, 'create_order_refund' ], 10, 2 );
        add_action( 'woocommerce_refund_deleted', [ $this, 'delete_order_refund' ], 10, 2 );
        add_action( 'after_delete_post', [ $this, 'delete_order' ], 10, 2 );
    }

    /**
     * Handling order
     *
     * @param $order_id
     * @param $status_from
     * @param $status_to
     * @param $order
     */
    public function handle_order( $order_id, $status_from, $status_to, $order ) {
        if ( ! Settings::instance()->is_enabled() ) {
            return;
        }

        wemail()->api
            ->send_json()
            ->ecommerce()
            ->orders( $order_id )
            ->put( OrderResource::single( $order ) );
    }

    /**
     * Create a new refund
     *
     * @param $order_id
     * @param $refund_id
     */
    public function create_order_refund( $order_id, $refund_id ) {
        if ( ! Settings::instance()->is_enabled() ) {
            return;
        }

        $order = wc_get_order( $refund_id );

        wemail()->api
            ->send_json()
            ->ecommerce()
            ->orders( $order_id )
            ->refunds( $refund_id )
            ->put( OrderResource::single( $order ) );
    }

    /**
     * Delete order refund
     *
     * @param $refund_id
     * @param $order_id
     */
    public function delete_order_refund( $refund_id, $order_id ) {
        if ( ! Settings::instance()->is_integrated() ) {
            return;
        }

        wemail()->api
            ->ecommerce()
            ->orders( $order_id )
            ->refunds( $refund_id )
            ->post(
                [
					'_method' => 'delete',
				]
            );
    }

    /**
     * Delete order
     *
     * @param $order_id
     * @param \WP_Post $post
     */
    public function delete_order( $order_id, \WP_Post $post ) {
        if ( $post->post_type !== 'shop_order' ) {
            return;
        }

        if ( ! Settings::instance()->is_integrated() ) {
            return;
        }

        wemail()->api
            ->ecommerce()
            ->orders( $order_id )
            ->post(
                [
					'_method' => 'delete',
				]
            );
    }

    /**
     * Is WooCommerce active or not
     *
     *
     * @return bool
     */
    public function is_active() {
        return class_exists( 'WooCommerce' );
    }

    public function get_name() {
        return 'woocommerce';
    }
}
