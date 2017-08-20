<?php

namespace WeDevs\WeMail\Modules\Settings;

use WeDevs\WeMail\Framework\Module;

class Settings extends Module {

    public $route_name = 'settings';
    public $menu_priority = 100;

    public function register_submenu( $menu_items, $capability ) {
        $menu_items[] = [ __( 'Settings', 'wemail' ), $capability, 'admin.php?page=wemail#/settings' ];

        return $menu_items;
    }

    public function register_route( $routes ) {
        $routes[] = [
            'path' => '/settings',
            'name' => 'settings',
            'component' => 'Settings',
            'requires' => WEMAIL_ASSETS . '/js/Settings.js',
            'dependencies' => apply_filters( 'wemail-admin-route-dep-settings', [] ),
            'scrollTo' => 'top'
        ];

        return $routes;
    }

    public function get_settings_initial_data() {
        $data = [
            'modelA' => 'Model A data from Home.php ' . current_time( 'mysql' ),
            'notInStore' => 'not found in store'
        ];

        $this->send_success( $data );
    }

}
