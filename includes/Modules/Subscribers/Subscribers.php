<?php

namespace WeDevs\WeMail\Modules\Subscribers;

use WeDevs\WeMail\Framework\Module;

class Subscribers extends Module {

    /**
     * Vue route name
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $route_name = 'subscribers';

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
        $this->add_filter( 'wemail-get-route-data-subscribers', 'get_route_data_subscribers', 10, 2 );

        parent::__construct();
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
     * Register Vue route
     *
     * @since 1.0.0
     *
     * @param array $routes
     *
     * @return array
     */
    public function register_route( $routes ) {
        $routes[] = [
            'path' => '/subscribers',
            'name' => 'subscribers',
            'component' => 'Subscribers',
            'requires' => WEMAIL_ASSETS . '/js/Subscribers.js',
            'dependencies' => apply_filters( 'wemail-admin-route-dep-subscribers', [] ),
            'scrollTo' => 'top',
            'ignoreDataRefetch' => [
                'query' => [
                    's', 'segment'
                ]
            ],
            'children' => [
                [
                    'path' => 'status/:status'
                ]
            ]
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


    /**
     * Subscribers route data
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function get_route_data_subscribers( $params, $query ) {
        // error_log( print_r( $query, true ) );

        return [
            'i18n' => [],
            'subscribers' => $this->get_subscribers( $query )
        ];
    }

    public function get_subscribers( $query = [] ) {
        return wemail()->api->get( '/subscribers', $query );
    }
}
