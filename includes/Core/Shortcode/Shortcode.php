<?php

namespace WeDevs\WeMail\Core\Shortcode;

use WeDevs\WeMail\Traits\Singleton;

class Shortcode {

    use Singleton;

    public function get( $type = '' ) {
        $shortcodes = [];

        $shortcodes['subscriber'] = [
            'title' => __( 'Subscriber', 'wemail' ),
            'codes' => [
                'first_name'        => [ 'title' => __( 'First Name', 'wemail' ), 'default' => 'reader' ],
                'last_name'         => [ 'title' => __( 'Last Name', 'wemail' ), 'default' => 'reader' ],
                'email'             => [ 'title' => __( 'Email', 'wemail' ), 'placeholder' => 'recipient@example.com' ],
                'life_stage'        => [ 'title' => __( 'Life Stage', 'wemail' ), 'placeholder' => 'life stage' ],
                'date_of_birth'     => [ 'title' => __( 'Date of Birth', 'wemail' ), 'placeholder' => 'MM/DD/YYYY' ],
                'source'            => [ 'title' => __( 'Source', 'wemail' ), 'placeholder' => '' ],
                'phone'             => [ 'title' => __( 'Phone', 'wemail' ), 'placeholder' => '8801000000000' ],
                'mobile'            => [ 'title' => __( 'Mobile', 'wemail' ), 'placeholder' => '8801000000000' ],
                'address'           => [ 'title' => __( 'Address', 'wemail' ) ],
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
                // 'fax'       => [ 'title' => __( 'Fax', 'wemail' ) ],
                'mobile'    => [ 'title' => __( 'Mobile', 'wemail' ) ],
                'website'   => [ 'title' => __( 'Website', 'wemail' ) ],
                // 'currency'  => [ 'title' => __( 'Currency', 'wemail' ) ],
            ]
        ];

        $shortcodes['links'] = [
            'title' => __( 'Links', 'wemail' ),
            'codes' => [
                'unsubscribe'       => [ 'title' => __( 'Unsubscribe Link', 'wemail' ), 'text' => __( 'Unsubscribe', 'wemail' ) ],
                // 'edit_subscription' => [ 'title' => __( 'Edit Subscription Page Link', 'wemail' ), 'text' => __( 'Edit your subscription', 'wemail' ) ],
                'archive'           => [ 'title' => __( 'Email Archive Link', 'wemail' ), 'text' => __( 'View this email in your browser', 'wemail' ) ],
            ]
        ];

        if ( !empty( $type ) && !empty( $shortcodes[ $type ] ) ) {
            return $shortcodes[ $type ];
        }

        return $shortcodes;
    }

}
