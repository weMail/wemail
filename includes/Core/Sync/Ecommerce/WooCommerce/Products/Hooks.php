<?php

namespace WeDevs\WeMail\Core\Sync\Ecommerce\WooCommerce\Products;

use WeDevs\WeMail\Core\Ecommerce\Requests\Products;
use WeDevs\WeMail\Core\Ecommerce\WooCommerce\WCProducts;
use WeDevs\WeMail\Traits\Hooker;

class Hooks {

    use Hooker;

    protected $productRequest;

    protected $source = 'woocommerce';
    /**
     * Class constructor
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function __construct() {
        $this->add_action( 'save_post', 'wemail_wc_product_updated', 10,3 );

        $this->productRequest = new Products();
    }

    /**
     * Sync new customer
     *
     * @param $post_id
     * @param $post
     * @param $update
     * @return void
     * @since 1.0.0
     */
    public function wemail_wc_product_updated($post_id, $post, $update)
    {
        if ($post->post_status != 'publish' || $post->post_type != 'product') {
            return;
        }

        $product = wc_get_product($post);

        $wcProducts = new WCProducts();

        if (!$product) {
            return;
        }

        $is_new = $post->post_date === $post->post_modified;

        if ( $is_new ) {
            $this->productRequest->store([
                'name'        => $product->get_name(),
                'slug'        => $product->get_slug(),
                'images'      => $wcProducts->get_product_images($product),
                'status'      => $product->get_status(),
                'price'       => $product->get_price(),
                'total_sales' => $product->get_total_sales(),
                'rating'      => $product->get_average_rating(),
                'permalink'   => get_permalink($product->get_id()),
                'categories'  => $wcProducts->get_product_categories($product->get_id())
            ], $this->source);
        } else {
            $this->productRequest->update([
                'name'        => $product->get_name(),
                'slug'        => $product->get_slug(),
                'images'      => $wcProducts->get_product_images($product),
                'status'      => $product->get_status(),
                'price'       => $product->get_price(),
                'total_sales' => $product->get_total_sales(),
                'rating'      => $product->get_average_rating(),
                'permalink'   => get_permalink($product->get_id()),
                'categories'  => $wcProducts->get_product_categories($product->get_id())
            ], $this->source);
        }
    }
}
