<?php
namespace WeDevs\WeMail\Admin;

use WeDevs\WeMail\Framework\Traits\Hooker;

class Scripts {

    use Hooker;

    public function __construct() {
        $this->add_action( 'wemail_admin_enqueue_styles', 'enqueue_styles' );
        $this->add_action( 'wemail_admin_enqueue_scripts', 'enqueue_scripts' );
    }

    public function enqueue_styles() {
        wp_enqueue_style( 'wemail-admin', WEMAIL_ASSETS . '/css/admin.css', [], WEMAIL_VERSION );
    }

    public function enqueue_scripts() {
        wp_enqueue_script( 'wemail-admin', WEMAIL_ASSETS . '/js/admin.js', ['wemail-common'] , WEMAIL_VERSION, true );

        $this->localized_script();
    }

    public function localized_script() {
        wp_enqueue_media();

        $wemail = wemail()->core->scripts->localized_script_vars();

        $admin_local_vars = apply_filters( 'weMail_localized_script', [] );

        $wemail = array_merge( $wemail, $admin_local_vars );

        wp_localize_script( 'wemail-vendor', 'weMail', $wemail );
    }

}
