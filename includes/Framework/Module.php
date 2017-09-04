<?php

namespace WeDevs\WeMail\Framework;

use WeDevs\WeMail\Framework\Traits\Ajax;
use WeDevs\WeMail\Framework\Traits\Hooker;

abstract class Module {

    use Ajax, Hooker;

    /**
     * Class constructor
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function __construct() {
        if ( method_exists( $this , 'register_submenu' ) && isset( $this->menu_priority ) ) {
            $this->add_filter( 'wemail-admin-submenu', 'register_submenu', $this->menu_priority, 2 );
        }

        if ( method_exists( $this , 'register_route' ) ) {
            $this->add_filter( 'wemail-admin-register-routes', 'register_route');
        }
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
    abstract public function register_submenu( $menu_items, $capability );

    /**
     * Register Vue route
     *
     * @since 1.0.0
     *
     * @param array $routes
     *
     * @return array
     */
    abstract public function register_route( $routes );

}
