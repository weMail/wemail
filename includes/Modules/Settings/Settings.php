<?php

namespace WeDevs\WeMail\Modules\Settings;

use WeDevs\WeMail\Framework\Module;

class Settings extends Module {

    /**
     * Vue route name
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $route_name = 'settings';

    /**
     * Submenu priority
     *
     * @since 1.0.0
     *
     * @var integer
     */
    public $menu_priority = 100;

    public $settings = [];

    /**
     * Class constructor
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function __construct() {
        $this->add_filter( 'wemail-get-route-data-settings', 'get_route_data' );
        $this->add_ajax_action( 'save_settings' );

        $this->register_settings();

        parent::__construct();
    }

    /**
     * Register settings
     *
     * @since 1.0.0
     *
     * @return void
     */
    private function register_settings() {
        $default_settings = [
            new Company( $this ),
            new SocialNetworks( $this )
        ];

        $settings = apply_filters( 'wemail-register-settings', $default_settings, $this );

        $this->settings = collect( $settings )->sortBy( 'priority' );

        $this->settings->each( function ( $setting ) {
            add_action( 'wemail-get-route-data-' . $setting->route_name, [ $setting, 'get_route_data' ] );
        } );
    }

    /**
     * Register submenu
     *
     * @since 1.0.0
     *
     * @param array $menu_items
     * @param string $capability
     *
     * @return array
     */
    public function register_submenu( $menu_items, $capability ) {
        $menu_items[] = [ __( 'Settings', 'wemail' ), $capability, 'admin.php?page=wemail#/settings' ];

        return $menu_items;
    }

    /**
     * Register Vue route
     *
     * @since 1.0.0
     *
     * @param array $routes
     *
     * @return array
     */
    public function register_route( $routes ) {
        $children = $this->settings->map( function ( $settings ) {
            return [
                'path'      => $settings->path,
                'name'      => $settings->route_name,
                'component' => $settings->component,
                'menu'      => $settings->menu
            ];
        } );

        $routes[] = [
            'path'         => '/settings',
            'name'         => 'settings',
            'component'    => 'Settings',
            'requires'     => WEMAIL_ASSETS . '/js/Settings.js',
            'dependencies' => apply_filters( 'wemail-admin-route-dep-settings', [] ),
            'scrollTo'     => 'top',
            'redirect'     => 'companyDetails',
            'children'     => $children
        ];

        return $routes;
    }

    /**
     * Settings route data
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function get_route_data() {
        return [
            'i18n' => [
                'settings'      => __( 'Settings', 'wemail' ),
                'saveSettings'  => __( 'Save Settings', 'wemail' ),
                'optional'      => __( 'optional', 'wemail' )
            ]
        ];
    }

    /**
     * Save settings
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function save_settings() {
        $this->verify_nonce();

        // permission check
        // TODO: Check permissions

        $success = false;

        if ( ! empty( $_POST['name'] ) && ! empty( $_POST['settings'] ) ) {
            $url = '/settings/' . \Stringy\StaticStringy::dasherize( $_POST['name'] );

            $response = wemail()->api->post( $url, $_POST['settings'] );

            if ( $response['success'] ) {
                $success = true;
            }
        }

        $this->send_success( ['success' => $success ] );
    }

}
