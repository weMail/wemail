<?php

// don't call the file directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Verifies that a value is boolean true or false
 *
 * @since 1.0.0
 *
 * @param mixed $value
 *
 * @return boolean
 */
function wemail_validate_boolean( $var ) {
    return filter_var( $var, FILTER_VALIDATE_BOOLEAN );
}

/**
 * Verifies that an URL is valid
 *
 * @since 1.0.0
 *
 * @param string $url URL to check
 *
 * @return boolean
 */
function wemail_is_url( $url ) {
    return filter_var( $url, FILTER_VALIDATE_URL );
}

/**
 * Country lists
 *
 * @since 1.0.0
 *
 * @return array
 */
function wemail_get_countries() {
    return include WEMAIL_PATH . '/i18n/countries/countries.php';
}

/**
 * Get states/province/division names for a single country
 *
 * @since 1.0.0
 *
 * @param string $country_code Example: 'BD', 'US', 'CA' etc
 *
 * @return array
 */
function wemail_get_country_states( $country_code ) {
    $filename = WEMAIL_PATH . '/i18n/countries/states/' . strtoupper( $country_code ) . '.php';

    if ( file_exists( $filename ) ) {
        return include $filename;
    } else {
        return [];
    }
}

/**
 * WP Timezone Settings
 *
 * @since 1.0.0
 *
 * @return string
 */
function wemail_get_wp_timezone() {
    $timezone_map = [
        'UTC-12'    => 'Etc/GMT+12',
        'UTC-11.5'  => 'Pacific/Niue',
        'UTC-11'    => 'Pacific/Pago_Pago',
        'UTC-10.5'  => 'Pacific/Honolulu',
        'UTC-10'    => 'Pacific/Honolulu',
        'UTC-9.5'   => 'Pacific/Marquesas',
        'UTC-9'     => 'America/Anchorage',
        'UTC-8.5'   => 'Pacific/Pitcairn',
        'UTC-8'     => 'America/Los_Angeles',
        'UTC-7.5'   => 'America/Edmonton',
        'UTC-7'     => 'America/Denver',
        'UTC-6.5'   => 'Pacific/Easter',
        'UTC-6'     => 'America/Chicago',
        'UTC-5.5'   => 'America/Havana',
        'UTC-5'     => 'America/New_York',
        'UTC-4.5'   => 'America/Halifax',
        'UTC-4'     => 'America/Manaus',
        'UTC-3.5'   => 'America/St_Johns',
        'UTC-3'     => 'America/Sao_Paulo',
        'UTC-2.5'   => 'Atlantic/South_Georgia',
        'UTC-2'     => 'Atlantic/South_Georgia',
        'UTC-1.5'   => 'Atlantic/Cape_Verde',
        'UTC-1'     => 'Atlantic/Azores',
        'UTC-0.5'   => 'Atlantic/Reykjavik',
        'UTC+0'     => 'Etc/UTC',
        'UTC'       => 'Etc/UTC',
        'UTC+0.5'   => 'Etc/UTC',
        'UTC+1'     => 'Europe/Madrid',
        'UTC+1.5'   => 'Europe/Belgrade',
        'UTC+2'     => 'Africa/Tripoli',
        'UTC+2.5'   => 'Asia/Amman',
        'UTC+3'     => 'Europe/Moscow',
        'UTC+3.5'   => 'Asia/Tehran',
        'UTC+4'     => 'Europe/Samara',
        'UTC+4.5'   => 'Asia/Kabul',
        'UTC+5'     => 'Asia/Karachi',
        'UTC+5.5'   => 'Asia/Kolkata',
        'UTC+5.75'  => 'Asia/Kathmandu',
        'UTC+6'     => 'Asia/Dhaka',
        'UTC+6.5'   => 'Asia/Rangoon',
        'UTC+7'     => 'Asia/Bangkok',
        'UTC+7.5'   => 'Asia/Bangkok',
        'UTC+8'     => 'Asia/Shanghai',
        'UTC+8.5'   => 'Asia/Pyongyang',
        'UTC+8.75'  => 'Australia/Eucla',
        'UTC+9'     => 'Asia/Tokyo',
        'UTC+9.5'   => 'Australia/Darwin',
        'UTC+10'    => 'Australia/Brisbane',
        'UTC+10.5'  => 'Australia/Adelaide',
        'UTC+11'    => 'Australia/Melbourne',
        'UTC+11.5'  => 'Pacific/Norfolk',
        'UTC+12'    => 'Asia/Anadyr',
        'UTC+12.75' => 'Asia/Anadyr',
        'UTC+13'    => 'Pacific/Fiji',
        'UTC+13.75' => 'Pacific/Chatham',
        'UTC+14'    => 'Pacific/Tongatapu',
    ];

    $current_offset = get_option( 'gmt_offset' );
    $tzstring = get_option( 'timezone_string' );

    // Remove old Etc mappings. Fallback to gmt_offset.
    if ( strpos( $tzstring, 'Etc/GMT' ) !== false ) {
        $tzstring = '';
    }

    // Create a UTC+- zone if no timezone string exists
    if ( empty( $tzstring ) ) {
        if ( 0 == $current_offset ) {
            $tzstring = 'UTC+0';
        } elseif ( $current_offset < 0 ) {
            $tzstring = 'UTC' . $current_offset;
        } else {
            $tzstring = 'UTC+' . $current_offset;
        }
    }

    if ( array_key_exists( $tzstring , $timezone_map ) ) {
        $tzstring = $timezone_map[ $tzstring ];
    }

    return $tzstring;
}

/**
 * Set owner's key as api key
 *
 * @since 1.0.0
 *
 * @return void
 */
function wemail_set_owner_api_key() {
    if ( get_current_user_id() ) {
        return;
    }

    global $wpdb;

    // We are assuming the first wemail_api_key belongs to owner
    $owner_api_key = $wpdb->get_var(
        $wpdb->prepare(
              'select meta_value'
            . ' from ' . $wpdb->usermeta
            . ' where meta_key = %s'
            . ' order by umeta_id asc'
            . ' limit 1',

            'wemail_api_key'
        )
    );

    wemail()->api->set_api_key( $owner_api_key );
}
