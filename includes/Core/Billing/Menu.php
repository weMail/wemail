<?php

namespace WeDevs\WeMail\Core\Billing;

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
    public $menu_priority = 106;

    /**
     * Class constructor
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function __construct() {
        $this->add_filter('wemail_admin_submenu', 'register_submenu', $this->menu_priority, 2);
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
    public function register_submenu($menu_items, $capability) {
        if (wemail()->user->can('manage_settings')) {
            $menu_items[] = [__('Billing', 'wemail'), $capability, 'admin.php?page=wemail#/billing'];
        }

        return $menu_items;
    }
}
