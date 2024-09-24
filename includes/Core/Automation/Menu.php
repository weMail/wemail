<?php

namespace WeDevs\WeMail\Core\Automation;

use WeDevs\WeMail\Traits\Hooker;

class Menu {
	use Hooker;

    /**
     * Submenu priority
     *
     * @since 1.0.0
     *
     * @var integer
     */
    private $menu_priority = 3;

    /**
     * Class constructor
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function __construct() {
        $this->add_filter( 'wemail_admin_submenu', 'register_submenu', $this->menu_priority, 2 );
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
    public function register_submenu( $menu_items, $capability ) {
        if ( wemail()->user->can( 'view_campaign' ) ) {
            $menu_items[] = array( __( 'Automations', 'wemail' ), $capability, 'admin.php?page=wemail#/automations' );
        }

        return $menu_items;
    }
}
