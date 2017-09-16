<?php

namespace WeDevs\WeMail\Modules\Settings;

use WeDevs\WeMail\Framework\Module;

class Settings extends Module {

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
        $this->add_filter( 'wemail_admin_submenu', 'register_submenu', $this->menu_priority, 2 );
        $this->register_settings();

        $this->add_filter( 'wemail_get_route_data_settings', 'get_route_data' );
        $this->add_ajax_action( 'save_settings' );
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
     * Register settings
     *
     * @since 1.0.0
     *
     * @return void
     */
    private function register_settings() {
        $settings = [
            new Company(),
            new SocialNetworks()
        ];

        $settings = apply_filters( 'wemail_register_settings', $settings );

        $this->settings = collect( $settings )->sortBy( 'priority' );

        $this->settings->each( function ( $setting ) {
            add_filter( "wemail_get_route_data_{$setting->route_name}", [ $setting, 'get_route_data' ] );
        } );
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
            ],
            'settings' => $this->settings->map( function ( $setting ) {
                return [
                    'routeName' => $setting->route_name,
                    'menu'      => $setting->menu
                ];
            } )
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
            $name = preg_replace( '/^settings/', '', $_POST['name'] );
            $url = '/settings/' . \Stringy\StaticStringy::dasherize( $name );

            $response = wemail()->api->post( $url, $_POST['settings'] );

            if ( $response['success'] ) {
                $success = true;
            }
        }

        $this->send_success( ['success' => $success ] );
    }

}
