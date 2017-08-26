<?php

namespace WeDevs\WeMail\Modules\Settings;

use WeDevs\WeMail\Framework\Module;

class Settings extends Module {

    /**
     * Vue route name
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $route_name = 'settings';

    /**
     * Submenu priority
     *
     * @since 1.0.0
     *
     * @var integer
     */
    public $menu_priority = 100;

    public function __construct() {
        $this->add_ajax_action('get_route_data_company_details');
        $this->add_ajax_action('get_route_data_social_networks');
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
        $menu_items[] = [ __( 'Settings', 'wemail' ), $capability, 'admin.php?page=wemail#/settings' ];

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
            'path' => '/settings',
            'name' => 'settings',
            'component' => 'Settings',
            'requires' => WEMAIL_ASSETS . '/js/Settings.js',
            'dependencies' => apply_filters( 'wemail-admin-route-dep-settings', [] ),
            'scrollTo' => 'top',
            'redirect' => 'company_details',
            'children' => [
                [
                    'path' => 'company-details',
                    'name' => 'company_details',
                    'component' => 'CompanyDetails',
                    'menu' => __( 'Company Details', 'wemail' )
                ],
                [
                    'path' => 'social-networks',
                    'name' => 'social_networks',
                    'component' => 'SocialNetworks',
                    'menu' => __( 'SocialNetworks', 'wemail' )
                ]
            ]
        ];

        return $routes;
    }

    /**
     * i18n strings
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function i18n() {
        return [
            'settings' => __( 'Settings', 'wemail' ),
            'saveSettings' => __( 'Save Settings', 'wemail' ),
            'optional' => __( 'optional', 'wemail' )
        ];
    }

    /**
     * `company_details` route data
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function get_route_data_company_details() {
        $this->verify_nonce();

        $data = [
            'i18n' => array_merge( $this->i18n(), [
                'companyName' => __( 'Company Name', 'wemail' ),
                'address1' => __( 'Address Line 1', 'wemail' ),
                'address2' => __( 'Address Line 2', 'wemail' ),
                'city' => __( 'City', 'wemail' ),
                'state' => __( 'State/Province/Region', 'wemail' ),
                'country' => __( 'Country', 'wemail' ),
                'zip' => __( 'Zip/Postal Code', 'wemail' ),
                'phone' => __( 'Phone Number', 'wemail' ),
                'mobile' => __( 'Mobile Number', 'wemail' ),
                'website' => __( 'Website', 'wemail' ),
            ] ),
            'settingsTitle' => __( 'Company Details', 'wemail' ),
            'countries' => wemail_get_countries(),
            'states' => wemail_get_country_states( 'BD' ),
            'settings' => [
                'name' => 'weDevs LLC',
                'address1' => 'Level - 3, House - 1005, Avenue - 11, Road - 09',
                'address2' => 'Mirpur DOHS',
                'city' => 'Dhaka',
                'state' => '13',
                'zip' => '33030303',
                'country' => 'BD',
                'phone' => '88888888888888',
                'mobile' => '88888888888888',
                'website' => 'https://wedevs.com'
            ]
        ];

        $this->send_success( $data );
    }

    /**
     * `social_networks` route data
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function get_route_data_social_networks() {
        $this->verify_nonce();

        $data = [
            'i18n' => $this->i18n(),
            'settingsTitle' => __( 'Social Networks', 'wemail' ),
            'settings' => [
                'name' => 'weDevs LLC',
                'street1' => 'Street 1 text',
                'street2' => 'Street 2 text',
            ]
        ];

        $this->send_success( $data );
    }

}
