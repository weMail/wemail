<?php

namespace WeDevs\WeMail\Modules\Overview;

use WeDevs\WeMail\Framework\Module;

class Overview extends Module {

    public $route_name = 'overview';
    public $menu_priority = 1;

    public function register_submenu( $menu_items, $capability ) {
        $menu_items[] = [ __( 'Overview', 'wemail' ), $capability, 'admin.php?page=wemail#/' ];

        return $menu_items;
    }

    public function register_route( $routes ) {
        $routes[] = [
            'path' => '/',
            'name' => 'overview',
            'component' => 'Overview',
            'requires' => WEMAIL_ASSETS . '/js/Overview.js',
            'dependencies' => apply_filters( 'wemail-admin-route-dep-overview', [] ),
            'storeStates' => function () {},
            'scrollTo' => 'top'
        ];

        return $routes;
    }

    public function get_overview_initial_data() {
        $data = [
            'modelA' => 'Model A data from Home.php ' . current_time( 'mysql' ),
            'modelB' => 'modelB - Overview.php ' . current_time( 'mysql' ),
            'notInStore' => 'not found in store'
        ];

        $this->send_success( $data );
    }

}
