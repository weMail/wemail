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
        $params = [
            'orderby'  => $args['orderby'] ? $args['orderby'] : 'date',
            'order'    => $args['order'] ? $args['order'] : 'DESC',
            'limit'    => $args['limit'] ? $args['limit'] : 50,
            'paginate' => true,
            'paged'    => $args['page'] ? $args['page'] : 1,
        ];

        if ( $args['status'] ) {
            $params['status'] = $args['status'];
        }


        $orders['current_page'] = intval( $params['paged'] );
//        $orders['total'] = $collection->total;
//        $orders['total_page'] = $collection->max_num_pages;
//
//        foreach ( $collection->orders as $order ) {
//            $orders['data'][] = $this->get( $order->get_id() );
//        }

        return $orders;
    }

}
