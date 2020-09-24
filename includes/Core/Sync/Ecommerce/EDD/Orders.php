<?php

namespace WeDevs\WeMail\Core\Sync\Ecommerce\EDD;

use WeDevs\WeMail\Core\Ecommerce\Requests\Orders as OrderRequest;
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
        $this->add_action( 'edd_complete_purchase', 'wemail_edd_complete_purchase' );
        $this->add_action( 'edd_update_payment_status', 'wemail_edd_update_payment_status', 10, 2 );

        $this->order_request = new OrderRequest();
    }

    /**
     * Sync new purchase
     *
     * @param $payment_id
     * @return void
     * @since 1.0.0
     */
    public function wemail_edd_complete_purchase( $payment_id ) {
        $integrated = get_option( 'wemail_edd_integrated' );
        $synced     = get_option( 'wemail_is_edd_synced' );
        if ( ! $integrated || ! $synced ) {
            return;
        }

        $payment_meta = edd_get_payment_meta( $payment_id );
        $payment = new \EDD_Payment($payment_id);

        $data = [
            'source'               => $this->source,
            'id'                   => $payment_id,
            'parent_id'            => '',
            'customer'             => $this->getCustomerInfo( $payment_meta['user_info'] ),
            'status'               => edd_get_payment_status( $payment_id ),
            'currency'             => $payment_meta['currency'],
            'total'                => $payment->total,
            'payment_method_title' => edd_get_payment_gateway($payment_id),
            'date_created'         => date('Y-m-d H:m:s', strtotime($payment_meta['date'])),
            'date_completed'       => date('Y-m-d H:m:s', strtotime($payment->completed_date)),
            'permalink'            => get_permalink( $payment_id ),
            'products'             => $this->get_ordered_products( $payment_meta['cart_details'] ),
        ];

        $this->order_request->received(
            $data,
            $this->source
        );
    }


    /**
     * @param $payment_id
     * @param $new_status
     */
    public function wemail_edd_order_status_updated( $payment_id, $new_status ) {
        $integrated = get_option( 'wemail_edd_integrated' );
        $synced     = get_option( 'wemail_is_edd_synced' );
        if ( ! $integrated || ! $synced ) {
            return;
        }

        $param = [
            'order_id' => $payment_id,
            'status'   => $new_status,
        ];

        $this->order_request->statusUpdated( $param, $this->source );
    }

    private function getCustomerInfo( $user ) {
        return [
            'wp_user_id'      => $user['id'] ?: '',
            'first_name'      => $user['first_name'] ?: '',
            'last_name'       => $user['last_name'] ?: '',
            'email'           => $user['email'] ?: '',
            'phone'           => '',
            'address_1'       => $user['address'],
            'address_2'       => '',
            'city'            => '',
            'state'           => '',
            'postcode'        => '',
            'country'         => ''
        ];
    }

    private function get_ordered_products( $cart_details ) {
        foreach($cart_details as $cart_item) {
            $download = new \EDD_Download( $cart_item['id'] );

            $products[] = [
                'id'           => $download->ID,
                'source'       => $this->source,
                'name'         => $download->post_title,
                'slug'         => $download->post_name,
                'total'        => $cart_item['subtotal'],
                'quantity'     => $cart_item['quantity'],
            ];
        }

        return $products;
    }
}
