<?php

namespace WeDevs\WeMail\Rest\Resources\Ecommerce\EDD;

use WeDevs\WeMail\Rest\Resources\Resource;

class OrderResource extends Resource {

	/**
	 * @inheritDoc
	 */
	public function blueprint( $resource ) {
		/** @var \WP_Post $resource */
        $payment = new \EDD_Payment( $resource->ID );

        return [
            'id'                => $resource->ID,
            'parent_id'         => $resource->post_parent,
            'is_refund'         => $resource->post_status === 'refunded',
            'customer'          => $this->customer( $payment->user_info ),
            'products'          => OrderItemResource::collection( $payment->cart_details ),
            'status'            => $this->get_status( $resource->post_status ),
            'currency'          => $payment->currency,
            'total'             => floatval( $payment->total ),
            'created_at'        => $payment->date,
            'updated_at'        => $resource->post_modified,
            'completed_at'      => isset( $payment->completed_date ) ? $payment->completed_date : null,
            'permalink'         => add_query_arg( 'id', $payment->ID, admin_url( 'edit.php?post_type=download&page=edd-payment-history&view=view-order-details' ) ),
            'source'            => 'edd',
        ];
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
     * Get customer data
     *
     * @param $customer
     *
     * @return array
     */
    protected function customer( $customer ) {
	    return [
            'first_name' => $customer['first_name'],
            'last_name'  => $customer['last_name'],
            'email'      => $customer['email'],
            'address1'   => isset( $customer['address']['line1'] ) ? $customer['address']['line1'] : null,
            'address2'   => isset( $customer['address']['line2'] ) ? $customer['address']['line2'] : null,
            'city'       => isset( $customer['address']['city'] ) ? $customer['address']['city'] : null,
            'state'      => isset( $customer['address']['state'] ) ? $customer['address']['state'] : null,
            'zip'        => isset( $customer['address']['zip'] ) ? $customer['address']['zip'] : null,
            'country'    => isset( $customer['address']['country'] ) ? $customer['address']['country'] : null,
        ];
    }
}
