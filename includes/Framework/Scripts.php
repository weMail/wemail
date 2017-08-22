<?php
namespace WeDevs\WeMail\Framework;

class Scripts {

    public $script_debug;
    public $suffix;
    public $version;
    public $vendor_dir;

    public function __construct() {
        $this->script_debug = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? true : false;
        $this->suffix       = $this->script_debug ? '' : '.min';
        $this->version      = $this->script_debug ? time() : WEMAIL_VERSION;
        $this->vendor_dir   = WEMAIL_ASSETS . '/vendor';

        $this->register_styles();
        $this->register_scripts();
    }

    private function register_styles() {

    }

    private function register_scripts() {
        wp_register_script( 'wemail-vendor', WEMAIL_ASSETS . '/js/vendor.js', ['jquery'], $this->version, true );
        wp_register_script( 'wemail', WEMAIL_ASSETS . '/js/wemail.js', ['wemail-vendor'], $this->version, true );

        wp_register_script( 'wemail-common', WEMAIL_ASSETS . '/js/common.js', ['wemail', 'jquery-ui-datepicker'] , WEMAIL_VERSION, true );
        do_action('wemail-dir-mixins-after');
    }

    public function localized_script_vars() {
        $time_format = get_option( 'time_format', 'g:i a' );

        $wemail = [
            'nonce'                => wp_create_nonce( 'wemail-nonce' ),
            'ajaxurl'              => admin_url( 'admin-ajax.php' ),
            'scriptDebug'          => $this->script_debug,
            'date'                 => [
                'format'           => wemail_js_date_format(),
                'placeholder'      => wemail_format_date( 'now' )
            ],
            'time'                 => [
                'format'           => $time_format,
                'placeholder'      => date( $time_format )
            ],

            'ajax'                 => function () {}, // function will be render as object
            'api'                  => [
                'rootEndPoint'     => apply_filters( 'wemail-api-root-end-point', 'https://api.wemail.com/' ),
                'key'              => 'apiKey',
            ],

            // Vue related data
            'router'               => function () {},
            'routes'               => apply_filters( 'wemail-admin-register-routes', [] ),
            'routeComponents'      => function () {},
            'component'            => function () {},
            'registeredComponents' => function () {},
            'partials'             => apply_filters( 'wemail-component-partials', [] ),
            'mixins'               => function () {},
            'stores'               => function () {}
        ];

        return $wemail;
    }

}
