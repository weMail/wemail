<?php

namespace WeDevs\WeMail\Rest\Resources\Ecommerce\EDD;

use WeDevs\WeMail\Rest\Resources\JsonResource;

class ProductResource extends JsonResource {

	/**
	 * @inheritDoc
	 */
	public function blueprint( $resource ) {
	    /** @var \WP_Post $resource */

        $download = new \EDD_Download( $resource->ID );

		return [
		    'id'        => $resource->ID,
            'name'      => $resource->post_title,
            'price'     => floatval( $download->get_price() ),
            'status'     => $resource->post_status,
            'permalink' => get_permalink( $resource ),
            'thumbnail' => $this->get_thumbnail( $resource ),
            'source'    => 'edd',
        ];
	}

	protected function get_thumbnail( \WP_Post $download ) {
        $image = get_the_post_thumbnail_url( $download );

        return empty( $image ) ? '' : $image;
    }
}
