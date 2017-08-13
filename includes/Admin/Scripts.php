<?php
namespace WeDevs\WeMail\Admin;

use WeDevs\WeMail\Framework\Traits\Hooker;

class Scripts {

    use Hooker;

    public function __construct() {
        $this->add_action( 'wemail-admin-enqueue-styles', 'enqueue_styles' );
        $this->add_action( 'wemail-admin-enqueue-scripts', 'enqueue_scripts' );
    }

    public function enqueue_styles() {
        wp_enqueue_style( 'wemail', WEMAIL_ASSETS . '/css/wemail.css', [], WEMAIL_VERSION );
    }

    public function enqueue_scripts() {
        wp_enqueue_script( 'wemail-dir-mixins', WEMAIL_ASSETS . '/js/wemail-directive-mixins.js', ['wemail'] , WEMAIL_VERSION, true );

        do_action('wemail-dir-mixins-after');

        wp_enqueue_script( 'wemail-app', WEMAIL_ASSETS . '/js/wemail-app.js', ['jquery-ui-datepicker', 'wemail-dir-mixins'] , WEMAIL_VERSION, true );


        $this->localized_script();
    }

    public function localized_script() {
        $wemail = wemail()->scripts->localized_script_vars();

        $admin_local_vars = apply_filters( 'weMail-localized-script', [] );

        $wemail = array_merge( $wemail, $admin_local_vars );

        wp_localize_script( 'wemail', 'weMail', $wemail );
    }

}
