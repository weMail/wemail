<?php

namespace WeDevs\WeMail\Rest\Resources\Ecommerce\EDD;

use WeDevs\WeMail\Rest\Resources\JsonResource;

class OrderResourceV3 extends JsonResource {
    public function blueprint( $resource ) {
        /** @var \EDD_Payment $resource */

        return [
            'id'                => $resource->ID,
            'parent_id'         => $resource->parent_payment
        ];
    }
}
