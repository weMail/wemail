<?php

namespace WeDevs\WeMail\Rest\Resources\Ecommerce\EDD;

use EDD_Payment;

class OrderResourceV3 extends OrderResource {

    /**
     * Define structure of a single order
     *
     * @param $resource
     *
     * @return array
     */
    public function blueprint( $resource ) {
        /** @var EDD_Payment $resource */
        $is_order = $resource->order->type === 'sale';

        $data = array(
            'id'           => (string) $resource->ID,
            'parent_id'    => $resource->parent_payment ? (string) $resource->parent_payment : null,
            'customer_id'  => $this->customer_id( $resource->user_info['email'] ),
            'products'     => OrderItemResource::collection( $resource->cart_details ),
            'status'       => $this->get_status( $resource->order->status ),
            'currency'     => $resource->currency,
            'total'        => floatval( $resource->total ),
            'permalink'    => add_query_arg( 'id', $resource->ID, admin_url( 'edit.php?post_type=download&page=edd-payment-history&view=view-order-details' . ( $is_order ? 'view-order-details' : 'view-refund-details' ) ) ),
            'type'         => $is_order ? 'order' : 'refund',
            'completed_at' => $resource->completed_date,
            'created_at'   => $resource->order->date_created,
            'updated_at'   => $resource->order->date_modified,
            'source'       => 'edd',
        );

        if ( $is_order ) {
            $data['customer'] = $this->customer( $resource->user_info );
        }

        return $data;
    }

    /**
     * Get formatted order status
     *
     * @param $status
     *
     * @return string
     */
    protected function get_status( $status ) {
        switch ( $status ) {
            case 'partially_refunded':
            case 'complete':
                return 'completed';
            case 'refund':
                return 'refunded';
            default:
                return $status;
        }
    }
}
