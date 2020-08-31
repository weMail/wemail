<?php

namespace WeDevs\WeMail\Core\Ecommerce\WooCommerce;

use WeDevs\WeMail\Traits\Singleton;

class WCProducts {

    use Singleton;

    /**
     * Get a collection of orders
     *
     * @return array|\WP_Error|\WP_HTTP_Response|\WP_REST_Response
     * Details: https://www.businessbloomer.com/woocommerce-easily-get-product-info-title-sku-desc-product-object/
     * @since 1.0.0
     */
    public function all( ) {
        $args = [
            'orderby' => 'date',
            'order'   => 'DESC',
            'status'  => 'publish'
        ];

        $collection = wc_get_products( $args );

        $products = [];

        foreach ($collection as $product) {
            $products[] = [
                'id'        => $product->get_id(),
                'slug'      => $product->get_slug(),
                'name'     => $product->get_name(),
                'status'    => $product->get_status(),
                'price'     => $product->get_price(),
                'image'     => $product->get_image(),
                'permalink' => get_permalink($product->get_id())
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
        return wc_get_product ($id);
    }

}
