<?php

namespace WeDevs\WeMail\Framework;

use WeDevs\WeMail\Framework\Traits\Ajax;
use WeDevs\WeMail\Framework\Traits\Hooker;

abstract class Module {

    use Ajax, Hooker;

    public function __construct() {
        if ( wemail()->is_request( 'ajax' ) ) {
            $this->add_ajax_action( 'get_' . $this->route_name . '_initial_data' );
        }

        if ( method_exists( $this , 'register_submenu' ) && isset( $this->menu_priority ) ) {
            $this->add_filter( 'wemail-admin-submenu', 'register_submenu', $this->menu_priority, 2 );
        }

        if ( method_exists( $this , 'register_route' ) ) {
            $this->add_filter( 'wemail-admin-register-routes', 'register_route');
        }
    }

    abstract public function register_submenu( $menu_items, $capability );

    abstract public function register_route( $routes );

}
