<?php

namespace WeDevs\WeMail\Admin;

use WeDevs\WeMail\Traits\Hooker;

class FormPreview {

    use Hooker;

    protected $form_id;

    /**
     * FormPreview constructor.
     *
     * @since 0.6.0
     */
    public function __construct() {
        wemail_set_owner_api_key( false );

        $this->add_action( 'wp_ajax_wemail_preview', 'render_form_component' );

        if ( isset( $_GET['action'], $_GET['form_id'] ) ) {
            $this->form_id = sanitize_text_field( wp_unslash( $_GET['form_id'] ) );
        }
    }


    /**
     *  Rendering Form Component
     *
     * @since 0.6.0
     * @return string
     */
    public function render_form_component() {
        global $wp_scripts;
        $form = wemail_form( $this->form_id, true );
        ?>
        <html <?php language_attributes(); ?>>
            <head>
                <title><?php esc_html_e( 'weMail Form Preview', 'wemail' ); ?></title>
                <link rel="stylesheet" href="<?php /** phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet */ echo get_stylesheet_directory_uri() . '/style.css'; ?>">
                <link rel="stylesheet" href="<?php /** phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet */ echo wemail()->wemail_cdn . '/build/css/Form.css?ver=' . WEMAIL_VERSION; ?>">
                <script>
                    var weMail = {
                        'restURL': '<?php echo untrailingslashit( get_rest_url( null, '/wemail/v1' ) ); ?>',
                        'nonce': '<?php echo wp_create_nonce( 'wp_rest' ); ?>',
                        'cdn': '<?php echo wemail()->wemail_cdn; ?>'
                    };
                    var wemailForm = <?php echo wp_json_encode( $form ); ?>
                </script>
            </head>
            <body>
                <div>
                    <?php echo sprintf( '<div id="preview-wemail-form" data-form-type="%s"><wemail-form-preview id="%s"/></div>', $form['type'], $this->form_id ); ?>
                </div>

                <script src="<?php /** phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedScript */ echo get_site_url() . $wp_scripts->registered['jquery-core']->src; ?>"></script>
                <script type="module" src="<?php /** phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedScript */ echo wemail()->wemail_cdn . '/build/js/frontend-vendor.js?ver=' . WEMAIL_VERSION; ?>"></script>
                <script type="module" src="<?php /** phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedScript */ echo wemail()->wemail_cdn . '/build/js/frontend.js?ver=' . WEMAIL_VERSION; ?>"></script>
                <script type="module" src="<?php /** phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedScript */ echo wemail()->wemail_cdn . '/build/js/preview.js?ver=' . WEMAIL_VERSION; ?>"></script>
            </body>
        </html>

        <?php
        exit( 0 );
    }
}
