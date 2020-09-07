<?php

namespace WeDevs\WeMail\Core\Ecommerce\WooCommerce;

use WeDevs\WeMail\Traits\Singleton;
use WP_User_Query;

class WCCustomers {

    use Singleton;

    /**
     * Get a collection of orders
     *
     * @param $args
     * @return array|\WP_Error|\WP_HTTP_Response|\WP_REST_Response
     * @since 1.0.0
     */
    public function all( $params ) {
        global $wpdb;

        $params = [
            'last_synced_id'    => $params['last_synced_id'] ? $params['last_synced_id'] : null,
            'orderby'    => $params['orderby'] ? $params['orderby'] : 'id',
            'limit'    => $params['limit'] ? $params['limit'] : 50,
            'page'    => $params['page'] ? $params['page'] : 1,
            'role'    => 'Customer',
            'fields'    => 'all_with_meta',
        ];

        $count_args  = array(
            'role'      => $params['role'],
            'fields'    => $params['fields'],
        );

        if ($params['last_synced_id']) {
            $count_args['exclude'] = range( 1, $params['last_synced_id']);
        }

        $user_count_query = new WP_User_Query($count_args);
        $user_count = $user_count_query->get_results();

// count the number of users found in the query
        $totalCustomer = $user_count ? count($user_count) : 1;

        $offset = $params['limit'] * ($params['page'] - 1);
        $total_pages = ceil($totalCustomer / $params['limit']);

        $args  = array(
            'role'      => $params['role'],
            'orderby'   => $params['orderby'],
            'fields'    => $params['fields'],
            'number'    => $params['limit'],
            'page'      => $params['page'],
            'offset'    => $offset // skip the number of users that we have per page
        );

        if ($params['last_synced_id']) {
            $args['exclude'] = range( 1, $params['last_synced_id']);
        }

// Create the WP_User_Query object
        $wp_user_query = new WP_User_Query($args);
//        dd($wp_user_query);

// Get the results
        $customers = $wp_user_query->get_results();

        $response['current_page'] = intval($args['page']);
        $response['total'] = $totalCustomer;
        $response['total_page'] = $total_pages;
        $response['data'] = [];

        foreach ($customers as $item) {


            $customer = new \WC_Customer($item->ID);

            $response['data'][] = [
                'id' => $item->ID,
                'customer_id' => $customer->get_id(),
                'email' => $customer->get_email(),
                'first_name' => $customer->get_first_name(),
                'last_name' => $customer->get_last_name(),
                'avatar' => $customer->get_avatar_url(),
                'display_name' => $customer->get_display_name(),
                'billing' => $customer->get_billing(),
                'shipping' => $customer->get_shipping(),
                'is_paying_customer' => $customer->get_is_paying_customer(),
                'date_created' => $customer->get_date_created(),
                'last_order' => $customer->get_last_order()
            ];
        }

        return $response;
    }
}
