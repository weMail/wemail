<?php

namespace WeDevs\WeMail\Modules\Overview;

use WeDevs\WeMail\Framework\Module;

class Overview extends Module {

    public $menu_priority = 1;

    public function __construct() {
        $this->add_filter( 'wemail_admin_submenu', 'register_submenu', $this->menu_priority, 2 );
        $this->add_filter( 'wemail_get_route_data_overview', 'get_route_data' );
    }

    public function register_submenu( $menu_items, $capability ) {
        $menu_items[] = [ __( 'Overview', 'wemail' ), $capability, 'admin.php?page=wemail#/' ];

        return $menu_items;
    }

    public function get_route_data() {
        return [];
    }
}
