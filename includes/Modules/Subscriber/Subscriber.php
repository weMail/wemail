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
        // $this->add_filter( 'wemail_get_route_data_subscribers', 'get_route_data_subscribers', 10, 2 );
        // $this->add_filter( 'wemail_get_route_data_subscriber', 'get_route_data_subscriber', 10, 2 );

        $this->add_filter( 'wemail_get_route_data_subscriberIndex', 'index', 10, 2 );
        $this->add_filter( 'wemail_get_route_data_subscriberShow', 'show', 10, 2 );
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
     * Subscribers list table data
     *
     * @since 1.0.0
     *
     * @param array $params
     * @param array $query
     *
     * @return array
     */
    public function index( $params, $query ) {
        return [
            'i18n' => [],
            'subscribers' => wemail()->subscriber->all( $query )
        ];
    }

    /**
     * Single subscriber page route data
     *
     * @since 1.0.0
     *
     * @param array $params
     * @param array $query
     *
     * @return array
     */
    public function show( $params, $query ) {
        $i18n = [
            'noName'  => __( 'no name', 'wemail' ),
            'website' => __( 'Website', 'wemail' ),
        ];

        $social_networks = wemail()->settings->social_networks->i18n();
        $i18n = array_merge( $i18n, $social_networks );

        $social_networks = wemail()->settings->social_networks->networks();
        array_unshift($social_networks, 'website');

        $social_network_icons = wemail()->settings->social_networks->icons();
        $social_network_icons = array_merge( $social_network_icons, [
            'website' => '<i class="fa fa-globe"></i>'
        ] );

        return [
            'dummyImageURL' => WEMAIL_ASSETS . '/images/misc/mystery-person.png',
            'subscriber'    => wemail()->subscriber->get( $params['hash'] ),
            'i18n' => $i18n,
            'socialNetworks' => [
                'networks' => $social_networks,
                'icons' => $social_network_icons
            ]
        ];
    }

    /**
     * Get list of subscribers
     *
     * @since 1.0.0
     *
     * @param array $args
     *
     * @return array
     */
    public function all( $args = [] ) {
        return wemail()->api->subscribers()->query( $args )->get();
    }

    /**
     * Get data for a single subscriber
     *
     * @since 1.0.0
     *
     * @param string $hash
     *
     * @return array
     */
    public function get( $hash ) {
        return wemail()->api->subscribers( $hash )->get();
    }
}
