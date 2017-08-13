<?php

namespace WeDevs\WeMail\Modules\Campaigns;

use WeDevs\WeMail\Framework\Module;

class Campaigns extends Module {

    public $route_name = 'campaigns';
    public $menu_priority = 2;

    public function register_submenu( $menu_items, $capability ) {
        $menu_items[] = [ __( 'Campaigns', 'wemail' ), $capability, 'admin.php?page=wemail#/campaigns' ];

        return $menu_items;
    }

    public function register_route( $routes ) {
        $routes[] = [
            'path' => '/campaigns',
            'name' => 'campaigns',
            'component' => 'Campaigns',
            'requires' => WEMAIL_ASSETS . '/js/Campaigns.js',
            'dependencies' => apply_filters( 'wemail-admin-route-dep-campaigns', [] ),
            'scrollTo' => 'top'
        ];

        return $routes;
    }

    public function get_campaigns_initial_data() {
        $data = [
            'modelA' => 'Model A data from Home.php ' . current_time( 'mysql' ),
            'notInStore' => 'not found in store'
        ];

        $this->send_success($data);
    }

}
