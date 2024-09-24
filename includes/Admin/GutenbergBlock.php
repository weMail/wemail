<?php

namespace WeDevs\WeMail\Admin;

use WeDevs\WeMail\Traits\Hooker;
use WeDevs\WeMail\Traits\Singleton;

class GutenbergBlock {
    use Singleton;
    use Hooker;

    public function boot() {
        $this->add_action( 'init', 'register' );
        $this->add_action( 'enqueue_block_editor_assets', 'export_forms' );
    }

    public function register() {
        $block_meta = require_once WEMAIL_PATH . '/assets/js/block/index.asset.php';

        wp_register_style( 'wemail-block-style', WEMAIL_ASSETS . '/css/gutenberg.css', array(), WEMAIL_VERSION );
        wp_register_script( 'wemail-block-script', WEMAIL_ASSETS . '/js/block/index.js', $block_meta['dependencies'], $block_meta['version'] );

        register_block_type(
            'wemail/forms',
            array(
				'editor_script' => 'wemail-block-script',
				'editor_style'  => 'wemail-block-style',
			)
        );
    }

    public function export_forms() {
        $forms = wemail()->form->get_forms(
            array(
                'type'      => array( 'modal', 'inline' ),
                'select'    => array( 'id', 'name' ),
            )
        );

        wp_localize_script(
            'wemail-block-script',
            'weMailData',
            array(
                'forms'  => $forms ? $forms : array(),
                'siteUrl' => get_site_url(),
            )
        );
    }
}
