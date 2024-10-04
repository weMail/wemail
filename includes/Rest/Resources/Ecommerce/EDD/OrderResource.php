<?php

namespace WeDevs\WeMail\Rest\Resources\Ecommerce\EDD;

use EDD_Payment;
use WeDevs\WeMail\Rest\Resources\JsonResource;
use WP_Post;

class OrderResource extends JsonResource {
    /**
     * @inheritDoc
     */
    public function blueprint( $resource ) {
        /** @var WP_Post $resource */
        $payment = new EDD_Payment( $resource->ID );

        return array(
            'id'           => (string) $payment->transaction_id,
            'parent_id'    => $payment->parent_payment ? (string) $payment->parent_payment : null,
            'customer_id'  => $this->customer_id( $payment->user_info['email'] ),
            'customer'     => $this->customer( $payment->user_info ),
            'products'     => OrderItemResource::collection( $payment->cart_details ),
            'status'       => $this->get_status( $payment->status ),
            'currency'     => $payment->currency,
            'total'        => floatval( $payment->total ),
            'created_at'   => get_gmt_from_date( $payment->date ),
            'updated_at'   => get_gmt_from_date( $resource->post_modified ),
            'completed_at' => isset( $payment->completed_date ) ? get_gmt_from_date( $payment->completed_date ) : null,
            'permalink'    => add_query_arg( 'id', $payment->ID, admin_url( 'edit.php?post_type=download&page=edd-payment-history&view=view-order-details' ) ),
            'source'       => 'edd',
            'type'         => $this->is_refund( $resource->post_status ) ? 'refund' : 'order',
        );
    }

    /**
     * Get customer id
     *
     * @param $email
     *
     * @return string
     */
    public function customer_id( $email ) {
        return md5( strtolower( trim( $email ) ) );
    }

    /**
     * Get customer data
     *
     * @param $customer
     *
     * @return array
     */
    protected function customer( $customer ) {
        return array(
            'first_name' => $customer['first_name'],
            'last_name'  => $customer['last_name'],
            'email'      => $customer['email'],
            'address1'   => isset( $customer['address']['line1'] ) ? $customer['address']['line1'] : null,
            'address2'   => isset( $customer['address']['line2'] ) ? $customer['address']['line2'] : null,
            'city'       => isset( $customer['address']['city'] ) ? $customer['address']['city'] : null,
            'state'      => isset( $customer['address']['state'] ) ? $customer['address']['state'] : null,
            'zip'        => isset( $customer['address']['zip'] ) ? $customer['address']['zip'] : null,
            'country'    => isset( $customer['address']['country'] ) ? $customer['address']['country'] : null,
        );
    }

    /**
     * Get formatted order status
     *
     * @param $status
     *
     * @return string
     */
    protected function get_status( $status ) {
        return $status === 'publish' ? 'completed' : $status;
    }

    /**
     * Check is it refund item
     *
     * @param $type
     *
     * @return bool
     */
    public function is_refund( $type ) {
        return $type === 'refunded';
    }
}
