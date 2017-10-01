<?php

namespace WeDevs\WeMail\Modules\Shortcode;

class Shortcode {

    public function get( $type = '' ) {
        $shortcodes = [];

        $shortcodes['user'] = [
            'title' => __( 'User', 'wemail' ),
            'codes' => [
                'first_name'        => [ 'title' => __( 'First Name', 'wemail' ), 'default' => 'reader' ],
                'last_name'         => [ 'title' => __( 'Last Name', 'wemail' ), 'default' => 'reader' ],
                'email'             => [ 'title' => __( 'Email', 'wemail' ), 'placeholder' => 'recipient@example.com' ],
                'company'           => [ 'title' => __( 'Company', 'wemail' ), 'placeholder' => __( 'Company Name', 'wemail' ) ],
                'phone'             => [ 'title' => __( 'Phone', 'wemail' ), 'placeholder' => '8801000000000' ],
                'mobile'            => [ 'title' => __( 'Mobile', 'wemail' ), 'placeholder' => '8801000000000' ],
                'other'             => [ 'title' => __( 'Other', 'wemail' ), 'placeholder' => __( 'other informations', 'wemail' ) ],
                'website'           => [ 'title' => __( 'Website', 'wemail' ), 'placeholder' => 'http://example.com' ],
                'fax'               => [ 'title' => __( 'Fax', 'wemail' ), 'placeholder' => '(880) 100 0000000' ],
                'notes'             => [ 'title' => __( 'Notes', 'wemail' ), 'placeholder' => __( 'notes', 'wemail' ) ],
                'street_1'          => [ 'title' => __( 'Street 1', 'wemail' ), 'placeholder' => __( 'Street address 1', 'wemail' ) ],
                'street_2'          => [ 'title' => __( 'Street 2', 'wemail' ), 'placeholder' => __( 'Street address 2', 'wemail' ) ],
                'city'              => [ 'title' => __( 'City', 'wemail' ), 'placeholder' => __( 'City Name', 'wemail' ) ],
                'state'             => [ 'title' => __( 'State', 'wemail' ), 'placeholder' => __( 'State Name', 'wemail' ) ],
                'postal_code'       => [ 'title' => __( 'Postal Code', 'wemail' ), 'placeholder' => '1216' ],
                'country'           => [ 'title' => __( 'Country', 'wemail' ), 'placeholder' => __( 'Country Name', 'wemail' ) ],
                'currency_code'     => [ 'title' => __( 'Currency Code', 'wemail' ), 'placeholder' => 'USD' ],
                'currency_symbol'   => [ 'title' => __( 'Currency Symbol', 'wemail' ), 'placeholder' => '$' ],
                'currency_name'     => [ 'title' => __( 'Currency Name', 'wemail' ), 'placeholder' => __( 'US Dollar', 'wemail' ) ],
            ]
        ];

        $shortcodes['date'] = [
            'title' => __( 'Date', 'wemail' ),
            'codes' => [
                'current_date'              => [ 'title' => __( 'Current date', 'wemail' ) ],
                'current_day_full_name'     => [ 'title' => __( 'Full name of current day', 'wemail' ) ],
                'current_day_short_name'    => [ 'title' => __( 'Short name of current day', 'wemail' ) ],
                'current_month_number'      => [ 'title' => __( 'Current Month number', 'wemail' ) ],
                'current_month_full_name'   => [ 'title' => __( 'Full name of current month', 'wemail' ) ],
                'current_month_short_name'  => [ 'title' => __( 'Short name of current month', 'wemail' ) ],
                'year'                      => [ 'title' => __( 'Year', 'wemail' ) ],
            ]
        ];

        $shortcodes['company'] = [
            'title' => __( 'Company', 'wemail' ),
            'codes' => [
                // 'logo'      => [ 'title' => __( 'Logo', 'wemail' ), 'plainText' => true, 'text' => $this->company_details['logo'] ],
                'name'      => [ 'title' => __( 'Name', 'wemail' ) ],
                'address'   => [ 'title' => __( 'Mailing Address', 'wemail' ) ],
                'phone'     => [ 'title' => __( 'Phone', 'wemail' ) ],
                'fax'       => [ 'title' => __( 'Fax', 'wemail' ) ],
                'mobile'    => [ 'title' => __( 'Mobile', 'wemail' ) ],
                'website'   => [ 'title' => __( 'Website', 'wemail' ) ],
                'currency'  => [ 'title' => __( 'Currency', 'wemail' ) ],
            ]
        ];

        $shortcodes['links'] = [
            'title' => __( 'Links', 'wemail' ),
            'codes' => [
                'unsubscribe'       => [ 'title' => __( 'Unsubscribe Link', 'wemail' ), 'text' => __( 'Unsubscribe', 'wemail' ) ],
                'edit_subscription' => [ 'title' => __( 'Edit Subscription Page Link', 'wemail' ), 'text' => __( 'Edit your subscription', 'wemail' ) ],
                'archive'           => [ 'title' => __( 'Email Archive Link', 'wemail' ), 'text' => __( 'View this email in your browser', 'wemail' ) ],
            ]
        ];

        if ( !empty( $type ) && !empty( $shortcodes[ $type ] ) ) {
            return $shortcodes[ $type ];
        }

        return $shortcodes;
    }

}
