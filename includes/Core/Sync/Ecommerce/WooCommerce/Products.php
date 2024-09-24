<?php

namespace WeDevs\WeMail\Core\Sync\Ecommerce\WooCommerce;

use WeDevs\WeMail\Core\Ecommerce\Requests\Products as ProductsRequest;
use WeDevs\WeMail\Core\Ecommerce\WooCommerce\WCProducts;
use WeDevs\WeMail\Traits\Hooker;

class Products {

    use Hooker;

    protected $product_request;

    protected $source = 'woocommerce';
    /**
     * Class constructor
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function __construct() {
        $this->add_action( 'save_post', 'wemail_wc_product_updated', 10, 3 );

        $this->product_request = ProductsRequest::instance();
    }

    /**
     * Sync new product
     *
     * @param $post_id
     * @param $post
     * @param $update
     * @return void
     * @since 1.0.0
     */
    public function wemail_wc_product_updated( $post_id, $post, $update ) {
        $integrated = get_option( 'wemail_woocommerce_integrated' );
        $synced     = get_option( 'wemail_is_woocommerce_synced' );
        if ( ! $integrated || ! $synced ) {
            return;
        }

        if ( $post->post_status !== 'publish' || $post->post_type !== 'product' || ! class_exists( $this->source ) ) {
            return;
        }

        $product = wc_get_product( $post );

        $wc_products = WCProducts::instance();

        if ( ! $product ) {
            return;
        }

        $is_new = $post->post_date === $post->post_modified;

        if ( $is_new ) {
            $new_arr = array(
                'id'          => $product->get_id(),
                'name'        => $product->get_name(),
                'slug'        => $product->get_slug(),
                'images'      => $wc_products->get_product_images( $product ),
                'status'      => $product->get_status(),
                'price'       => $product->get_price(),
                'total_sales' => $product->get_total_sales(),
                'rating'      => $product->get_average_rating(),
                'permalink'   => get_permalink( $product->get_id() ),
                'categories'  => $wc_products->get_product_categories( $product->get_id() ),
            );
            $this->product_request->store( $new_arr, $this->source );
        } else {
            $update_arr = array(
                'id'          => $product->get_id(),
                'name'        => $product->get_name(),
                'slug'        => $product->get_slug(),
                'images'      => $wc_products->get_product_images( $product ),
                'status'      => $product->get_status(),
                'price'       => $product->get_price(),
                'total_sales' => $product->get_total_sales(),
                'rating'      => $product->get_average_rating(),
                'permalink'   => get_permalink( $product->get_id() ),
                'categories'  => $wc_products->get_product_categories( $product->get_id() ),
            );
            $this->product_request->update( $update_arr, $this->source );
        }
    }
}
