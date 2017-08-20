<?php

namespace WeDevs\WeMail\Modules\Subscribers;

use WeDevs\WeMail\Framework\Module;

class Subscribers extends Module {

    public $route_name = 'subscribers';
    public $menu_priority = 70;

    public function register_submenu( $menu_items, $capability ) {
        $menu_items[] = [ __( 'Subscribers', 'wemail' ), $capability, 'admin.php?page=wemail#/subscribers' ];

        return $menu_items;
    }

    public function register_route( $routes ) {
        $routes[] = [
            'path' => '/subscribers',
            'name' => 'subscribers',
            'component' => 'Subscribers',
            'requires' => WEMAIL_ASSETS . '/js/Subscribers.js',
            'dependencies' => apply_filters( 'wemail-admin-route-dep-subscribers', [] ),
            'scrollTo' => 'top'
        ];

        return $routes;
    }

    public function get_subscribers_initial_data() {
        $data = [
            'modelA' => 'Model A data from Home.php ' . current_time( 'mysql' ),
            'notInStore' => 'not found in store'
        ];

        $this->send_success( $data );
    }

}
