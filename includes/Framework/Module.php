<?php

namespace WeDevs\WeMail\Framework;

use WeDevs\WeMail\Framework\Traits\Ajax;
use WeDevs\WeMail\Framework\Traits\Hooker;

abstract class Module {

    use Ajax, Hooker;

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
}
