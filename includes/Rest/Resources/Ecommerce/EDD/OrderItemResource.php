<?php

namespace WeDevs\WeMail\Rest\Resources\Ecommerce\EDD;

use WeDevs\WeMail\Rest\Resources\JsonResource;

class OrderItemResource extends JsonResource {

    /**
     * @inheritDoc
     */
    public function blueprint( $resource ) {
        $thumbnail = get_the_post_thumbnail_url( $resource['id'], 'thumbnail' );

        return array(
            'id'         => (string) $resource['id'],
            'parent_id'  => null,
            'name'       => $resource['name'],
            'quantity'   => intval( $resource['quantity'] ),
            'total'      => floatval( $resource['price'] ),
            'thumbnail'  => $thumbnail ? $thumbnail : null,
            'categories' => $this->get_categories( $resource['id'] ),
            'permalink'  => get_permalink( $resource['id'] ),
        );
    }

    protected function get_categories( $id ) {
        $terms = get_the_terms( $id, 'download_category' );

        return array_map(
            function ( $term ) {
                return $term->term_id;
            }, is_array( $terms ) ? $terms : array()
        );
    }
}
