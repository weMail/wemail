<?php

namespace WeDevs\WeMail\Core\Ecommerce\Platforms;

use WeDevs\WeMail\Core\Ecommerce\Settings;
use WeDevs\WeMail\Traits\Singleton;

class WooCommerce implements PlatformInterface {
    use Singleton;

    public function currency() {
        return get_woocommerce_currency();
    }

    public function currency_symbol() {
        return get_woocommerce_currency_symbol();
    }

    public function products() {

    }

    public function orders() {

    }

    public function customers() {

    }

    /**
     * Register post update hooks
     */
    public function register_hooks() {
        if (! class_exists('WooCommerce' ) ) {
            return;
        }

        add_action( 'save_post', [ $this, 'handle' ], 10, 3 );
    }

    /**
     * Handle the action
     *
     * @param $post_id
     * @param \WP_Post $post
     * @param $is_updated
     */
    public function handle( $post_id, \WP_Post $post, $is_updated ) {
        // Check if the post is order or product
        if (! preg_match( '/shop_order|product/', $post->post_type ) ) {
            return;
        }
        // Check if order completed or product published
        if (! preg_match( '/wc-completed|publish/', $post->post_status ) ) {
            return;
        }

        if (! Settings::instance()->is_enabled() ) {
            return;
        }

        call_user_func_array([$this, 'sync_'. $post->post_type], [$post, $is_updated]);
    }

    public function sync_shop_order( \WP_Post $order, $is_updated ) {

    }

    /**
     * Sync single product with weMail
     *
     * @param \WP_Post $post
     * @param $is_updated
     */
    public function sync_product( \WP_Post $post, $is_updated ) {
        $api = wemail()->api->ecommerce();
        $product = wc_get_product($post->ID);

        $payload = [
            'source' => 'woocommerce',
            'price' => $product->get_price(),
            'name' => $product->get_name(),
            'status' => $product->get_status(),
            'image' => wp_get_attachment_image_url( $product->get_image_id(), 'woocommerce_thumbnail' ),
            'permalink' => $product->get_permalink()
        ];

        if ( $is_updated ) {
            $api->products($post->ID)->put($payload);
        } else {
            $api->products()->post(array_merge($payload, ['id' => $post->ID]));
        }
    }
}
