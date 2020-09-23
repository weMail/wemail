<?php

namespace WeDevs\WeMail\Core\Sync\Ecommerce\EDD;

use WeDevs\WeMail\Core\Ecommerce\Requests\Orders as OrderRequest;
use WeDevs\WeMail\Core\Ecommerce\WooCommerce\WCOrders;
use WeDevs\WeMail\Traits\Hooker;

class Orders {

    use Hooker;

    protected $order_request;

    protected $source = 'edd';
    /**
     * Class constructor
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function __construct() {
        $this->add_action( 'edd_complete_purchase', 'wemail_edd_order_received' );
//        $this->add_action( 'woocommerce_order_status_changed', 'wemail_wc_order_status_updated', 10, 3 );

        $this->order_request = new OrderRequest();
    }

    /**
     * Sync new order
     *
     * @param $payment_id
     * @return void
     * @since 1.0.0
     */
    public function wemail_edd_order_received( $payment_id ) {
//        $integrated = get_option( 'wemail_edd_integrated' );
//        $synced     = get_option( 'wemail_is_edd_synced' );
//        if ( ! $integrated || ! $synced ) {
//            return;
//        }

        // Basic payment meta
        $payment_meta = edd_get_payment_meta( $payment_id );



        // Cart details
        $cart_items = edd_get_payment_meta_cart_details( $payment_id );

        $data = [
            'source'               => 'woocommerce',
            'id'                   => $payment_id,
            'parent_id'            => '',
            'customer'             => $this->getCustomerInfo( $payment_meta->user_info->email ),
            'status'               => '',
            'currency'             => $payment_meta->currency,
            'total'                => '',
            'payment_method_title' => '',
            'date_created'         => $payment_meta->date->format( 'Y-m-d H:m:s' ),
            'date_completed'       => $payment_meta->date->format( 'Y-m-d H:m:s' ),
            'permalink'            => get_permalink( $payment_id ),
            'products'             => $this->get_ordered_products( $payment_id ),
        ];

        $data = [];
        error_log('============================');
        error_log(json_encode($payment_meta));
        error_log('============================');
        return;

//        $this->order_request->received(
//            $data,
//            $this->source
//        );
    }


    /**
     * @param $order_id
     * @param $old_status
     * @param $new_status
     */
    public function wemail_edd_order_status_updated( $order_id, $old_status, $new_status ) {
//        $integrated = get_option( 'wemail_edd_integrated' );
//        $synced     = get_option( 'wemail_is_edd_synced' );
//        if ( ! $integrated || ! $synced ) {
//            return;
//        }

        $param = [
            'order_id' => $order_id,
            'status'   => $new_status,
        ];
        $this->order_request->statusUpdated( $param, $this->source );
    }

    private function getCustomerInfo( $id ) {
        $user = new \EDD_Customer( $id );

        if ( $user ) {
            $customer = [
                'wp_user_id' => $user ? $user->id : '',
                'first_name' => $user ? $user->first_name : '',
                'last_name'  => $user ? $user->last_name : '',
                'email'      => $user ? ( $user->user_email ? $user->user_email : $order->get_billing_email() ) : '',
            ];
        } elseif ( intval( $order->get_parent_id() ) !== 0 ) {
            $order = new \WC_Order( $order->get_parent_id() );

            return $this->getCustomerInfo( $order );
        } else {
            $customer = [
                'wp_user_id' => '',
                'first_name' => $order->get_billing_first_name(),
                'last_name'  => $order->get_billing_last_name(),
                'email'      => $order->get_billing_email(),
            ];
        }

        $customer['phone']  = $order->get_billing_phone();
        $customer['address_1']  = $order->get_billing_address_1();
        $customer['address_2']  = $order->get_billing_address_2();
        $customer['city']  = $order->get_billing_city();
        $customer['state']  = $order->get_billing_state();
        $customer['postcode']  = $order->get_billing_postcode();
        $customer['country']  = $order->get_billing_country();

        return $customer;
    }

    private function get_ordered_products( $payment_id ) {

    }
}
