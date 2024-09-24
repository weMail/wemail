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

        return array(
            'id'         => (string) $resource->get_id(),
            'parent_id'  => (string) $resource->get_parent_id(),
            'name'       => $this->get_resource_name( $resource ),
            'price'      => floatval( $resource->get_price() ),
            'status'     => $resource->get_status(),
            'permalink'  => $resource->get_permalink(),
            'thumbnail'  => $thumbnail ? $thumbnail : null,
            'categories' => $resource->get_category_ids(),
            'source'     => 'woocommerce',
        );
    }

    /**
     * @param \WC_Product $resource
     * @return string
     */
    public function get_resource_name( \WC_Product $resource ) {
        if ( $resource->is_type( 'variation' ) ) {
            $attributes = $resource->get_attributes();
            $variation_names = array();

            if ( $attributes ) {
                foreach ( $attributes as $attribute ) {
                    if ( $attribute ) {
                        $variation_names[] = $attribute;
                    }
                }
            }
            return $resource->get_title() . '- ' . implode( ',', $variation_names );
        }
        return $resource->get_name();
    }
}
