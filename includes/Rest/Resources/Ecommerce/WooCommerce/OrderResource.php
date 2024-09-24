<?php

namespace WeDevs\WeMail\Rest\Resources\Ecommerce\WooCommerce;

use DateTimeZone;
use WeDevs\WeMail\Rest\Resources\JsonResource;

class OrderResource extends JsonResource {

    /**
     * Date format
     */
    const DATE_FORMAT = 'Y-m-d h:i:s';

    /**
     * Structure of order
     *
     * @param $order
     *
     * @return array
     */
    public function blueprint( $order ) {
        /** @var \Automattic\WooCommerce\Admin\Overrides\Order $order */
        $items = array_filter(
            $order->get_items(), function ( $order_item ) {
				/** @var \WC_Order_Item_Product $order_item */
				return $order_item->get_product_id();
			}
        );

        $data = array(
            'id'         => (string) $order->get_id(),
            'parent_id'  => $order->get_parent_id() ? (string) $order->get_parent_id() : null,
            'products'   => OrderItemResource::collection( $items ),
            'status'     => $order->get_status(),
            'currency'   => $order->get_currency(),
            'total'      => floatval( $order->get_total() ),
            'source'     => 'woocommerce',
            'type'       => $this->get_type( $order->get_type() ),
            'created_at' => $order->get_date_created()->setTimezone( new DateTimeZone( 'UTC' ) )->format( self::DATE_FORMAT ),
            'updated_at' => $order->get_date_modified()->setTimezone( new DateTimeZone( 'UTC' ) )->format( self::DATE_FORMAT ),
        );

        if ( $this->is_refund( $data['type'] ) ) {
            /** @var $order \Automattic\WooCommerce\Admin\Overrides\OrderRefund */
            $data['customer_id']  = $this->get_customer_id( wc_get_order( $order->get_parent_id() )->get_billing_email() );
            $data['permalink']    = wc_get_order( $order->get_parent_id() )->get_edit_order_url();
            $data['completed_at'] = $order->get_date_modified()->setTimezone( new DateTimeZone( 'UTC' ) )->format( self::DATE_FORMAT );
        } else {
            $data['customer']     = $this->customer( $order );
            $data['permalink']    = $order->get_edit_order_url();
            $data['customer_id']  = $this->get_customer_id( $data['customer']['email'] );
            $completed_date       = $order->get_date_completed();
            $data['completed_at'] = $completed_date && $this->is_completed( $this->get_status( $order->get_status() ) ) ? $completed_date->setTimezone( new DateTimeZone( 'UTC' ) )->format( self::DATE_FORMAT ) : null;
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
        return array(
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
        );
    }

    /**
     * Get customer ID
     *
     * @param $email
     *
     * @return string
     */
    protected function get_customer_id( $email ) {
        return md5( trim( strtolower( $email ) ) );
    }


    /**
     * Get item type
     *
     * @param $type
     *
     * @return string
     */
    protected function get_type( $type ) {
        return $type === 'shop_order_refund' ? 'refund' : 'order';
    }

    /**
     * Get item status
     *
     * @param $status
     *
     * @return string
     */
    protected function get_status( $status ) {
        return preg_match( '/^completed|refunded$/', $status ) ? 'completed' : $status;
    }
}
