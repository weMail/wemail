<?php

namespace WeDevs\WeMail\Core\Ecommerce\EDD;

use WeDevs\WeMail\Traits\Singleton;

class EDDProducts {

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
            'orderby'  => $args['orderby'] ? $args['orderby'] : 'date',
            'order'    => $args['order'] ? $args['order'] : 'DESC',
            'status'   => $args['status'] ? $args['status'] : 'publish',
            'limit'    => $args['limit'] ? $args['limit'] : 50,
            'paginate' => true,
            'page'     => $args['page'] ? $args['page'] : 1,
        ];

        $a = new \EDD_API();
        dd($a->get_products());

        $posts = get_posts( array(
                'post_type' => 'download',
                'post_status' => 'any',
                'suppress_filters' => $args['limit'],
                'posts_per_page' => -1,
            )
        );

        $products = [];

        $products['current_page'] = intval( $args['page'] );
//        $products['total'] = $collection->total;
//        $products['total_page'] = $collection->max_num_pages;

        foreach ( $posts as $post ) {
            $product = new \EDD_Download( $post->ID );

            $products['data'][] = [
                'id'          => $product->ID,
                'source'      => 'edd',
                'name'        => $product->post_title,
                'slug'        => $product->post_name,
//                'images'      => $this->get_product_images( $product ),
                'status'      => $product->post_status,
                'price'       => $product->price,
                'total_sales' => $product->sales,
//                'rating'      => $product->get_average_rating(),
                'permalink'   => get_permalink( $product->ID ),
                'categories'  => get_the_term_list($product->ID, 'download_category'),
            ];
        }

        return $products;
    }
}
