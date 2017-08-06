<?php
namespace WeDevs\WeMail;

use WeDevs\WeMail\Framework\Traits\Hooker;

class Scripts {

    use Hooker;

    public $is_script_debug_on;
    public $suffix;
    public $version;
    public $vendor_dir;

    public function __construct() {
        // @TODO Refactor this to reuse
        $this->is_script_debug_on = ( defined( 'SCRIPT_DEBUG') && SCRIPT_DEBUG ) ? true : false;
        $this->suffix             = $this->is_script_debug_on ? '' : '.min';
        $this->version            = $this->is_script_debug_on ? time() : WEMAIL_VERSION;
        $this->vendor_dir         = WEMAIL_ASSETS . '/vendor';

        $this->register_styles();
        $this->register_scripts();

        $this->filter( 'weMail-localized-script', 'localized_script' );
    }

    private function register_styles() {

    }

    private function register_scripts() {
        wp_register_script( 'vue', $this->vendor_dir . '/vue/vue' . $this->suffix . '.js', [], $this->version, true );
        wp_register_script( 'vuex', $this->vendor_dir . '/vue/vuex' . $this->suffix . '.js', ['vue'], $this->version, true );
        wp_register_script( 'vue-router', $this->vendor_dir . '/vue/vue-router' . $this->suffix . '.js', ['vue'], $this->version, true );
    }

    public function localized_script( $wemail ) {
        $time_format = get_option( 'time_format', 'g:i a' );

        $vars = [
            'nonce'             => wp_create_nonce( 'wemail-nonce' ),
            'ajaxurl'           => admin_url( 'admin-ajax.php' ),
            'scriptDebug'       => $this->is_script_debug_on,
            'date'              => [
                'format'        => wemail_js_date_format(),
                'placeholder'   => wemail_format_date( 'now' )
            ],
            'time'              => [
                'format'        => $time_format,
                'placeholder'   => date( $time_format )
            ],
        ];

        return array_merge( $wemail, $vars );
    }

}
