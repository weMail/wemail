<?php

namespace WeDevs\WeMail\Core\Sync\Ecommerce\EDD;

use WeDevs\WeMail\Core\Ecommerce\Requests\Products as ProductsRequest;
use WeDevs\WeMail\Traits\Hooker;

class Products {

    use Hooker;

    protected $product_request;

    protected $source = 'edd';
    /**
     * Class constructor
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function __construct() {
        $this->add_action( 'post_updated', 'wemail_edd_wp_update_post' );

        $this->product_request = ProductsRequest::instance();
    }

    /**
     * Sync new product
     *
     * @param $post_id
     * @return void
     * @since 1.0.0
     */

    public function wemail_edd_wp_update_post( $post_id ) {
        if ( ! class_exists( 'Easy_Digital_Downloads' ) ) {
            return;
        }

        $integrated = get_option( 'wemail_edd_integrated' );
        $synced     = get_option( 'wemail_is_edd_synced' );
        if ( ! $integrated || ! $synced ) {
            return;
        }

        $download = new \EDD_Download( $post_id );
        if ( ! $download || $download->post_type !== 'download' ) {
            return;
        }

        $is_new = $download->post_date === $download->post_modified;

        $product = [
            'id'          => $download->ID,
            'name'        => $download->post_title,
            'slug'        => $download->post_name,
            'images'      => $this->get_download_images( $post_id ),
            'status'      => $download->post_status,
            'price'       => $download->get_price(),
            'total_sales' => $download->get_sales(),
            'rating'      => '',
            'permalink'   => get_permalink( $download->ID ),
            'categories'  => $this->get_download_categories( $download->ID ),
        ];

        if ( $is_new ) {
            $this->product_request->store( $product, $this->source );
        } else {
            $this->product_request->update( $product, $this->source );
        }
    }

    /**
     * ORDER PRODUCT CATEGORIES
     * @param $download_id
     * @return array
     */
    public function get_download_categories( $download_id ) {
        $terms = wp_get_post_terms( $download_id, 'download_category', [ 'fields' => 'all' ] );

        $categories = [];
        foreach ( $terms as $term ) {
            $categories[] = [
                'id'    => $term->term_id,
                'name'  => $term->name,
            ];
        }

        return $categories;
    }

    public function get_download_images( $post_id ) {
        $attachment_id = get_post_thumbnail_id( $post_id );

        $src = wp_get_attachment_image_url( $attachment_id, 'thumbnail' );

        return [
            'id' => (int) $attachment_id,
            'src' => $src,
            'title' => get_the_title( $attachment_id ),
            'alt' => get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ),
        ];
    }
}
