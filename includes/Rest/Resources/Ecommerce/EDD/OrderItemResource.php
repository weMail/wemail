<?php

namespace WeDevs\WeMail\Rest\Resources\Ecommerce\EDD;

use WeDevs\WeMail\Rest\Resources\JsonResource;

class OrderItemResource extends JsonResource {

	/**
	 * @inheritDoc
	 */
	public function blueprint( $resource ) {
		return [
            'id'        => $resource['id'],
            'name'      => $resource['name'],
            'quantity'  => $resource['quantity'],
            'total'     => $resource['price'],
            'thumbnail' => get_the_post_thumbnail_url( $resource['id'], 'thumbnail' ),
            'permalink' => get_permalink( $resource['id'] ),
        ];
	}
}
