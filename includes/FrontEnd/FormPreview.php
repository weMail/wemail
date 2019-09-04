<?php

namespace WeDevs\WeMail\FrontEnd;

use WeDevs\WeMail\Traits\Hooker;

class FormPreview {

    use Hooker;

    protected $form_id;

    protected $version;

    /**
     * FormPreview constructor.
     *
     * @since 0.6.0
     */
    public function __construct() {
        if ( isset( $_GET['wemail_form'] ) ) {
            $this->form_id = $_GET['wemail_form'];
            $this->render_init();
        }
    }

    /**
     *  Initialize Rendering
     *
     * @since 0.6.0
     */
    public function render_init() {
        if ( wp_is_uuid( $this->form_id ) ) {
            $this->version = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? time() : WEMAIL_VERSION;

            $this->add_action('wp_head', 'import_head_data');
            $this->enqueue_scripts();

            $this->add_filter('the_content', 'render_form_component');
        }
    }

    /**
     *  Rendering Form Component
     *
     * @since 0.6.0
     * @return string
     */
    public function render_form_component() {

        return sprintf('<div id="preview-wemail-form"><wemail-form-preview id="%s"/></div>', $this->form_id);
    }

    /**
     *  Enqueue Scripts
     *
     * @since 0.6.0
     * @return void
     */
    public function enqueue_scripts() {
        wp_register_script( 'wemail-preview', wemail()->wemail_cdn . '/js/preview.js', ['wemail-frontend-vendor'], $this->version, true );

        wp_enqueue_script('wemail-frontend-vendor');
        wp_enqueue_script('wemail-preview');
    }

    /**
     *  Importing Style and Form Object
     *
     * @since 0.6.0
     */
    public function import_head_data() {
        ?>
        <style>
            #wpadminbar {
                display: none;
            }
            header,
            footer{
                display: none;
            }

            #preview-wemail-form {
                z-index: 9001;
                position: fixed;
                top: 0;
                left: 0;
                width: 100vw;
                height: 100vh;
                background-color: white;
                display: block !important;
            }
        </style>
        <script>
            var wemailForm = <?php echo json_encode(wemail()->form->get($this->form_id));?>
        </script>
        <?php
    }
}
