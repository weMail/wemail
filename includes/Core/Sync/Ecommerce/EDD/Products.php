<?php

namespace WeDevs\WeMail\Core\Sync\Ecommerce\EDD;

use WeDevs\WeMail\Core\Ecommerce\Requests\Products as ProductsRequest;
use WeDevs\WeMail\Core\Ecommerce\EDD\EDDProducts;
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
        error_log('new product hook class');
        $this->add_action('wp_insert_post', 'wemail_edd_new_to_publish', 10, 3);

        $this->product_request = new ProductsRequest();
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
    public function wemail_edd_new_to_publish( $post_id, $post, $update ) {
        error_log($post_id);
        dd($post_id);
        $integrated = get_option( 'wemail_woocommerce_integrated' );
        $synced     = get_option( 'wemail_is_woocommerce_synced' );
        if ( ! $integrated || ! $synced ) {
            return;
        }

        if ( $post->post_status !== 'publish' || $post->post_type !== 'product' ) {
            return;
        }

        $product = wc_get_product( $post );

        $wc_products = new WCProducts();

        if ( ! $product ) {
            return;
        }

        $is_new = $post->post_date === $post->post_modified;

        if ( $is_new ) {
            $new_arr = [
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
            ];
            $this->product_request->store( $new_arr, $this->source );
        } else {
            $update_arr = [
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
            ];
            $this->product_request->update( $update_arr, $this->source );
        }
    }
}
