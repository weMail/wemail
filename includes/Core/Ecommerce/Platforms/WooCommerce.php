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
    public function products( array $args = array() ) {
        $args = wp_parse_args(
            $args,
            array(
                'limit'    => isset( $args['limit'] ) ? intval( $args['limit'] ) : 50,
                'page'     => isset( $args['page'] ) ? intval( $args['page'] ) : 1,
                'status'   => isset( $args['status'] ) ? $args['status'] : null,
                'paginate' => true,
                'type'     => array_unique( array_merge( array( 'variation' ), array_keys( wc_get_product_types() ) ) ),
            )
        );

        $products = wc_get_products( $args );

        return array(
            'data'         => ProductResource::collection( $products->products ),
            'total'        => $products->total,
            'current_page' => intval( $args['page'] ),
            'total_page'   => $products->max_num_pages,
        );
    }

    /**
     * Get orders from WooCommerce store
     *
     * @param array $args
     *
     * @return array
     */
    public function orders( array $args = array() ) {
        $args = wp_parse_args(
            $args,
            array(
                'limit'    => isset( $args['limit'] ) ? intval( $args['limit'] ) : 50,
                'page'     => isset( $args['page'] ) ? intval( $args['page'] ) : 1,
                'paginate' => true,
                'status'   => array( 'completed', 'refunded', 'on-hold', 'processing', 'cancelled', 'failed' ),
                'type'     => array( 'shop_order', 'shop_order_refund' ),
            )
        );

        if ( isset( $args['after_updated'] ) ) {
            $args['date_modified'] = '>=' . $args['after_updated'];
            unset( $args['after_updated'] );
        }

        $data = wc_get_orders( $args );

        return array(
            'data'         => OrderResource::collection( $data->orders ),
            'total'        => $data->total,
            'current_page' => intval( $args['page'] ),
            'total_page'   => $data->max_num_pages,
        );
    }

    /**
     * Register post update hooks
     */
    public function register_hooks() {
        // New order created hook
        add_action( 'woocommerce_new_order', array( $this, 'handle_new_order' ), 10, 2 );

        // Order status changed hook (handles pending payment, completed, etc.)
        add_action( 'woocommerce_order_status_changed', array( $this, 'handle_order_status_changed' ), 10, 4 );

        // Pending payment status specific hook
        add_action( 'woocommerce_order_status_pending', array( $this, 'handle_pending_payment' ), 10, 1 );

        add_action( 'woocommerce_order_refunded', array( $this, 'create_order_refund' ), 10, 2 );
        add_action( 'woocommerce_refund_deleted', array( $this, 'delete_order_refund' ), 10, 2 );
        add_action( 'after_delete_post', array( $this, 'delete' ), 10, 2 );
        add_action( 'woocommerce_update_product', array( $this, 'handle_update_product' ), 10, 2 );
        add_action( 'woocommerce_new_product', array( $this, 'handle_product' ), 10, 2 );
        add_action( 'woocommerce_new_product_variation', array( $this, 'handle_product_variation' ) );
        add_action( 'woocommerce_update_product_variation', array( $this, 'handle_product_variation' ) );
        add_action( 'woocommerce_delete_product_variation', array( $this, 'delete_product_variation' ) );
        add_action( 'created_product_cat', array( $this, 'handle_category' ), 10, 2 );
        add_action( 'edited_product_cat', array( $this, 'handle_category' ), 10, 2 );
        add_action( 'delete_product_cat', array( $this, 'handle_category_delete' ), 10, 3 );
    }

    /**
     * Handle pending payment status
     *
     * @param int $order_id Order ID
     */
    public function handle_pending_payment( $order_id ) {
        $order = wc_get_order( $order_id );

        if ( ! $order ) {
            return;
        }

        if ( ! $this->is_valid_order_item( $order->get_type() ) ) {
            return;
        }

        if ( ! Settings::instance()->is_enabled() ) {
            return;
        }

        $payload = OrderResource::single( $order );

        wemail()->api
            ->send_json()
            ->ecommerce()
            ->orders( $order_id )
            ->put( $payload );
    }

    /**
     * Handle new order created
     *
     * @param int $order_id Order ID
     * @param \WC_Order $order Order object
     */
    public function handle_new_order( $order_id, $order ) {
        $this->process_order( $order_id, $order );
    }

    /**
     * Handle order status changed
     *
     * @param int $order_id Order ID
     * @param string $status_from From status
     * @param string $status_to To status
     * @param \WC_Order $order Order object
     */
    public function handle_order_status_changed( $order_id, $status_from, $status_to, $order ) {
        $this->process_order( $order_id, $order );
    }

    /**
     * Process order - shared logic for new order and status change
     *
     * @param int $order_id Order ID
     * @param \WC_Order $order Order object
     */
    private function process_order( $order_id, $order ) {
        if ( ! $order ) {
            $order = wc_get_order( $order_id );
        }

        if ( ! $order ) {
            return;
        }

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

        $this->productPostRequest( $product, $id );
    }

    public function handle_update_product( $id, $product ) {
        if ( ! Settings::instance()->is_integrated() ) {
            return;
        }

        $this->handle_product( $id, $product );

        $variations = $product->get_children();
        $variants = array();

        if ( $product->is_type( 'variable' ) && ! empty( $variations ) ) {
            foreach ( $variations as $variation ) {
                $variants[] = wc_get_product( $variation );
            }

            $variant_products = ProductResource::collection( $variants );

            $data['data'] = array_values( $variant_products );
            wemail()->api
                ->send_json()
                ->ecommerce()
                ->products( $id )
                ->variant()
                ->put( $data );
        }
    }

    public function handle_product_variation( $id ) {
        if ( ! Settings::instance()->is_integrated() ) {
            return;
        }

        $product = wc_get_product( $id );

        $this->productPostRequest( $product, $id );
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
                array(
                    '_method' => 'delete',
                )
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
                    array(
                        '_method' => 'delete',
                    )
                );
        }

        // Delete order
        if ( $this->is_valid_order_item( $post->post_type ) ) {
            wemail()->api
                ->ecommerce()
                ->orders( $post_id )
                ->post(
                    array(
                        '_method' => 'delete',
                    )
                );
        }
    }

    public function delete_product_variation( $variation_id ) {
        wemail()->api
            ->ecommerce()
            ->products( $variation_id )
            ->post(
                array(
                    '_method' => 'delete',
                )
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
    public function categories( array $args = array() ) {
        $terms = get_terms(
            array(
                'taxonomy'   => 'product_cat',
                'hide_empty' => false,
            )
        );

        return array(
            'data' => CategoryResource::collection( $terms ),
        );
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
    public function productPostRequest( $product, $id ) {
        $payload = ProductResource::single( $product );

        wemail()->api
            ->send_json()
            ->ecommerce()
            ->products( $id )
            ->put( $payload );
    }

    /**
     * Handle category create and update event
     *
     * @param int $term_id Category term ID
     * @param int $tt_id Term taxonomy ID
     *
     * @return void
     */
    public function handle_category( $term_id, $tt_id = null ) {
        if ( ! Settings::instance()->is_integrated() ) {
            return;
        }

        $term = get_term( $term_id, 'product_cat' );

        if ( ! $term || is_wp_error( $term ) ) {
            return;
        }

        $payload = CategoryResource::single( $term );

        wemail()->api
            ->send_json()
            ->ecommerce()
            ->categories( $term_id )
            ->put( $payload );
    }

    /**
     * Handle category delete event
     *
     * @param int $term_id Category term ID
     * @param int $tt_id Term taxonomy ID
     * @param object $deleted_term Deleted term object
     *
     * @return void
     */
    public function handle_category_delete( $term_id, $tt_id, $deleted_term ) {
        if ( ! Settings::instance()->is_integrated() ) {
            return;
        }

        wemail()->api
            ->ecommerce()
            ->categories( $term_id )
            ->delete();
    }
}
