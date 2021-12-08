<?php

namespace WeDevs\WeMail\Rest\Resources\Ecommerce\WooCommerce;

use WeDevs\WeMail\Rest\Resources\JsonResource;

class ProductResource extends JsonResource {

    /**
     * @inheritDoc
     */
    public function blueprint( $resource ) {
        /** @var $resource \WC_Product */
        return [
            'id'            => $resource->get_id(),
            'name'          => $resource->get_name(),
            'price'         => floatval( $resource->get_price() ),
            'status'        => $resource->get_status(),
            'permalink'     => $resource->get_permalink(),
            'thumbnail'     => wp_get_attachment_image_url( $resource->get_image_id(), 'woocommerce_thumbnail' ),
            'categories'    => $resource->get_category_ids(),
            'source'        => 'woocommerce',
        ];
    }
}
