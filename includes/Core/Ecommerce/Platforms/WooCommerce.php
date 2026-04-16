<?php

namespace WeDevs\WeMail\Core\Ecommerce\Platforms;

use WeDevs\WeMail\Core\Ecommerce\Settings;
use WeDevs\WeMail\Core\Sync\Ecommerce\RevenueTrack;
use WeDevs\WeMail\Rest\Resources\Ecommerce\WooCommerce\CartResource;
use WeDevs\WeMail\Rest\Resources\Ecommerce\WooCommerce\CategoryResource;
use WeDevs\WeMail\Rest\Resources\Ecommerce\WooCommerce\OrderResource;
use WeDevs\WeMail\Rest\Resources\Ecommerce\WooCommerce\ProductResource;
use WeDevs\WeMail\Rest\Resources\Ecommerce\WooCommerce\SubscriptionResource;
use WeDevs\WeMail\Traits\Singleton;
use WP_Post;

class WooCommerce extends AbstractPlatform {
    use Singleton;

    /**
     * Flag to prevent recursive hook firing during cart recovery
     *
     * @var bool
     */
    private $is_recovering = false;

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
        // Cart recovery hook
        add_action( 'template_redirect', array( $this, 'handle_cart_recovery' ) );

        // Cart hooks
        add_action( 'woocommerce_add_to_cart', array( $this, 'handle_add_to_cart' ), 10, 6 );
        add_action( 'woocommerce_cart_updated', array( $this, 'handle_cart_updated' ), 10, 0 );
        add_action( 'woocommerce_remove_cart_item', array( $this, 'handle_remove_cart_item' ), 10, 2 );

        // New order created hook
        add_action( 'woocommerce_new_order', array( $this, 'handle_new_order' ), 10, 2 );

        // Order status changed hook (handles pending payment, completed, etc.)
        add_action( 'woocommerce_order_status_changed', array( $this, 'handle_order_status_changed' ), 10, 4 );

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

