<?php

namespace WeDevs\WeMail\Core\Sync\Ecommerce\WooCommerce\Orders;

use WeDevs\WeMail\Core\Ecommerce\Requests\Orders;
use WeDevs\WeMail\Core\Ecommerce\WooCommerce\WCOrderProducts;
use WeDevs\WeMail\Traits\Hooker;

class Hooks {

    use Hooker;

    protected $orderRequest;
    protected $wcProducts;

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
        $this->add_action( 'woocommerce_order_status_changed', 'wemail_wc_order_status_updated', 10, 3 );

        $this->orderRequest = new Orders();
        $this->wcProducts = new WCOrderProducts();
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


        $products = $this->wcProducts->get_ordered_products( $order );

        $this->orderRequest->received([
            'order'    => $order_data,
            'products' => $products,
        ], $this->source);
    }


    /**
     * @param $order_id
     * @param $old_status
     * @param $new_status
     */
    public function wemail_wc_order_status_updated( $order_id, $old_status, $new_status ) {
        $this->orderRequest->statusUpdated([
            'order_id' => $order_id,
            'status'   => $new_status,
        ], $this->source);
    }
}
