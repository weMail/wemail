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
        wemail()->api->ecommerce()->orders()->post([
            'source'       => $source,
            'order_id'     => $payload['order']['id'],
            'status'       => $payload['order']['status'],
            'currency'     => $payload['order']['currency'],
            'date_created' => $payload['order']['date_created'],
            'total'        => $payload['order']['total'],
            'customer_id'  => $payload['order']['customer_id'],
            'billing'      => $payload['order']['billing'],
            'products'     => $payload['products']
        ]);
    }

    /**
     * @param $payload
     * @param $source
     */
    public function statusUpdated( $payload, $source ) {
        wemail()->api->ecommerce()->order_status()->put([
            'source'   => $source,
            'order_id' => $payload['order_id'],
            'status'   => $payload['status']
        ]);
    }
}
