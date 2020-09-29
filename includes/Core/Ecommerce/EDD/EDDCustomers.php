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
            'orderby'        => $args['orderby'] ? $args['orderby'] : 'id',
            'number'         => $args['limit'] ? $args['limit'] : 50,
            'page'           => $args['page'] ? $args['page'] : 1,
        ];

        $api = new \EDD_API_V2();

        $all_edd_customers = $api->get_customers();

        $total_customer = count( $all_edd_customers['customers'] );

        $edd_customers = $api->get_customers( $params );

        $response['current_page'] = intval( $params['page'] );
        $response['total'] = $total_customer;
        $response['total_page'] = ceil( $total_customer / $params['number'] );
        $response['data'] = [];

        foreach ( $edd_customers['customers'] as $item ) {
            if ( $args['last_synced_id'] && $item['info']['user_id'] && $item['info']['user_id'] <= $args['last_synced_id'] ) {
                continue;
            }

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
