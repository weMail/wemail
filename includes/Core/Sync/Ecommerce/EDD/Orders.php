<?php

namespace WeDevs\WeMail\Core\Sync\Ecommerce\EDD;

use WeDevs\WeMail\Core\Ecommerce\Requests\Orders as OrderRequest;
use WeDevs\WeMail\Traits\Hooker;
use WeDevs\WeMail\Core\Ecommerce\EDD\EDDOrders;

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

        $this->order_request = OrderRequest::instance();
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

        $edd_orders = new EDDOrders();

        $this->order_request->received(
            $edd_orders->get( $payment_id ),
            $this->source
        );
    }


    /**
     * @param $payment_id
     * @param $new_status
     */
    public function wemail_edd_update_payment_status( $payment_id, $new_status ) {
        $integrated = get_option( 'wemail_edd_integrated' );
        $synced     = get_option( 'wemail_is_edd_synced' );
        if ( ! $integrated || ! $synced ) {
            return;
        }

        $param = array(
            'order_id' => $payment_id,
            'status'   => $new_status,
        );

        $this->order_request->statusUpdated( $param, $this->source );
    }
}
