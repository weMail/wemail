<?php
namespace WeDevs\WeMail\Admin;

use WeDevs\WeMail\Framework\Traits\Hooker;

class Scripts {

    use Hooker;

    public function __construct() {
        $this->action( 'wemail-admin-enqueue-styles', 'enqueue_styles' );
        $this->action( 'wemail-admin-enqueue-scripts', 'enqueue_scripts' );
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

        $admin_local_vars = apply_filters( 'weMail-localized-script', [
            'routes' => apply_filters( 'wemail-admin-vue-routes', [
                [
                    'path' => '/',
                    'name' => 'home',
                    'component' => 'Home',
                    'requires' => WEMAIL_ASSETS . '/js/Home.js',
                    'scrollTo' => 'top'
                ],
                [
                    'path' => '/campaigns',
                    'name' => 'campaigns',
                    'component' => 'Campaigns',
                    'requires' => WEMAIL_ASSETS . '/js/Campaigns.js',
                    'scrollTo' => 'top'
                ],
                [
                    'path' => '/subscribers',
                    'name' => 'subscribers',
                    'component' => 'Subscribers',
                    'requires' => WEMAIL_ASSETS . '/js/Subscribers.js',
                    'scrollTo' => 'top'
                ],
            ] ),

            // Without an empty default value, `routeComponents` will be an
            // array instead of object
            'routeComponents' => [
                'default' => null
            ]
        ] );

        $wemail = array_merge( $wemail, $admin_local_vars );

        wp_localize_script( 'wemail', 'weMail', $wemail );
    }

}
