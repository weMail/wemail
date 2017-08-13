<?php

namespace WeDevs\WeMail\Modules\Lists;

use WeDevs\WeMail\Framework\Module;

class Lists extends Module {

    public $route_name = 'lists';
    public $menu_priority = 90;

    public function register_submenu( $menu_items, $capability ) {
        $menu_items[] = [ __( 'Lists', 'wemail' ), $capability, 'admin.php?page=wemail#/lists' ];

        return $menu_items;
    }

    public function register_route( $routes ) {
        $routes[] = [
            'path' => '/lists',
            'name' => 'lists',
            'component' => 'Lists',
            'requires' => WEMAIL_ASSETS . '/js/Lists.js',
            'dependencies' => apply_filters( 'wemail-admin-route-dep-lists', [] ),
            'scrollTo' => 'top'
        ];

        return $routes;
    }

    public function get_lists_initial_data() {
        $data = [
            'modelA' => 'Model A data from Home.php ' . current_time( 'mysql' ),
            'notInStore' => 'not found in store'
        ];

        $this->send_success($data);
    }

}
