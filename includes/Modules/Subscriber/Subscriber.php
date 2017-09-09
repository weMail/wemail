<?php

namespace WeDevs\WeMail\Modules\Subscriber;

use WeDevs\WeMail\Framework\Module;

class Subscriber extends Module {

    /**
     * Submenu priority
     *
     * @since 1.0.0
     *
     * @var integer
     */
    public $menu_priority = 70;

    /**
     * Class constructor
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function __construct() {
        $this->add_filter( 'wemail-admin-submenu', 'register_submenu', $this->menu_priority, 2 );
        $this->add_filter( 'wemail-get-route-data-subscribers', 'get_route_data_subscribers', 10, 2 );
        $this->add_filter( 'wemail-get-route-data-subscriber', 'get_route_data_subscriber', 10, 2 );
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
        $menu_items[] = [ __( 'Subscribers', 'wemail' ), $capability, 'admin.php?page=wemail#/subscribers' ];

        return $menu_items;
    }

    /**
     * Subscribers route data
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function get_route_data_subscribers( $params, $query ) {
        return [
            'i18n' => [],
            'subscribers' => $this->get_subscribers( $query )
        ];
    }

    public function get_route_data_subscriber( $params, $query ) {
        return [
            'subscriber' => wemail()->api->get( '/subscribers/' . $params['id'] )
        ];
    }

    public function get_subscribers( $query = [] ) {
        return wemail()->api->get( '/subscribers', $query );
    }
}
