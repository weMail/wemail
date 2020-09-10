<?php

namespace WeDevs\WeMail\Core\Ecommerce\WooCommerce;

use WeDevs\WeMail\Traits\Singleton;

class WCOrders {

    use Singleton;

    /**
     * Get a collection of orders
     *
     * @param $params
     * @return array|\WP_Error|\WP_HTTP_Response|\WP_REST_Response
     * Details: https://www.businessbloomer.com/woocommerce-easily-get-product-info-title-sku-desc-product-object/
     * @since 1.0.0
     */
    public function all( $params ) {
        $statuses = ['completed'];

        $params = [
            'orderby'  => $params['orderby'] ? $params['orderby'] : 'date',
            'order'    => $params['order'] ? $params['order'] : 'DESC',
            'status'   => $params['status'] ? $params['status'] : $statuses,
            'limit'    => $params['limit'] ? $params['limit'] : 50,
            'paginate' => true,
            'paged'    => $params['page'] ? $params['page'] : 1
        ];

        $collection = wc_get_orders( $params );

        $wcProducts = new WCOrderProducts();

        $orders['current_page'] = intval($params['paged']);
        $orders['total'] = $collection->total;
        $orders['total_page'] = $collection->max_num_pages;

        foreach ($collection->orders as $order) {
            $order = new \WC_Order( $order->get_id() );

            $orders['data'][] = [
                'source'               => 'woocommerce',
                'id'                   => $order->get_id(),
                'parent_id'            => $order->get_parent_id(),
                'customer'             => $this->getCustomerInfo($order),
                'status'               => $order->get_status(),
                'currency'             => $order->get_currency(),
                'total'                => $order->get_total(),
                'payment_method_title' => $order->get_payment_method_title(),
                'date_created'         => $order->get_date_created()->format ('Y-m-d H:m:s'),
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

    private function getCustomerInfo( $order ) {
        $user = $order->get_user();
        if ($user) {
            return [
                'first_name' => $user ? $user->first_name : '',
                'last_name'  => $user ? $user->last_name : '',
                'email'      => $user ? $user->user_email : '',
            ];
        } else if ($order->get_billing_email()){
            return [
                'first_name' => $order->get_billing_first_name(),
                'last_name'  => $order->get_billing_last_name(),
                'email'      => $order->get_billing_email(),
            ];
        } else if (intval($order->get_parent_id()) != 0) {
            $order = new \WC_Order( $order->get_parent_id() );
            return [
                'first_name' => $order->get_billing_first_name(),
                'last_name'  => $order->get_billing_last_name(),
                'email'      => $order->get_billing_email(),
            ];
        }

        return [
            'first_name' => '',
            'last_name' => '',
            'email' => '',
        ];
    }

}
