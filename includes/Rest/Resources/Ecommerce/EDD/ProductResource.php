<?php

namespace WeDevs\WeMail\Rest\Resources\Ecommerce\EDD;

use EDD_Download;
use WeDevs\WeMail\Rest\Resources\JsonResource;
use WP_Post;

class ProductResource extends JsonResource {

    /**
     * @inheritDoc
     */
    public function blueprint( $resource ) {
        /** @var WP_Post $resource */

        $download = new EDD_Download( $resource->ID );

        return [
            'id'         => (string) $resource->ID,
            'parent_id'  => (string) $resource->post_parent,
            'name'       => $resource->post_title,
            'price'      => floatval( $download->get_price() ),
            'status'     => $resource->post_status,
            'permalink'  => get_permalink( $resource ),
            'thumbnail'  => $this->get_thumbnail( $resource ),
            'categories' => $this->get_categories( $resource->ID ),
            'source'     => 'edd',
        ];
    }

    /**
     * Get thumbnail image url
     *
     * @param WP_Post $download
     *
     * @return string|null
     */
    protected function get_thumbnail( WP_Post $download ) {
        $image = get_the_post_thumbnail_url( $download );

        return $image ? $image : null;
    }

    /**
     * Get categories ID
     *
     * @param $id
     *
     * @return int[]
     */
    protected function get_categories( $id ) {
        $terms = get_the_terms( $id, 'download_category' );

        return array_map(
            function ( $term ) {
                return $term->term_id;
            }, is_array( $terms ) ? $terms : []
        );
    }
}
