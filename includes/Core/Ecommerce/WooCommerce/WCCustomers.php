<?php

namespace WeDevs\WeMail\Core\Ecommerce\WooCommerce;

use WeDevs\WeMail\Traits\Singleton;
use WP_User_Query;

class WCCustomers {

    use Singleton;

    /**
     * Get a collection of orders
     *
     * @param $params
     * @return array|\WP_Error|\WP_HTTP_Response|\WP_REST_Response
     * @throws \Exception
     * @since 1.0.0
     */
    public function all( $params ) {
        global $wpdb;

        $args = [
            'last_synced_id' => $params['last_synced_id'] ? $params['last_synced_id'] : null,
            'orderby'        => $params['orderby'] ? $params['orderby'] : 'id',
            'number'          => '-1',
            'role'           => 'Customer',
            'fields'         => 'ids',
        ];

        if ( $params['last_synced_id'] ) {
            $args['exclude'] = range( 1, $params['last_synced_id'] );
        }

        $wp_user_query = new WP_User_Query( $args );
        $total_customer = $wp_user_query->get_total();

        $args['number'] = $params['limit'] ? $params['limit'] : 100;
        $args['page'] = $params['page'] ? $params['page'] : 1;

        $offset = $args['number'] * ( $args['page'] - 1 );
        $total_pages = intval( ceil( $total_customer / $args['number'] ) );

        $args['offset'] = $offset;

        $wp_user_query = new WP_User_Query( $args );
        $customers = $wp_user_query->get_results();

        $response['current_page'] = intval( $args['page'] );
        $response['total'] = $total_customer;
        $response['total_page'] = $total_pages;
        $response['data'] = [];

        foreach ( $customers as $item ) {
            $customer = new \WC_Customer( $item );

            $response['data'][] = [
                'source'             => 'woocommerce',
                'wp_user_id'         => $customer->get_id(),
                'email'              => $customer->get_email(),
                'first_name'         => $customer->get_first_name(),
                'last_name'          => $customer->get_last_name(),
                'display_name'       => $customer->get_display_name(),
                'is_paying_customer' => $customer->get_is_paying_customer(),
                'date_created'       => $customer->get_date_created()->format( 'Y-m-d H:m:s' ),
                'last_order'         => $customer->get_last_order(),
            ];
        }

        return $response;
    }
}
