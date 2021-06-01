<?php

namespace WeDevs\WeMail\Core\Ecommerce\WooCommerce;

use WeDevs\WeMail\Traits\Singleton;

class WCOrders {

    use Singleton;

    protected $wc_products;

    public function __construct() {
        $this->wc_products = new WCOrderProducts();
    }
    /**
     * Get a collection of orders
     *
     * @param $args
     * @return array|\WP_Error|\WP_HTTP_Response|\WP_REST_Response
     * Details: https://www.businessbloomer.com/woocommerce-easily-get-product-info-title-sku-desc-product-object/
     * @since 1.0.0
     */
    public function all( $args ) {
        $integrated = get_option( 'wemail_woocommerce_integrated' );
        $synced     = get_option( 'wemail_is_woocommerce_synced' );
        if ( ! $integrated || ! $synced ) {
            return [
                'data' => [],
                'message' => __( 'WooCommerce not integrated with weMail', 'wemail' ),
            ];
        }

        $params = [
            'exclude'        => $args['last_synced_id'] ? range( 1, $args['last_synced_id'] ) : null,
            'orderby'        => $args['orderby'] ? $args['orderby'] : 'date',
            'order'          => $args['order'] ? $args['order'] : 'DESC',
            'limit'          => $args['limit'] ? $args['limit'] : 50,
            'paginate'       => true,
            'paged'          => $args['page'] ? $args['page'] : 1,
        ];

        if ( $args['status'] ) {
            $params['status'] = $args['status'];
        }

        $collection = wc_get_orders( $params );
        $orders['data'] = [];
        $orders['current_page'] = intval( $params['paged'] );
        $orders['total'] = $collection->total;
        $orders['total_page'] = $collection->max_num_pages;

        foreach ( $collection->orders as $order ) {
            $date_completed = $order->get_date_completed();
            /** @var \Automattic\WooCommerce\Admin\Overrides\Order $order*/

            $orders['data'][] = [
                'id' => $order->get_id(),
                'parent_id' => $order->get_parent_id(),
                'customer' => [
                    'wp_user_id' => $order->get_customer_id(),
                    'first_name' => $order->get_billing_first_name(),
                    'last_name' => $order->get_billing_last_name(),
                    'email' => $order->get_billing_email(),
                    'phone' => $order->get_billing_phone(),
                    'address1' => $order->get_billing_address_1(),
                    'address2' => $order->get_billing_address_2(),
                    'city' => $order->get_billing_city(),
                    'zip' => $order->get_billing_postcode(),
                    'country' => $order->get_billing_country(),
                ],
                'status' => $order->get_status(),
                'currency' => $order->get_currency(),
                'total'    => $order->get_total(),
                'payment_method_title' => $order->get_payment_method_title(),
                'permalink'            => htmlspecialchars_decode( get_edit_post_link( $order->get_id() ) ),
                'products' => $this->get_items( $order ),
                'source' => 'woocommerce',
                'created_at' => $order->get_date_created()->format( 'Y-m-d H:m:s' ),
                'completed_at'       => $date_completed ? $date_completed->format( 'Y-m-d H:m:s' ) : null,
            ];
        }

        return $orders;
    }

    /**
     * Get order items
     *
     * @param \Automattic\WooCommerce\Admin\Overrides\Order $order
     * @return array
     */
    protected function get_items( $order ) {
        return array_values(
            array_map(
                function ( $item ) {
                    /** @var \WC_Order_Item $item*/
                    return [
                        'id'           => $item->get_product_id(),
                        'name'         => $item->get_name(),
                        'total'        => $item->get_total(),
                        'quantity'     => $item->get_quantity(),
                        'source'       => 'woocommerce',
                    ];
                }, $order->get_items()
            )
        );
    }

    /**
     * Get a single campaign
     *
     * @param string $id
     * @return array|false|\WC_Product
     * @since 1.0.0
     */
    public function get( $id ) {
        $order = new \WC_Order( $id );
        $date_completed = $order->get_date_completed();

        return [
            'source'               => 'woocommerce',
            'id'                   => $order->get_id(),
            'parent_id'            => $order->get_parent_id(),
            'customer'             => $this->getCustomerInfo( $order ),
            'status'               => $order->get_status(),
            'currency'             => $order->get_currency(),
            'total'                => $order->get_total(),
            'payment_method_title' => $order->get_payment_method_title(),
            'date_created'         => $order->get_date_created()->format( 'Y-m-d H:m:s' ),
            'date_completed'       => $date_completed ? $date_completed->format( 'Y-m-d H:m:s' ) : '',
            'permalink'            => htmlspecialchars_decode( get_edit_post_link( $order->get_id() ) ),
            'products'             => $this->wc_products->get_ordered_products( $order ),
        ];
    }

    private function getCustomerInfo( $order ) {
        $user = $order->get_user();

        if ( $user ) {
            $customer = [
                'wp_user_id' => $user ? $user->id : '',
                'first_name' => $user ? $user->first_name : '',
                'last_name'  => $user ? $user->last_name : '',
                'email'      => $user ? ( $user->user_email ? $user->user_email : $order->get_billing_email() ) : '',
            ];
        } elseif ( intval( $order->get_parent_id() ) !== 0 ) {
            $order = new \WC_Order( $order->get_parent_id() );

            return $this->getCustomerInfo( $order );
        } else {
            $customer = [
                'wp_user_id' => '',
                'first_name' => $order->get_billing_first_name(),
                'last_name'  => $order->get_billing_last_name(),
                'email'      => $order->get_billing_email(),
            ];
        }

        $customer['phone']  = $order->get_billing_phone();
        $customer['address_1']  = $order->get_billing_address_1();
        $customer['address_2']  = $order->get_billing_address_2();
        $customer['city']  = $order->get_billing_city();
        $customer['state']  = $order->get_billing_state();
        $customer['postcode']  = $order->get_billing_postcode();
        $customer['country']  = $order->get_billing_country();

        return $customer;
    }

}