        // WooCommerce Subscriptions hooks
        add_action( 'woocommerce_new_subscription', array( $this, 'handle_new_subscription' ), 10, 1 );
        add_action( 'woocommerce_subscription_status_cancelled', array( $this, 'handle_subscription_cancelled' ), 10, 1 );
        add_action( 'woocommerce_subscription_status_expired', array( $this, 'handle_subscription_expired' ), 10, 1 );
        add_action( 'woocommerce_subscription_renewal_payment_complete', array( $this, 'handle_subscription_renewal_payment_complete' ), 10, 2 );
    }

    /**
     * Handle add to cart event
     *
     * @param string $cart_item_key Cart item key
     * @param int $product_id Product ID
     * @param int $quantity Quantity
     * @param int $variation_id Variation ID
     * @param array $variation Variation data
     * @param array $cart_item_data Cart item data
     */
    public function handle_add_to_cart( $cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data ) {
        if ( $this->is_recovering || ! Settings::instance()->is_enabled() ) {
            return;
        }

        $this->send_cart_data( 'add_to_cart' );
    }

    /**
     * Handle cart updated event
     */
    public function handle_cart_updated() {
        if ( $this->is_recovering || ! Settings::instance()->is_enabled() ) {
            return;
        }

        $this->send_cart_data( 'cart_updated' );
    }

    /**
     * Handle remove cart item event
     *
     * @param string $cart_item_key Cart item key
     * @param \WC_Cart $cart Cart object
     */
    public function handle_remove_cart_item( $cart_item_key, $cart ) {
        if ( $this->is_recovering || ! Settings::instance()->is_enabled() ) {
            return;
        }

        $this->send_cart_data( 'remove_cart_item' );
    }

    /**
     * Handle cart recovery from abandoned cart email link
     */
    public function handle_cart_recovery() {
        if ( ! isset( $_GET['wemail-recover-cart'] ) ) {
            return;
        }

        $token = sanitize_text_field( wp_unslash( $_GET['wemail-recover-cart'] ) );

        if ( empty( $token ) ) {
            return;
        }

        if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
            return;
        }

        $coupon_code = isset( $_GET['coupon'] ) ? sanitize_text_field( wp_unslash( $_GET['coupon'] ) ) : '';

        $response = wemail()->api
            ->ecommerce()
            ->abandoned_carts()
            ->recover()
            ->query( array( 'token' => $token ) )
            ->get();

        if ( is_wp_error( $response ) || empty( $response['data']['items'] ) ) {
            wp_safe_redirect( wc_get_checkout_url() );
            exit;
        }

        $this->is_recovering = true;

        WC()->cart->empty_cart();

        $items = $response['data']['items'];

        foreach ( $items as $item ) {
            $product_id     = isset( $item['product_id'] ) ? absint( $item['product_id'] ) : 0;
            $quantity       = isset( $item['quantity'] ) ? absint( $item['quantity'] ) : 1;
            $variation_id   = isset( $item['variation_id'] ) ? absint( $item['variation_id'] ) : 0;
            $variation_attr = isset( $item['variation'] ) ? (array) $item['variation'] : array();

            if ( $product_id ) {
                try {
                    WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variation_attr );
                } catch ( \Exception $e ) {
                    error_log( 'weMail cart recovery: failed to add product ' . $product_id . ' - ' . $e->getMessage() );
                }
            }
        }

        if ( ! empty( $coupon_code ) && wc_get_coupon_id_by_code( $coupon_code ) ) {
            WC()->cart->apply_coupon( $coupon_code );
        }

        WC()->cart->calculate_totals();

        $this->is_recovering = false;

        wp_safe_redirect( wc_get_checkout_url() );
        exit;
    }

    /**
     * Get or generate cart key
     *
     * @return string Cart key
     */
    private function get_cart_key() {
        $cart_key = WC()->session->get( 'wem_cart_key' );

        if ( ! $cart_key ) {
            $cart_key = wp_generate_uuid4();
            WC()->session->set( 'wem_cart_key', $cart_key );
        }

        return $cart_key;
    }

    /**
     * Reset cart key for current session.
     *
     * @return void
     */
    private function reset_cart_key() {
        if ( function_exists( 'WC' ) && WC()->session ) {
            WC()->session->__unset( 'wem_cart_key' );
        }
    }

    /**
     * Send cart data to weMail API
     *
     * @param string $event Event type
     */
    private function send_cart_data( $event ) {
        if ( ! WC()->session || ! WC()->session->get_customer_id() ) {
            return;
        }

        $cart = WC()->cart;

        if ( ! $cart ) {
            return;
        }

        // Don't send if cart is empty (except for cart_emptied event)
        if ( $event !== 'cart_emptied' && $cart->is_empty() ) {
            return;
        }

        $cart_key = $this->get_cart_key();
        $payload = CartResource::with_customer( $cart, $cart_key );
        $payload['event'] = $event;
        $payload['session_id'] = WC()->session->get_customer_id();

        wemail()->api
            ->send_json()
            ->ecommerce()
            ->carts()
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

        $this->reset_cart_key();
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
     * Check if WooCommerce Subscriptions is active
     *
     * @return bool
     */
    public function is_subscriptions_active() {
        return class_exists( 'WC_Subscription' );
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

    /**
     * Handle new subscription created
     *
     * @param \WC_Subscription $subscription Subscription object
     */
    public function handle_new_subscription( $subscription ) {
        if ( is_numeric( $subscription ) ) {
            $subscription = wcs_get_subscription( $subscription );
        }

        if ( ! $subscription ) {
            return;
        }

        $this->send_subscription_data( $subscription, 'subscription_created' );
    }

    /**
     * Handle subscription status change
     *
     * @param \WC_Subscription $subscription Subscription object
     * @param string $new_status New status
     * @param string $old_status Old status
     */
    /**
     * Handle subscription cancelled
     *
     * @param \WC_Subscription $subscription Subscription object
     */
    public function handle_subscription_cancelled( $subscription ) {
        $this->send_subscription_data( $subscription, 'subscription_cancelled' );
    }

    /**
     * Handle subscription expired
     *
     * @param \WC_Subscription $subscription Subscription object
     */
    public function handle_subscription_expired( $subscription ) {
        $this->send_subscription_data( $subscription, 'subscription_expired' );
    }

    /**
     * Handle subscription renewal payment complete
     *
     * @param \WC_Subscription $subscription Subscription object
     * @param \WC_Order $last_order Last renewal order
     */
    public function handle_subscription_renewal_payment_complete( $subscription, $last_order ) {
        $this->send_subscription_data( $subscription, 'subscription_renewal_payment_complete', array(
            'renewal_complete' => true,
        ) );
    }
    /**
     * Send subscription data to weMail API
     *
     * @param \WC_Subscription $subscription Subscription object
     * @param string $event Event type
     * @param array $extra Additional payload data
     */
    private function send_subscription_data( $subscription, $event, $extra = array() ) {
        if ( ! $subscription ) {
            return;
        }

        if ( ! Settings::instance()->is_enabled() ) {
            return;
        }

        $payload          = SubscriptionResource::single( $subscription );
        $payload['event'] = $event;
        $payload          = array_merge( $payload, $extra );


        wemail()->api
            ->send_json()
            ->ecommerce()
            ->subscriptions( $subscription->get_id() )
            ->put( $payload );
    }
}
