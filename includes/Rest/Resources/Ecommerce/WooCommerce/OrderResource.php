<?php

namespace WeDevs\WeMail\Rest\Resources\Ecommerce\WooCommerce;

use WeDevs\WeMail\Rest\Resources\JsonResource;

class OrderResource extends JsonResource {

    /**
     * Date format
     */
    const DATE_FORMAT = 'Y-m-d h:i:s';

    public function blueprint( $order ) {
        /** @var \Automattic\WooCommerce\Admin\Overrides\Order $order */
        $status = $order->get_status();

        $data = [
            'id'            => $order->get_id(),
            'is_refund'     => $order->get_type() === 'shop_order_refund',
            'parent_id'     => $order->get_parent_id(),
            'products'      => OrderItemResource::collection( $order->get_items() ),
            'customer'      => [],
            'status'        => $status,
            'currency'      => $order->get_currency(),
            'total'         => floatval( $order->get_total() ),
            'created_at'    => $order->get_date_created()->format( self::DATE_FORMAT ),
            'updated_at'    => $order->get_date_modified()->format( self::DATE_FORMAT ),
            'source'        => 'woocommerce',
            'permalink'     => '',
        ];
        // Refunded completed order
        if ( $status === 'completed' && ! $order->get_parent_id() ) {
            $data['completed_at'] = $order->get_date_completed()->format( self::DATE_FORMAT );
        }

        if ( ! $order->get_parent_id() ) {
            $data['customer'] = $this->customer( $order );
            $data['permalink'] = get_edit_post_link( $order->get_id(), 'raw' );
        } else {
            /** @var $order \Automattic\WooCommerce\Admin\Overrides\OrderRefund */
            $data['customer'] = $this->customer( wc_get_order( $order->get_parent_id() ) );
            $data['refunded_at'] = $order->get_date_modified()->format( self::DATE_FORMAT );
            $data['permalink'] = get_edit_post_link( $order->get_parent_id(), 'raw' );
        }

        return $data;
    }

    /**
     * Transform customer
     *
     * @param $order
     *
     * @return array
     */
    protected function customer( $order ) {
        return [
            'first_name' => $order->get_billing_first_name(),
            'last_name'  => $order->get_billing_last_name(),
            'email'      => $order->get_billing_email(),
            'phone'      => $order->get_billing_phone(),
            'address1'   => $order->get_billing_address_1(),
            'address2'   => $order->get_billing_address_2(),
            'city'       => $order->get_billing_city(),
            'state'      => $order->get_billing_state(),
            'zip'        => $order->get_billing_postcode(),
            'country'    => $order->get_billing_country(),
        ];
    }
}
