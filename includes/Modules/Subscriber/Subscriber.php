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
        $this->add_filter( 'wemail_admin_submenu', 'register_submenu', $this->menu_priority, 2 );
        $this->add_filter( 'wemail_get_route_data_subscribers', 'get_route_data_subscribers', 10, 2 );
        $this->add_filter( 'wemail_get_route_data_subscriber', 'get_route_data_subscriber', 10, 2 );
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
            'subscriber' => $this->get_subscriber( $params['id'] )
        ];
    }

    public function get_subscribers( $args = [] ) {
        return wemail()->api->get( '/subscribers', $args );
    }

    public function get_subscriber( $id ) {
        return wemail()->api->get( '/subscribers/' . $id );
    }
}
