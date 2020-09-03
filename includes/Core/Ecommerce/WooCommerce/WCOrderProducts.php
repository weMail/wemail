<?php

namespace WeDevs\WeMail\Core\Ecommerce\WooCommerce;

use WeDevs\WeMail\Traits\Singleton;

class WCOrderProducts {

    use Singleton;

    /**
     * ORDER PRODUCT CATEGORIES
     * @param $productId
     * @return array
     */
    public function get_product_categories( $productId ) {
        $product_cats_ids = wc_get_product_term_ids( $productId, 'product_cat' );
        $categories = [];
        foreach( $product_cats_ids as $cat_id ) {
            $term = get_term_by( 'id', $cat_id, 'product_cat' );

            $categories[] = [
                'id' => $cat_id,
                'name' => $term->name,
            ];;
        }

        return $categories;
    }

    /**
     * GET ORDER PRODUCTS
     * @param $order_obj
     * @return array
     */
    public function get_ordered_products( $order_obj ) {
        $wcProduct = new WCProducts();

        $items = $order_obj->get_items();
        $products = [];
        foreach ( $items as $item ) {
            $productId = $item->get_product_id();
            $product = new \WC_Product($item->get_product_id());

            $categories = $this->get_product_categories( $productId );

            $products[] = [
                'id'           => $product->get_id(),
                'product_id'   => $item->get_product_id(),
                'name'         => $product->get_name(),
                'slug'         => $product->get_slug(),
                'total'        => $item->get_total(),
                'quantity'     => $item->get_quantity(),
                'images'       => $wcProduct->get_product_images($product),
                'categories'   => $categories
            ];
        }

        return $products;
    }
}
