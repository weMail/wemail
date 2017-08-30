<?php

namespace WeDevs\WeMail\Modules\Settings;

use WeDevs\WeMail\Modules\Settings\AbstractSettings;

class CompanyDetails extends AbstractSettings {

    /**
     * Menu priority
     *
     * @since 1.0.0
     *
     * @var integer
     */
    public $priority = 10;

    /**
     * Settings menu name
     *
     * @since 1.0.0
     *
     * @return string
     */
    public function get_menu_name() {
        return __( 'Company Details', 'wemail' );
    }

    /**
     * Settings data
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function get_settings() {
        $defaults = [
            'name'      => null,
            'image_id'  => 0,
            'address1'  => null,
            'address2'  => null,
            'city'      => null,
            'state'     => null,
            'country'   => null,
            'zip'       => null,
            'phone'     => null,
            'mobile'    => null,
            'website'   => null
        ];

        $settings = wemail()->api->get( '/settings/company-details' );

        return wp_parse_args( $settings, $defaults );
    }

    /**
     * Route data
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function get_route_data() {
        $this->verify_nonce();

        $settings = $this->get_settings();

        $root = [
            'settingsTitle' => $this->menu,
            'i18n'          => $this->parent->i18n(),
        ];

        $current = [
            'i18n' => [
                'companyName'       => __( 'Company Name', 'wemail' ),
                'address1'          => __( 'Address Line 1', 'wemail' ),
                'address2'          => __( 'Address Line 2', 'wemail' ),
                'city'              => __( 'City', 'wemail' ),
                'state'             => __( 'State/Province/Region', 'wemail' ),
                'country'           => __( 'Country', 'wemail' ),
                'zip'               => __( 'Zip/Postal Code', 'wemail' ),
                'phone'             => __( 'Phone Number', 'wemail' ),
                'mobile'            => __( 'Mobile Number', 'wemail' ),
                'website'           => __( 'Website', 'wemail' ),
                'selectCountry'     => __( 'Select a country', 'wemail' ),
                'selectState'       => __( 'Select a state/province/region', 'wemail' ),
                'noStateFound'      => __( 'No state found', 'wemail' ),
                'noCountryFound'    => __( 'No country found', 'wemail' ),
                'addComapanyLogo'   => __( 'Add Comapany Logo', 'wemail' )
            ],
            'settings'  => $settings,
            'countries' => wemail_get_countries(),
            'states'    => ! empty( $settings['country'] ) ? wemail_get_country_states( $settings['country'] ) : []
        ];

        $this->send_success( [
            'settings'       => $root,
            'companyDetails' => $current
        ] );
    }

}
