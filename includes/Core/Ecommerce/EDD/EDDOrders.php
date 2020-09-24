<?php

namespace WeDevs\WeMail\Core\Ecommerce\EDD;

use WeDevs\WeMail\Traits\Singleton;

class EDDOrders {

    use Singleton;

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
            'orderby'   => $args['orderby'] ? $args['orderby'] : 'date',
            'order'     => $args['order'] ? $args['order'] : 'DESC',
            'number'    => $args['limit'] ? $args['limit'] : 50,
            'page'      => $args['page'] ? $args['page'] : 1,
            'mode'      => edd_is_test_mode() ? 'test' : 'live'
        ];

        if ( $args['status'] ) {
            $params['status'] = $args['status'];
        }

        $eddPayments = edd_get_payments( $params );

        dd($eddPayments);
        
        $total = count($eddPayments);


        $orders['current_page'] = intval( $params['page'] );
        $orders['total'] = $total;
        $orders['total_page'] = ceil($total/$params['number']);
        $orders['data'] = null;

        foreach ( $eddPayments as $order ) {
       
            $orders['data'][] = [
                'source'               => 'edd',
                'id'                   => $order->ID,
                'parent_id'            => $order->get_parent_id(),
                'customer'             => $this->getCustomerInfo( $order ),
                'status'               => $order->get_status(),
                'currency'             => $order->get_currency(),
                'total'                => $order->get_total(),
                'payment_method_title' => $order->get_payment_method_title(),
                'date_created'         => $order->get_date_created()->format( 'Y-m-d H:m:s' ),
                'date_completed'       => $date_completed ? $date_completed->format( 'Y-m-d H:m:s' ) : '',
                'permalink'            => get_permalink( $order->get_id() ),
                'products'             => $this->wc_products->get_ordered_products( $order ),
            ];
        }

        return $orders;
    }

}
