<?php

namespace WeDevs\WeMail\Rest\Resources\Ecommerce\WooCommerce;

use WeDevs\WeMail\Rest\Resources\JsonResource;

class ProductResource extends JsonResource {

    /**
     * @inheritDoc
     */
    public function blueprint( $resource ) {
        /** @var $resource \WC_Product */

        $thumbnail = wp_get_attachment_image_url( $resource->get_image_id(), 'woocommerce_thumbnail' );

        return [
            'id'         => (string) $resource->get_id(),
            'parent_id'  => (string) $resource->get_parent_id(),
            'name'       => $resource->get_name(),
            'price'      => floatval( $resource->get_price() ),
            'status'     => $resource->get_status(),
            'permalink'  => $resource->get_permalink(),
            'thumbnail'  => $thumbnail ? $thumbnail : null,
            'categories' => $resource->get_category_ids(),
            'source'     => 'woocommerce',
        ];
    }
}
