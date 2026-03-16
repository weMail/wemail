<?php

namespace WeDevs\WeMail\Core\AbandonedCarts;

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
    public $menu_priority = 86;

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
        if ( wemail()->user->can( 'view_wemail' ) ) {
            $menu_items[] = array( __( 'Abandoned Carts', 'wemail' ), $capability, 'admin.php?page=wemail#/abandoned-carts' );
        }

        return $menu_items;
    }
}

