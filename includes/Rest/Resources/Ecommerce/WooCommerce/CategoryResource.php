<?php

namespace WeDevs\WeMail\Rest\Resources\Ecommerce\WooCommerce;

use WeDevs\WeMail\Rest\Resources\JsonResource;

class CategoryResource extends JsonResource {
    /**
     * @inerhitDoc
     */
    protected $reset_keys = true;

    /**
     * @param \WP_Term $resource
     *
     * @return mixed|void
     */
    public function blueprint( $resource ) {
        return array(
            'id'          => (string) $resource->term_id,
            'name'        => $resource->name,
            'slug'        => $resource->slug,
            'parent_id'   => $resource->parent ? (string) $resource->parent : null,
            'description' => $resource->description,
            'source'      => 'woocommerce',
        );
    }
}
