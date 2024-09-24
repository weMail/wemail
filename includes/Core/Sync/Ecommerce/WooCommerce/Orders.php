<?php

namespace WeDevs\WeMail\Core\Sync\Ecommerce\WooCommerce;

use WeDevs\WeMail\Core\Ecommerce\Requests\Orders as OrderRequest;
use WeDevs\WeMail\Core\Ecommerce\WooCommerce\WCOrders;
use WeDevs\WeMail\Traits\Hooker;

class Orders {

    use Hooker;

    protected $order_request;
    protected $wc_order;

    protected $source = 'woocommerce';
    /**
     * Class constructor
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function __construct() {
        $this->add_action( 'woocommerce_thankyou', 'wemail_wc_order_received', 10, 1 );
        $this->add_action( 'woocommerce_order_status_changed', 'wemail_wc_order_status_updated', 10, 3 );

        $this->order_request = OrderRequest::instance();
        $this->wc_order = WCOrders::instance();
    }

    /**
     * Sync new order
     *
     * @param $order_id
     * @return void
     * @since 1.0.0
     */
    public function wemail_wc_order_received( $order_id ) {
        $integrated = get_option( 'wemail_woocommerce_integrated' );
        $synced     = get_option( 'wemail_is_woocommerce_synced' );
        if ( ! $integrated || ! $synced ) {
            return;
        }

        $this->order_request->received(
            $this->wc_order->get( $order_id ),
            $this->source
        );
    }


    /**
     * @param $order_id
     * @param $old_status
     * @param $new_status
     */
    public function wemail_wc_order_status_updated( $order_id, $old_status, $new_status ) {
        $integrated = get_option( 'wemail_woocommerce_integrated' );
        $synced     = get_option( 'wemail_is_woocommerce_synced' );
        if ( ! $integrated || ! $synced ) {
            return;
        }

        $param = array(
            'order_id' => $order_id,
            'status'   => $new_status,
        );
        $this->order_request->statusUpdated( $param, $this->source );
    }
}
