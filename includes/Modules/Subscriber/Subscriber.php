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
            'noName'             => __( 'no name', 'wemail' ),
            'website'            => __( 'Website', 'wemail' ),
            'informations'       => __( 'Informations', 'wemail' ),
            'lists'              => __( 'Lists', 'wemail' ),
            'subscriberNotFound' => __( 'Subscriber not found', 'wemail' ),
            'dob'                => __( 'Date of birth', 'wemail' ),
            'address1'           => __( 'Address 1', 'wemail' ),
            'address2'           => __( 'Address 2', 'wemail' ),
            'city'               => __( 'City', 'wemail' ),
            'state'              => __( 'State', 'wemail' ),
            'country'            => __( 'Country', 'wemail' ),
            'zip'                => __( 'Zip', 'wemail' ),
            'editInfo'           => __( 'edit info', 'wemail' ),
            'save'               => __( 'Save', 'wemail' ),
            'cancel'             => __( 'Cancel', 'wemail' ),
            'unconfirmed'        => __( 'unconfirmed', 'wemail' ),
            'unsubscribed'       => __( 'unsubscribed', 'wemail' ),
            'subscribed'         => __( 'subscribed', 'wemail' ),
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
            'i18n' => $i18n,
            'dummyImageURL' => WEMAIL_ASSETS . '/images/misc/mystery-person.png',
            'subscriber' => wemail()->subscriber->get( $params['hash'] ),
            'lists' => wemail()->lists->all(),
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
