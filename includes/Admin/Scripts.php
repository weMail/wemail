<?php
namespace WeDevs\WeMail\Admin;

use WeDevs\WeMail\Framework\Traits\Hooker;

class Scripts {

    use Hooker;

    public function __construct() {
        $this->add_action( 'wemail-admin-enqueue-styles', 'enqueue_styles' );
        $this->add_action( 'wemail-admin-enqueue-scripts', 'enqueue_scripts' );
        $this->add_action( 'wemail-component-partials', 'partials' );
    }

    public function enqueue_styles() {
        wp_enqueue_style( 'wemail', WEMAIL_ASSETS . '/css/wemail.css', [], WEMAIL_VERSION );
    }

    public function enqueue_scripts() {
        wp_enqueue_script( 'wemail-app', WEMAIL_ASSETS . '/js/admin.js', ['wemail-dir-mixins'] , WEMAIL_VERSION, true );

        $this->localized_script();
    }

    public function localized_script() {
        $wemail = wemail()->scripts->localized_script_vars();

        $admin_local_vars = apply_filters( 'weMail-localized-script', [] );

        $wemail = array_merge( $wemail, $admin_local_vars );

        wp_localize_script( 'wemail-vendor', 'weMail', $wemail );
    }

    public function partials($partials) {
        $partials['overview'][] = [
            'tag' => 'div',
            'content' => 'testing {{ startDate }} partials'
        ];

        $partials['hello-world'][] = [
            'tag' => 'div',
            'content' => 'hello world {{ startDate }} partials'
        ];

        return $partials;
    }

}
