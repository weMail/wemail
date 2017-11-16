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
        wp_register_style( 'wemail-jquery-ui', WEMAIL_ASSETS . '/vendor/jquery-ui/jquery-ui.min.css', [], $this->version );
        wp_register_style( 'wemail-timepicker', WEMAIL_ASSETS . '/vendor/jquery-timepicker/jquery.timepicker.min.css', [], $this->version );
        wp_register_style( 'wemail-tiny-mce', site_url( '/wp-includes/css/editor.css' ), ['wp-color-picker'], $this->version );
    }

    private function register_scripts() {
        wp_register_script( 'wemail-tiny-mce', site_url( '/wp-includes/js/tinymce/tinymce.min.js' ), [] );
        wp_register_script( 'wemail-tiny-mce-code', WEMAIL_ASSETS . '/vendor/tinymce/plugins/code/plugin.min.js', [ 'wemail-tiny-mce' ], $this->version, true );
        wp_register_script( 'wemail-tiny-mce-hr', WEMAIL_ASSETS . '/vendor/tinymce/plugins/hr/plugin.min.js', [ 'wemail-tiny-mce-code' ], $this->version, true );

        wp_register_script( 'wemail-vendor', WEMAIL_ASSETS . '/js/vendor.js', ['jquery'], $this->version, true );

        wp_register_script( 'wemail-moment', WEMAIL_ASSETS . '/vendor/moment/moment.min.js', [], $this->version, true );
        wp_register_script( 'wemail-moment-timezone', WEMAIL_ASSETS . '/vendor/moment/moment-timezone-with-data-2012-2022.min.js', ['wemail-moment'], $this->version, true );

        wp_register_script( 'wemail', WEMAIL_ASSETS . '/js/wemail.js', ['wemail-vendor', 'wemail-moment', 'wemail-moment-timezone'], $this->version, true );

        wp_register_script( 'wemail-timepicker', WEMAIL_ASSETS . '/vendor/jquery-timepicker/jquery.timepicker.min.js', [], $this->version, true );
        wp_register_script( 'wemail-popper-js', WEMAIL_ASSETS . '/vendor/popper.js/popper.min.js', [], $this->version, true );
        wp_register_script( 'wemail-bootstrap', WEMAIL_ASSETS . '/js/bootstrap.js', ['jquery', 'wemail-popper-js'], $this->version, true );

        wp_register_script( 'wemail-common', WEMAIL_ASSETS . '/js/common.js', ['wemail', 'jquery-ui-datepicker', 'jquery-ui-sortable', 'jquery-ui-draggable', 'wemail-timepicker', 'wemail-bootstrap'] , $this->version, true );
    }

    public function localized_script_vars() {
        /**
         * weMail CDN url
         *
         * @since 1.0.0
         *
         * @param string
         */
        $cdn = apply_filters( 'wemail_cdn_root', 'https://cdn.wemail.com' );
        $cdn = untrailingslashit( $cdn );

        $time_format = get_option( 'time_format', 'g:i a' );

        $wemail = [
            'nonce'                => wp_create_nonce( 'wemail-nonce' ),
            'siteURL'              => site_url('/'),
            'ajaxurl'              => admin_url( 'admin-ajax.php' ),
            'assetsURL'            => WEMAIL_ASSETS,
            'scriptDebug'          => $this->script_debug,
            'dateTime'             => [
                'server'           => [
                    'timezone'     => wemail_get_wp_timezone(),
                    'date'         => current_time( 'Y-m-d' ),
                    'time'         => current_time( 'H:i:s' ),
                    'dateFormat'   => get_option( 'date_format', 'Y-m-d' ),
                    'timeFormat'   => get_option( 'time_format', 'g:i a' ),
                    'startOfWeek'  => get_option( 'start_of_week', 0 ),
                ],
                'toMoment'         => '',
                'momentDateFormat' => '',
                'momentTimeFormat' => '',
            ],

            'ajax'                 => function () {}, // function will be render as object
            'api'                  => wemail()->api->get_props(),
            'cdn'                  => $cdn,
            'userCaps'             => [ // hard coded for now
                // subscriber
                'create_subscriber' => true,
                'view_subscriber'   => true,
                'edit_subscriber'   => true,
                'delete_subscriber' => true,

                // form
                'create_form'       => true,
                'view_form'         => true,
                'edit_form'         => true,
                'delete_form'       => true,

                // campaigns
                'create_campaign'   => true,
                'view_campaign'     => true,
                'edit_campaign'     => true,
                'delete_campaign'   => true,
            ],

            // Vue related data
            'subMenuMap'            => [],
            'mixins'                => function () {},
            'stores'                => function () {},
            'actions'               => apply_filters( 'wemail_component_actions', [] ),
            'customizerIframe'      => WEMAIL_URL . '/views/customizer.html',
            'lists'                 => wemail()->lists->items(),
            'i18n'                  => [
                'selectLists'       => __( 'Select Lists', 'wemail' ),
                'noListFound'       => __( 'No List Found', 'wemail' ),
                'cancel'            => __( 'Cancel', 'wemail' ),
                'addImage'          => __( 'Add Image', 'wemail' ),
                'missingImgMethod'  => __( 'Component is missing onSelectImages method', 'wemail' ),
                'ok'                => __( 'OK', 'wemail' ),
                'cancel'            => __( 'Cancel', 'wemail' ),
                'save'              => __( 'Save', 'wemail' )
            ]
        ];

        return $wemail;
    }

}
