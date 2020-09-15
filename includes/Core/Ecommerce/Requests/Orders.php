<?php

namespace WeDevs\WeMail\Core\Ecommerce\Requests;

use WeDevs\WeMail\Traits\Singleton;

class Orders {

    use Singleton;

    /**
     * @param $payload
     * @param $source
     */
    public function received( $payload, $source ) {
        wemail()->api->ecommerce()->orders()->post(
            [
                'source'               => $source,
                'id'                   => $payload['id'],
                'parent_id'            => $payload['parent_id'],
                'customer'             => $payload['customer'],
                'status'               => $payload['status'],
                'currency'             => $payload['currency'],
                'total'                => $payload['total'],
                'payment_method_title' => $payload['payment_method_title'],
                'date_created'         => $payload['date_created'],
                'date_completed'       => $payload['date_completed'],
                'permalink'            => $payload['permalink'],
                'products'             => $payload['products'],
            ]
        );
    }

    /**
     * @param $payload
     * @param $source
     */
    public function statusUpdated( $payload, $source ) {
        wemail()->api->ecommerce()->order_status()->put(
            [
                'source'   => $source,
                'order_id' => $payload['order_id'],
                'status'   => $payload['status'],
            ]
        );
    }
}
