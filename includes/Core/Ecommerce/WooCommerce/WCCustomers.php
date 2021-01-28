<?php

namespace WeDevs\WeMail\Core\Ecommerce\WooCommerce;

use WeDevs\WeMail\Traits\Singleton;

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

        $last_synced_id = $params['last_synced_id'] ? $params['last_synced_id'] : null;

        $offset = $params['limit'] * ( $params['page'] - 1 );

        $args = array(
            'role'      => 'customer',
            'orderby'   => $params['orderby'] ? $params['orderby'] : 'id',
            'number'    => $params['limit'] ? $params['limit'] : 50,
            'page'      => $params['page'] ? $params['page'] : 1,
            'offset'    => $offset,
            'fields'    => 'ID',
        );

        if ( $params['last_synced_id'] ) {
            $args['exclude'] = range( 1, $last_synced_id );
        }

        $user_count_data = count_users();

        $total_customer = isset( $user_count_data['avail_roles']['customer'] ) ?
            $user_count_data['avail_roles']['customer'] : 0;

        $customer_ids = get_users( $args );

        $customers = $wpdb->get_results(
            sprintf(
                "SELECT u.ID, u.user_email, u.display_name, u.user_registered,
                (select meta_value from $wpdb->usermeta where user_id = u.id and meta_key = 'first_name' limit 1) as first_name,
                (select meta_value from $wpdb->usermeta where user_id = u.id and meta_key = 'last_name' limit 1) as last_name
                FROM $wpdb->users u WHERE u.ID IN (%s)", implode( ', ', $customer_ids )
            )
        );

        $response['total'] = $total_customer;
        $response['total_page'] = $response['total'] ? ceil( $response['total'] / $args['number'] ) : 0;
        $response['current_page'] = intval( $args['page'] );
        $response['data'] = [];

        foreach ( $customers as $customer ) {
            $response['data'][] = [
                'source'             => 'woocommerce',
                'wp_user_id'         => $customer->ID,
                'email'              => $customer->user_email,
                'first_name'         => $customer->first_name,
                'last_name'          => $customer->last_name,
                'display_name'       => $customer->display_name,
                'date_created'       => gmdate( 'Y-m-d H:m:s', strtotime( $customer->user_registered ) ),
            ];
        }

        return $response;
    }
}
