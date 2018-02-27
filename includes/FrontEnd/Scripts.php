<?php

namespace WeDevs\WeMail\FrontEnd;

use WeDevs\WeMail\Traits\Hooker;

class Scripts {

    private $version;

    use Hooker;

    public function __construct() {
        $this->add_action( 'wp_enqueue_scripts', 'scripts' );
    }

    public function scripts() {
        $this->version = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? time() : WEMAIL_VERSION;

        $this->enqueue_styles();
        $this->enqueue_scripts();
    }

    public function enqueue_styles() {
        // wp_enqueue_style( 'wemail-frontend', $src, $deps, $ver, $media );
    }

    public function enqueue_scripts() {
        wp_enqueue_script( 'wemail-frontend-vendor', wemail()->wemail_cdn . '/js/frontend-vendor.js', ['jquery'], $this->version, true );
        wp_enqueue_script( 'wemail-frontend', wemail()->wemail_cdn . '/js/frontend.js', ['wemail-frontend-vendor'], $this->version, true );

        $wemail = [
            'restURL'   => untrailingslashit( get_rest_url( null, '/wemail/v1') ),
            'nonce'     => wp_create_nonce( 'wp_rest' ),
            'cdn'       => wemail()->wemail_cdn,
        ];

        wp_localize_script( 'wemail-frontend-vendor', 'weMail', $wemail );
    }

}
