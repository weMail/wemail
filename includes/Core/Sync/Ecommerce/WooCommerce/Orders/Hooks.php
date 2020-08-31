<?php

namespace WeDevs\WeMail\Core\Sync\Ecommerce\WooCommerce\Orders;

use WeDevs\WeMail\Core\Ecommerce\Requests\Orders;
use WeDevs\WeMail\Traits\Hooker;

class Hooks {

    use Hooker;

    protected $orderRequest;

    protected $source = 'woocommerce';
    /**
     * Class constructor
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function __construct() {
        $this->add_action( 'woocommerce_thankyou', 'wemail_wc_order_received' );
        $this->add_action( 'woocommerce_order_status_completed', 'wemail_wc_order_status_updated');
        $this->add_action( 'woocommerce_order_status_refunded', 'wemail_wc_order_status_updated');
        $this->add_action( 'woocommerce_order_status_pending', 'wemail_wc_order_status_updated');
        $this->add_action( 'woocommerce_order_status_failed', 'wemail_wc_order_status_updated');
        $this->add_action( 'woocommerce_order_status_on-hold', 'wemail_wc_order_status_updated');
        $this->add_action( 'woocommerce_order_status_processing', 'wemail_wc_order_status_updated');
        $this->add_action( 'woocommerce_order_status_cancelled', 'wemail_wc_order_status_updated');

        $this->orderRequest = new Orders();
    }

    /**
     * Sync new customer
     *
     * @param $order_id
     * @return void
     * @since 1.0.0
     *
     */
    public function wemail_wc_order_received( $order_id ) {
        $order = wc_get_order( $order_id );

        $order_data = $order->get_data();

        $products = $this->get_products( $order );

        $this->orderRequest->received([
            'order' => $order_data,
            'products' => $products,
        ], $this->source);
    }


    /**
     * @param $order_id
     */
    public function wemail_wc_order_status_updated( $order_id ) {
        $this->orderRequest->statusUpdated([
            'order_id' => $order_id,
        ], $this->source);
    }

    /**
     * @param $product_obj
     * @return array
     */
    private function get_product_categories( $product_obj ) {
        $product_cats_ids = wc_get_product_term_ids( $product_obj->get_product_id(), 'product_cat' );
        $categories = [];
        foreach( $product_cats_ids as $cat_id ) {
            $term = get_term_by( 'id', $cat_id, 'product_cat' );

            $categories[] = [
                'id' => $cat_id,
                'name' => $term->name,
            ];;
        }

        return $categories;
    }

    /**
     * @param $order_obj
     * @return array
     */
    private function get_products( $order_obj ) {
        $items = $order_obj->get_items();
        $products = [];
        foreach ( $items as $item ) {

            $categories = $this->get_product_categories( $item );

            $products[] = [
                'name' => $item->get_name(),
                'product_id' => $item->get_product_id(),
                'variation_id' => $item->get_variation_id(),
                'categories' => $categories
            ];
        }

        return $products;
    }
}
