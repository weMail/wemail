<?php

namespace WeDevs\WeMail\Core\Ecommerce\EDD;

use WeDevs\WeMail\Traits\Singleton;

class EDDProducts {

    use Singleton;

    /**
     * Get a collection of products
     *
     * @param $args
     * @return array|\WP_Error|\WP_HTTP_Response|\WP_REST_Response
     * @since 1.0.0
     */
    public function all( $args ) {
        $args = [
            'post_type' => 'download',
            'orderby'  => $args['orderby'] ? $args['orderby'] : 'date',
            'order'    => $args['order'] ? $args['order'] : 'DESC',
            'status'   => $args['status'] ? $args['status'] : 'publish',
            'posts_per_page'   => $args['limit'] ? $args['limit'] : 50,
            'paged'     => $args['page'] ? $args['page'] : 1,
        ];

        $product_list = new \WP_Query( $args );

        $api = new \EDD_API();

        $edd_products = [];
        if ( $product_list->posts ) {
            foreach ( $product_list->posts as $product_info ) {
                $edd_products[] = $api->get_product_data( $product_info );
            }
        }

        $products['current_page'] = intval( $args['paged'] );
        $products['total'] = $product_list->found_posts;
        $products['total_page'] = ceil( $product_list->found_posts / $args['posts_per_page'] );
        $products['data'] = null;

        foreach ( $edd_products as $download ) {
            $products['data'][] = [
                'id'          => $download['info']['id'],
                'source'      => 'edd',
                'name'        => $download['info']['title'],
                'slug'        => $download['info']['slug'],
                'images'      => $this->get_images( $download['info']['thumbnail'], $download['info']['title'] ),
                'status'      => $download['info']['status'],
                'price'       => $this->get_price( $download['pricing'] ),
                'total_sales' => $download['stats']['total']['sales'],
                'rating'      => '',
                'permalink'   => get_permalink( $download['info']['id'] ),
                'categories'  => $this->get_formated_categories( $download['info']['category'] ),
            ];
        }

        return $products;
    }

    private function get_images( $thumbnail, $title ) {
        if ( ! $thumbnail ) {
            return null;
        }

        return [
            'id' => '',
            'src' => $thumbnail,
            'title' => $title,
            'alt' => $title,
        ];
    }

    private function get_price( $pricing ) {
        if ( isset( $pricing['amount'] ) ) {
            return $pricing['amount'];
        } elseif ( isset( $pricing['amount'][0] ) ) {
            // getting first price from price variation
            return $pricing['amount'][0];
        } else {
			return 0;
        }
    }

    private function get_formated_categories( $categories ) {
        if ( ! $categories ) {
            return false;
        }

        $result = [];
        foreach ( $categories as $category ) {
            $result[] = [
                'id'    => $category->term_id,
                'name'  => $category->name,
            ];
        }

        return $result;
    }
}
