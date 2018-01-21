<?php

namespace WeDevs\WeMail\Core\Form;

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
    public $menu_priority = 90;

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
        if ( wemail()->user->can( 'view_form' ) ) {
            $menu_items[] = [ __( 'Forms', 'wemail' ), $capability, 'admin.php?page=wemail#/forms' ];
        }

        return $menu_items;
    }

}
