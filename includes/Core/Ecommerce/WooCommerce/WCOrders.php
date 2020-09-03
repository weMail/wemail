<?php

namespace WeDevs\WeMail\Core\Ecommerce\WooCommerce;

use WeDevs\WeMail\Traits\Singleton;

class WCOrders {

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
        $statuses = ['completed'];

        $args = [
            'orderby'  => $args['orderby'] ? $args['orderby'] : 'date',
            'order'    => $args['order'] ? $args['order'] : 'DESC',
            'status'   => $args['status'] ? $args['status'] : $statuses,
            'limit'    => $args['limit'] ? $args['limit'] : 50,
            'paginate' => true,
            'paged'    => $args['paged'] ? $args['paged'] : 1
        ];

        $collection = wc_get_orders( $args );

        $wcProducts = new WCOrderProducts();

        $orders['current_page'] = intval($args['paged']);
        $orders['total'] = $collection->total;
        $orders['total_page'] = $collection->max_num_pages;

        foreach ($collection->orders as $order) {
            $orders['data'][] = [
                'id'                   => $order->get_id(),
                'user_id'              => $order->get_user_id(),
                'status'               => $order->get_status(),
                'currency'             => $order->get_currency(),
                'payment_method_title' => $order->get_payment_method_title(),
                'date_created'         => $order->get_date_created(),
                'permalink'            => get_permalink($order->get_id()),
                'products'             => $wcProducts->get_ordered_products($order)
            ];
        }

        return $orders;
    }

    /**
     * Get a single campaign
     *
     * @param string $id
     * @return array|false|\WC_Product
     * @since 1.0.0
     */
    public function get( $id ) {
        return wc_get_product ($id);
    }

}
