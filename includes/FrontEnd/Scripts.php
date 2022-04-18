<?php

namespace WeDevs\WeMail\FrontEnd;

use WeDevs\WeMail\Traits\Hooker;
use WeDevs\WeMail\WeMail;

class Scripts {
    use Hooker;

    public function __construct() {
        $this->add_action( 'wp_enqueue_scripts', 'scripts' );
    }

    public function scripts() {
        $this->enqueue_scripts();
    }

    public function enqueue_scripts() {
        if ( ! is_wemail_hmr_enable() ) {
            wp_register_script( 'wemail-frontend-vendor', wemail()->wemail_cdn . '/build/js/frontend-vendor.js', [ 'jquery' ], WEMAIL_VERSION, true );
            wp_register_script( 'wemail-frontend', wemail()->wemail_cdn . 'build/js/frontend.js', [ 'wemail-frontend-vendor' ], WEMAIL_VERSION, true );
        } else {
            $hmr_host = wemail()->hmr_host();

            wp_register_script( 'wemail-frontend-vendor', $hmr_host . '/src/js/frontend/frontend-vendor.js', [ 'jquery' ], WEMAIL_VERSION, true );
            wp_register_script( 'wemail-frontend', $hmr_host . '/src/js/frontend/frontend.js', [ 'wemail-frontend-vendor' ], WEMAIL_VERSION, true );
        }

        $wemail = [
            'restURL'   => untrailingslashit( get_rest_url( null, '/wemail/v1' ) ),
            'nonce'     => wp_create_nonce( 'wp_rest' ),
            'cdn'       => wemail()->wemail_cdn,
        ];

        wp_localize_script( 'wemail-frontend-vendor', 'weMail', $wemail );

        WeMail::register_module_scripts();
    }

}
