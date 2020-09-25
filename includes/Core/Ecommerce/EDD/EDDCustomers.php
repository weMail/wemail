<?php

namespace WeDevs\WeMail\Core\Ecommerce\EDD;

use WeDevs\WeMail\Traits\Singleton;
use WP_User_Query;

class EDDCustomers {

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

        $params = [
            'orderby'        => $params['orderby'] ? $params['orderby'] : 'id',
            'number'         => $params['limit'] ? $params['limit'] : 50,
            'page'           => $params['page'] ? $params['page'] : 1,
        ];

        $api = new \EDD_API_V2();
        $edd_customers = $api->get_customers( $params );

        $total_customer = count( $edd_customers['customers'] );

        $response['current_page'] = intval( $params['page'] );
        $response['total'] = $total_customer;
        $response['total_page'] = ceil( $total_customer / $params['number'] );
        $response['data'] = [];

        foreach ( $edd_customers['customers'] as $item ) {
            $response['data'][] = [
                'source'             => 'edd',
                'wp_user_id'         => $item['info']['user_id'],
                'email'              => $item['info']['email'],
                'first_name'         => $item['info']['first_name'],
                'last_name'          => $item['info']['last_name'],
                'display_name'       => $item['info']['display_name'],
                'is_paying_customer' => boolval( (int) $item['stats']['total_purchases'] ),
                'date_created'       => $item['info']['date_created'],
                'last_order'         => '',
            ];
        }

        return $response;
    }
}
