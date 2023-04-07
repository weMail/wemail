<?php

namespace WeDevs\WeMail\Core\Ecommerce\Platforms;

use WeDevs\WeMail\Core\Ecommerce\Settings;
use WeDevs\WeMail\Core\Sync\Ecommerce\RevenueTrack;
use WeDevs\WeMail\Rest\Resources\Ecommerce\WooCommerce\CategoryResource;
use WeDevs\WeMail\Rest\Resources\Ecommerce\WooCommerce\OrderResource;
use WeDevs\WeMail\Rest\Resources\Ecommerce\WooCommerce\ProductResource;
use WeDevs\WeMail\Traits\Singleton;
use WP_Post;

class WooCommerce extends AbstractPlatform {
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
                'limit'    => isset( $args['limit'] ) ? intval( $args['limit'] ) : 50,
                'page'     => isset( $args['page'] ) ? intval( $args['page'] ) : 1,
                'status'   => isset( $args['status'] ) ? $args['status'] : null,
                'paginate' => true,
                'type'     => array_unique( array_merge( [ 'variation' ], array_keys( wc_get_product_types() ) ) ),
            ]
        );

        $products = wc_get_products( $args );

        return [
            'data'         => ProductResource::collection( $products->products ),
            'total'        => $products->total,
            'current_page' => intval( $args['page'] ),
            'total_page'   => $products->max_num_pages,
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
                'limit'    => isset( $args['limit'] ) ? intval( $args['limit'] ) : 50,
                'page'     => isset( $args['page'] ) ? intval( $args['page'] ) : 1,
                'paginate' => true,
                'status'   => [ 'completed', 'refunded', 'on-hold', 'processing', 'cancelled', 'failed' ],
                'type'     => [ 'shop_order', 'shop_order_refund' ],
            ]
        );

        if ( isset( $args['after_updated'] ) ) {
            $args['date_modified'] = '>=' . $args['after_updated'];
            unset( $args['after_updated'] );
        }

        $data = wc_get_orders( $args );

        return [
            'data'         => OrderResource::collection( $data->orders ),
            'total'        => $data->total,
            'current_page' => intval( $args['page'] ),
            'total_page'   => $data->max_num_pages,
        ];
    }

    /**
     * Register post update hooks
     */
    public function register_hooks() {
        add_action( 'woocommerce_order_status_changed', [ $this, 'handle_order' ], 10, 4 );
        add_action( 'woocommerce_order_refunded', [ $this, 'create_order_refund' ], 10, 2 );
        add_action( 'woocommerce_refund_deleted', [ $this, 'delete_order_refund' ], 10, 2 );
        add_action( 'after_delete_post', [ $this, 'delete' ], 10, 2 );
        add_action( 'woocommerce_update_product', [ $this, 'handle_update_product' ], 10, 2 );
        add_action( 'woocommerce_new_product', [ $this, 'handle_product' ], 10, 2 );
        add_action( 'woocommerce_new_product_variation', [ $this, 'handle_product_variation' ] );
        add_action( 'woocommerce_update_product_variation', [ $this, 'handle_product_variation' ] );
        add_action( 'woocommerce_delete_product_variation', [ $this, 'delete_product_variation' ] );
    }

    /**
     * Handling order
     *
     * @param $order_id
     * @param $status_from
     * @param $status_to
     * @param $order \WC_Order
     */
    public function handle_order( $order_id, $status_from, $status_to, $order ) {
        if ( ! $this->is_valid_order_item( $order->get_type() ) ) {
            return;
        }

        if ( ! Settings::instance()->is_enabled() ) {
            return;
        }

        $payload = OrderResource::single( $order );

        RevenueTrack::track_id( $payload );

        wemail()->api
            ->send_json()
            ->ecommerce()
            ->orders( $order_id )
            ->put( $payload );
    }

    /**
     * Handle product create and update event
     *
     * @param $id
     * @param $product
     *
     * @return void
     */
    public function handle_product( $id, $product ) {
        if ( ! Settings::instance()->is_integrated() ) {
            return;
        }

        $this->productPostRequest($product, $id);
    }

    public function handle_update_product($id, $product ) {
        if ( ! Settings::instance()->is_integrated() ) {
            return;
        }

        $this->handle_product($id, $product);

        $variations = $product->get_children();
        $products = [];

        if ($product->is_type('variable') && ! empty($variations)) {
            foreach ($variations as $variation) {
                $products[] = wc_get_product($variation);
            }

            $variantProducts = ProductResource::collection($products);

            $data['data'] = array_values($variantProducts);
            wemail()->api
                ->send_json()
                ->ecommerce()
                ->products($id)
                ->variant()
                ->put($data);
        }
    }

    public function handle_product_variation( $id ) {
        if ( ! Settings::instance()->is_integrated() ) {
            return;
        }

        $product = wc_get_product( $id );

        $this->productPostRequest($product, $id);
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

        if ( ! $this->is_valid_order_item( $order->get_type() ) ) {
            return;
        }

        $payload = OrderResource::single( $order );

        wemail()->api
            ->send_json()
            ->ecommerce()
            ->orders( $order_id )
            ->refunds( $refund_id )
            ->put( $payload );
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
     * @param $post_id
     * @param WP_Post $post
     */
    public function delete( $post_id, WP_Post $post ) {
        if ( ! $this->is_valid_order_item( $post->post_type ) && $post->post_type !== 'product' ) {
            return;
        }

        if ( ! Settings::instance()->is_integrated() ) {
            return;
        }

        // Delete product
        if ( $post->post_type === 'product' ) {
            $res = wemail()->api
                ->ecommerce()
                ->products( $post_id )
                ->post(
                    [
                        '_method' => 'delete',
                    ]
                );
        }

        // Delete order
        if ( $this->is_valid_order_item( $post->post_type ) ) {
            wemail()->api
                ->ecommerce()
                ->orders( $post_id )
                ->post(
                    [
                        '_method' => 'delete',
                    ]
                );
        }
    }

    public function delete_product_variation( $variation_id ) {
        wemail()->api
            ->ecommerce()
            ->products( $variation_id )
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

    /**
     * Get integration name
     *
     * @return string
     */
    public function get_name() {
        return 'woocommerce';
    }

    /**
     * Get WooCommerce categories
     *
     * @param array $args
     *
     * @return array
     */
    public function categories( array $args = [] ) {
        $terms = get_terms(
            [
                'taxonomy'   => 'product_cat',
                'hide_empty' => false,
            ]
        );

        return [
            'data' => CategoryResource::collection( $terms ),
        ];
    }

    /**
     * Check if it is valid order item
     *
     * @param $type
     *
     * @return bool|false
     */
    protected function is_valid_order_item( $type ) {
        return (bool) preg_match( '/^shop_order(_refund)?$/', $type );
    }

    /**
     * @param $product
     * @param $id
     * @return void
     */
    public function productPostRequest($product, $id)
    {
        $payload = ProductResource::single($product);

        wemail()->api
            ->send_json()
            ->ecommerce()
            ->products($id)
            ->put($payload);
    }
}
