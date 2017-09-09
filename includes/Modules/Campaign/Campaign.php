<?php

namespace WeDevs\WeMail\Modules\Campaign;

use WeDevs\WeMail\Framework\Module;

class Campaign extends Module {

    private $menu_priority = 2;

    public function __construct() {
        $this->add_filter( 'wemail-admin-submenu', 'register_submenu', $this->menu_priority, 2 );
        $this->add_filter( 'wemail-get-route-data-campaigns', 'get_route_data_campaigns' );
    }

    public function register_submenu( $menu_items, $capability ) {
        $menu_items[] = [ __( 'Campaigns', 'wemail' ), $capability, 'admin.php?page=wemail#/campaigns' ];

        return $menu_items;
    }

    public function get_route_data_campaigns() {
        $data = [
            'modelA' => 'Model A data from Home.php ' . current_time( 'mysql' ),
            'notInStore' => 'not found in store'
        ];

        $this->send_success( $data );
    }

}
