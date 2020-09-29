<?php

namespace WeDevs\WeMail\Core\Ecommerce\EDD;

use WeDevs\WeMail\Traits\Singleton;

class EDDOrders {

    use Singleton;

    protected $source = 'edd';
    /**
     * Get a collection of orders
     *
     * @param $args
     * @return array|\WP_Error|\WP_HTTP_Response|\WP_REST_Response
     * Details: https://www.businessbloomer.com/woocommerce-easily-get-product-info-title-sku-desc-product-object/
     * @since 1.0.0
     */
    public function all( $args ) {
        $integrated = get_option( 'wemail_edd_integrated' );
        $synced     = get_option( 'wemail_is_edd_synced' );
        if ( ! $integrated || ! $synced ) {
            return [
                'data' => [],
                'message' => __( 'EDD not integrated with weMail', 'wemail' ),
            ];
        }

        $params = [
            'post__not_in' => $args['last_synced_id'] ? range( 1, $args['last_synced_id'] ) : null,
            'orderby'      => $args['orderby'] ? $args['orderby'] : 'date',
            'order'        => $args['order'] ? $args['order'] : 'DESC',
            'number'       => $args['limit'] ? $args['limit'] : 50,
            'page'         => $args['page'] ? $args['page'] : 1,
            'mode'         => edd_is_test_mode() ? 'test' : 'live',
            'fields'       => 'ids',
        ];

        if ( $args['status'] ) {
            $params['status'] = $args['status'];
        }

        $all_payment_ids = edd_get_payments(
            [
                'number'       => -1,
                'post__not_in' => $params['post__not_in'],
                'mode'         => $params['mode'],
                'fields'       => $params['fields'],
			]
        );

        $total = count( $all_payment_ids );

        $edd_payment_ids = edd_get_payments( $params );

        $orders['current_page'] = intval( $params['page'] );
        $orders['total'] = $total;
        $orders['total_page'] = ceil( $total / $params['number'] );
        $orders['data'] = null;

        foreach ( $edd_payment_ids as $payment_id ) {
            $orders['data'][] = $this->get( $payment_id );
        }

        return $orders;
    }

    public function get( $payment_id ) {
        $payment_meta = edd_get_payment_meta( $payment_id );
        $payment = new \EDD_Payment( $payment_id );

        return [
            'source'               => $this->source,
            'id'                   => $payment_id,
            'parent_id'            => '',
            'customer'             => $this->getCustomerInfo( $payment_meta['user_info'] ),
            'status'               => edd_get_payment_status( $payment_id ),
            'currency'             => $payment_meta['currency'],
            'total'                => $payment->total,
            'payment_method_title' => edd_get_payment_gateway( $payment_id ),
            'date_created'         => gmdate( 'Y-m-d H:m:s', strtotime( $payment_meta['date'] ) ),
            'date_completed'       => gmdate( 'Y-m-d H:m:s', strtotime( $payment->completed_date ) ),
            'permalink'            => get_permalink( $payment_id ),
            'products'             => $this->get_ordered_products( $payment_meta['cart_details'] ),
        ];
    }

    private function getCustomerInfo( $user ) {
        return [
            'wp_user_id'      => $user['id'] ? $user['id'] : '',
            'first_name'      => $user['first_name'] ? $user['first_name'] : '',
            'last_name'       => $user['last_name'] ? $user['last_name'] : '',
            'email'           => $user['email'] ? $user['email'] : '',
            'phone'           => '',
            'address_1'       => $user['address'],
            'address_2'       => '',
            'city'            => '',
            'state'           => '',
            'postcode'        => '',
            'country'         => '',
        ];
    }

    private function get_ordered_products( $cart_details ) {
        foreach ( $cart_details as $cart_item ) {
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
