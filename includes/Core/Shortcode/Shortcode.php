<?php

namespace WeDevs\WeMail\Core\Shortcode;

use WeDevs\WeMail\Traits\Singleton;

class Shortcode {

    use Singleton;

    public function get( $type = '' ) {
        $shortcodes = array();

        $shortcodes['subscriber'] = array(
            'title' => __( 'Subscriber', 'wemail' ),
            'codes' => array(
                'first_name'        => array(
                    'title' => __( 'First Name', 'wemail' ),
                    'default' => 'reader',
                ),
                'last_name'         => array(
                    'title' => __( 'Last Name', 'wemail' ),
                    'default' => 'reader',
                ),
                'email'             => array(
                    'title' => __( 'Email', 'wemail' ),
                    'placeholder' => 'recipient@example.com',
                ),
                'date_of_birth'     => array(
                    'title' => __( 'Date of Birth', 'wemail' ),
                    'placeholder' => 'MM/DD/YYYY',
                ),
                'source'            => array(
                    'title' => __( 'Source', 'wemail' ),
                    'placeholder' => '',
                ),
                'phone'             => array(
                    'title' => __( 'Phone', 'wemail' ),
                    'placeholder' => '8801000000000',
                ),
                'mobile'            => array(
                    'title' => __( 'Mobile', 'wemail' ),
                    'placeholder' => '8801000000000',
                ),
                'address'           => array(
                    'title' => __( 'Address', 'wemail' ),
                ),
            ),
        );

        $shortcodes['date'] = array(
            'title' => __( 'Date', 'wemail' ),
            'codes' => array(
                'current_date'              => array(
                    'title' => __( 'Current date', 'wemail' ),
                ),
                'current_day_full_name'     => array(
                    'title' => __( 'Full name of current day', 'wemail' ),
                ),
                'current_day_short_name'    => array(
                    'title' => __( 'Short name of current day', 'wemail' ),
                ),
                'current_month_number'      => array(
                    'title' => __( 'Current Month number', 'wemail' ),
                ),
                'current_month_full_name'   => array(
                    'title' => __( 'Full name of current month', 'wemail' ),
                ),
                'current_month_short_name'  => array(
                    'title' => __( 'Short name of current month', 'wemail' ),
                ),
                'year'                      => array(
                    'title' => __( 'Year', 'wemail' ),
                ),
            ),
        );

        $shortcodes['company'] = array(
            'title' => __( 'Company', 'wemail' ),
            'codes' => array(
                'name'      => array(
                    'title' => __( 'Name', 'wemail' ),
                ),
                'address'   => array(
                    'title' => __( 'Mailing Address', 'wemail' ),
                ),
                'phone'     => array(
                    'title' => __( 'Phone', 'wemail' ),
                ),
                'mobile'    => array(
                    'title' => __( 'Mobile', 'wemail' ),
                ),
                'website'   => array(
                    'title' => __( 'Website', 'wemail' ),
                ),
            ),
        );

        $shortcodes['links'] = array(
            'title' => __( 'Links', 'wemail' ),
            'codes' => array(
                'unsubscribe'       => array(
                    'title' => __( 'Unsubscribe Link', 'wemail' ),
                    'text' => __( 'Unsubscribe', 'wemail' ),
                ),
                'archive'           => array(
                    'title' => __( 'Email Archive Link', 'wemail' ),
                    'text' => __( 'View this email in your browser', 'wemail' ),
                ),
            ),
        );

        if ( ! empty( $type ) && ! empty( $shortcodes[ $type ] ) ) {
            return $shortcodes[ $type ];
        }

        return $shortcodes;
    }
}
