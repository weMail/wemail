<?php

namespace WeDevs\WeMail\Core\Ecommerce\WooCommerce;

use WeDevs\WeMail\Traits\Singleton;

class WCProducts {

    use Singleton;

    /**
     * Get a collection of orders
     *
     * @param $args
     * @return array|\WP_Error|\WP_HTTP_Response|\WP_REST_Response
     * Details: https://www.businessbloomer.com/woocommerce-easily-get-product-info-title-sku-desc-product-object/
     * @since 1.0.0
     */
    public function all( $args ) {
        $args = [
            'exclude'  => $args['last_synced_id'] ? range( 1, $args['last_synced_id'] ) : null,
            'orderby'  => $args['orderby'] ? $args['orderby'] : 'date',
            'order'    => $args['order'] ? $args['order'] : 'DESC',
            'status'   => $args['status'] ? $args['status'] : 'publish',
            'limit'    => $args['limit'] ? $args['limit'] : 50,
            'paginate' => true,
            'page'     => $args['page'] ? $args['page'] : 1,
        ];

        $collection = wc_get_products( $args );

        $products = [];

        $products['current_page'] = intval( $args['page'] );
        $products['total'] = $collection->total;
        $products['total_page'] = $collection->max_num_pages;

        foreach ( $collection->products as $product ) {
            $id = $product->get_id();
            $categories = $this->get_product_categories( $id );

            $products['data'][] = [
                'id'          => $id,
                'source'      => 'woocommerce',
                'name'        => $product->get_name(),
                'slug'        => $product->get_slug(),
                'images'      => $this->get_product_images( $product ),
                'status'      => $product->get_status(),
                'price'       => $product->get_price(),
                'total_sales' => $product->get_total_sales(),
                'rating'      => $product->get_average_rating(),
                'permalink'   => get_permalink( $product->get_id() ),
                'categories'  => $categories,
            ];
        }

        return $products;
    }

    /**
     * Get a single campaign
     *
     * @param string $id
     * @return array|false|\WC_Product
     * @since 1.0.0
     */
    public function get( $id ) {
        return wc_get_product( $id );
    }

    /**
     * @param $product
     * @return array
     */
    public function get_product_images( $product ) {
        $attachment_ids = array();
        $images = $attachment_ids;
        $product_image = $product->get_image_id();
        // Add featured image.
        if ( ! empty( $product_image ) ) {
            $attachment_ids[] = $product_image;
        }
        // add gallery images.
        $attachment_ids = array_merge( $attachment_ids, $product->get_gallery_image_ids() );

        $images = [];
        foreach ( $attachment_ids as $attachment_id ) {
            $attachment_post = get_post( $attachment_id );
            if ( is_null( $attachment_post ) ) {
                continue;
            }
            $attachment = wp_get_attachment_image_src( $attachment_id, 'full' );
            if ( ! is_array( $attachment ) ) {
                continue;
            }
            $images[] = [
                'id' => (int) $attachment_id,
                'src' => current( $attachment ),
                'title' => get_the_title( $attachment_id ),
                'alt' => get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ),
            ];
        }
        // Set a placeholder image if the product has no images set.
        if ( empty( $images ) ) {
            $images[] = [
                'id' => 0,
                'src' => wc_placeholder_img_src(),
                'title' => __( 'Placeholder', 'wemail' ),
                'alt' => __( 'Placeholder', 'wemail' ),
            ];
        }

        return $images;
    }

    /**
     * ORDER PRODUCT CATEGORIES
     * @param $product_id
     * @return array
     */
    public function get_product_categories( $product_id ) {
        $product_cats_ids = wc_get_product_term_ids( $product_id, 'product_cat' );
        $categories = [];
        foreach ( $product_cats_ids as $cat_id ) {
            $term = get_term_by( 'id', $cat_id, 'product_cat' );

            $categories[] = [
                'id' => $cat_id,
                'name' => $term->name,
            ];
        }

        return $categories;
    }

}
