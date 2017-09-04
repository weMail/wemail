<?php

namespace WeDevs\WeMail\Modules\Subscribers;

use WeDevs\WeMail\Framework\Module;

class Subscribers extends Module {

    public $route_name = 'subscribers';
    public $menu_priority = 70;

    public function __construct() {
        $this->add_filter( 'wemail-get-route-data-subscribers', 'get_route_data_subscribers' );

        parent::__construct();
    }

    public function register_submenu( $menu_items, $capability ) {
        $menu_items[] = [ __( 'Subscribers', 'wemail' ), $capability, 'admin.php?page=wemail#/subscribers' ];

        return $menu_items;
    }

    public function register_route( $routes ) {
        $routes[] = [
            'path' => '/subscribers',
            'name' => 'subscribers',
            'component' => 'Subscribers',
            'requires' => WEMAIL_ASSETS . '/js/Subscribers.js',
            'dependencies' => apply_filters( 'wemail-admin-route-dep-subscribers', [] ),
            'scrollTo' => 'top'
        ];

        $routes[] = [
            'path' => '/subscriber/:subscriber_id',
            'name' => 'subscriber',
            'component' => 'Subscriber',
            'requires' => WEMAIL_ASSETS . '/js/Subscriber.js',
            'dependencies' => apply_filters( 'wemail-admin-route-dep-subscriber', [] ),
            'scrollTo' => 'top',
            'submenu' => '/subscribers'
        ];

        return $routes;
    }

    public function get_route_data_subscribers() {
        return [
            'name' => 'subscribers : ' . current_time( 'mysql' )
        ];
    }
}
