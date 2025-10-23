<?php
namespace WeDevs\WeMail\Admin;

use WeDevs\WeMail\Core\Ecommerce\Ecommerce;
use WeDevs\WeMail\Core\Ecommerce\Settings;
use WeDevs\WeMail\Traits\Hooker;
use WeDevs\WeMail\WeMail;

class Scripts {

    use Hooker;

    /**
     * Holds the $version param value for enqueue scripts/styles
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $version;

    /**
     * Class constructor
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function __construct() {
        $this->version = WEMAIL_VERSION;

        $this->add_action( 'wemail_admin_enqueue_styles', 'enqueue_styles' );
        $this->add_action( 'wemail_admin_enqueue_scripts', 'enqueue_scripts' );
    }

    /**
     * Enqueue admin styles
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function enqueue_styles() {
        wp_register_style( 'wemail-jquery-ui', wemail()->wemail_cdn . '/vendor/jquery-ui/jquery-ui.min.css', array(), $this->version );
        wp_register_style( 'wemail-timepicker', wemail()->wemail_cdn . '/vendor/jquery-timepicker/jquery.timepicker.min.css', array(), $this->version );
        wp_register_style( 'wemail-tiny-mce', site_url( '/wp-includes/css/editor.css' ), array(), $this->version );

        $dependencies = array(
            'wemail-jquery-ui',
            'wemail-timepicker',
            'wemail-tiny-mce',
        );

        wp_enqueue_style( 'wemail', wemail()->wemail_cdn . '/build/css/wemail.css', $dependencies, $this->version );
    }

    /**
     * Enqueue admin js
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function enqueue_scripts() {
        wp_enqueue_media();

        wp_register_script( 'wemail-tiny-mce', site_url( '/wp-includes/js/tinymce/tinymce.min.js' ), array(), true );
        wp_register_script( 'wemail-tiny-mce-code', wemail()->wemail_cdn . '/vendor/tinymce/plugins/code/plugin.min.js', array( 'wemail-tiny-mce' ), $this->version, true );
        wp_register_script( 'wemail-tiny-mce-hr', wemail()->wemail_cdn . '/vendor/tinymce/plugins/hr/plugin.min.js', array( 'wemail-tiny-mce-code' ), $this->version, true );

        wp_register_script( 'wemail-moment', wemail()->wemail_cdn . '/vendor/moment/moment.min.js', array(), $this->version, true );
        wp_register_script( 'wemail-moment-timezone', wemail()->wemail_cdn . '/vendor/moment/moment-timezone-with-data-2012-2022.min.js', array( 'wemail-moment' ), $this->version, true );

        wp_register_script( 'wemail-timepicker', wemail()->wemail_cdn . '/vendor/jquery-timepicker/jquery.timepicker.min.js', array( 'jquery' ), $this->version, true );
        wp_register_script( 'wemail-popper-js', wemail()->wemail_cdn . '/vendor/popper.js/popper.min.js', array(), $this->version, true );

        $dependencies = array(
            'jquery',
            'jquery-ui-datepicker',
            'jquery-ui-sortable',
            'jquery-ui-draggable',
            'wemail-tiny-mce',
            'wemail-tiny-mce-code',
            'wemail-tiny-mce-hr',
            'wemail-moment',
            'wemail-moment-timezone',
            'wemail-timepicker',
            'wemail-popper-js',
            'wemail-bootstrap',
        );

        if ( ! is_wemail_hmr_enable() ) {
            wp_register_script( 'wemail-bootstrap', wemail()->wemail_cdn . '/build/js/bootstrap.js', array( 'jquery', 'wemail-popper-js' ), $this->version, true );
            wp_enqueue_script( 'wemail-vendor', wemail()->wemail_cdn . '/build/js/vendor.js', $dependencies, $this->version, true );
            wp_enqueue_script( 'wemail', wemail()->wemail_cdn . '/build/js/wemail.js', array( 'wemail-vendor' ), $this->version, true );
        } else {
            $hmr_host = wemail()->hmr_host();

            wp_register_script( 'wemail-bootstrap', $hmr_host . '/src/vendor/bootstrap/js/bootstrap.js', array( 'jquery', 'wemail-popper-js' ), $this->version, true );
            wp_enqueue_script( 'wemail-vendor', $hmr_host . '/src/js/vendor.js', $dependencies, $this->version, true );
            wp_enqueue_script( 'wemail', $hmr_host . '/src/js/wemail.js', array( 'wemail-vendor' ), $this->version, true );
        }

        $user = wemail()->user;

        $date_format = get_option( 'date_format', 'Y-m-d' );
        $time_format = get_option( 'time_format', 'g:i a' );

        $date_format = trim( $date_format );
        $time_format = trim( $time_format );
        $current = wp_get_current_user();
        $current_user = $current && $current->data ? $current->data : null;

        $wemail = array(
            'version'              => wemail()->version,
            'siteURL'              => site_url( '/' ),
            'homeURL'              => home_url( '/' ),
            'restURL'              => untrailingslashit( get_rest_url( null, '/wemail/v1' ) ),
            'wpRestURL'            => untrailingslashit( get_rest_url( null, '/wp/v2' ) ),
            'nonce'                => wp_create_nonce( 'wp_rest' ),
            'wemailSiteURL'        => wemail()->wemail_site,
            'api'                  => wemail()->api->get_props(),
            'cdn'                  => wemail()->wemail_cdn,
            'app'                  => wemail()->wemail_app,
            'dateTime'             => array(
                'server'           => array(
                    'timezone'     => wemail_get_wp_timezone(),
                    'date'         => current_time( 'Y-m-d' ),
                    'time'         => current_time( 'H:i:s' ),
                    'dateFormat'   => $date_format ? $date_format : 'Y-m-d',
                    'timeFormat'   => $time_format ? $time_format : 'g:i a',
                    'startOfWeek'  => get_option( 'start_of_week', 0 ),
                ),
                'toMoment'         => '',
                'momentDateFormat' => '',
                'momentTimeFormat' => '',
            ),

            'user'                 => array(
                'hash'             => $user->hash,
                'role'             => $user->role,
                'allowed'          => $user->allowed,
                // 'permissions'      => $user->permissions,
            ),
            'currentUser' => $current_user ? array(
                'name' => $current_user->display_name,
                'email' => $current_user->user_email,
                'api_key'  => (bool)get_user_meta($current_user->ID, 'wemail_api_key', true),
            ) : null,

            // Vue related data
            'customizerIframe'      => WEMAIL_URL . '/views/customizer.html',
            'locale_data'           => $this->get_locale_data(),
            'hideAdminNoticesIn'    => array(
                'campaignEditDesign',
            ),
            'activeIntegrations'    => $this->active_integrations(),
            'integrations' => array(
                'woocommerce' => array(
                    'is_active' => Ecommerce::instance()->platform( 'woocommerce' )->is_active(),
                    'is_integrated' => Ecommerce::instance()->platform( 'woocommerce' )->is_integrated(),
                ),
                'edd' => array(
                    'is_active' => Ecommerce::instance()->platform( 'edd' )->is_active(),
                    'is_integrated' => Ecommerce::instance()->platform( 'edd' )->is_integrated(),
                ),
            ),
            'site_name' => get_bloginfo( 'name' ),
        );

        wp_localize_script( 'wemail-vendor', 'weMail', $wemail );

        WeMail::register_module_scripts();
    }

    /**
     * Load the translation strings
     *
     * Src: https://github.com/WordPress/gutenberg/blob/25a2003c7faac2dcdb723161fefd10ef44d147b6/lib/i18n.php#L12
     *
     * @since 1.0.0
     *
     * @return array
     */
    private function get_locale_data() {
        $domain = wemail()->text_domain;

        $translations = get_translations_for_domain( $domain );

        $locale = array(
            'domain'      => $domain,
            'locale_data' => array(
                $domain => array(
                    '' => array(
                        'domain' => $domain,
                        'lang'   => is_admin() ? get_user_locale() : get_locale(),
                    ),
                ),
            ),
        );

        if ( ! empty( $translations->headers['Plural-Forms'] ) ) {
            $locale['locale_data'][ $domain ]['']['plural_forms'] = $translations->headers['Plural-Forms'];
        }

        foreach ( $translations->entries as $msgid => $entry ) {
            $locale['locale_data'][ $domain ][ $msgid ] = $entry->translations;
        }

        return $locale;
    }

    /**
     * Active integration reference
     *
     * @since 1.0.0
     *
     * @return array
     */
    private function active_integrations() {
        return array(
            'erp_crm'  => is_erp_crm_active(),
            'mailpoet' => class_exists( 'MailPoet\Listing\Handler' ) || class_exists( 'WYSIJA' ),
        );
    }
}
