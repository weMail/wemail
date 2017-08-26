<?php

// don't call the file directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Get a formatted date from WordPress format
 *
 * @param  string  $date the date
 *
 * @return string  formatted date
 */
function wemail_format_date( $date, $format = false ) {
    if ( ! $format ) {
        $format = get_option( 'date_format', 'wemail_settings_general', 'Y-m-d' );
    }

    $time = strtotime( $date );

    return date_i18n( $format, $time );
}

/**
 * Date format for the jQuery Datepicker
 *
 * @since 1.0.0
 *
 * @return string Example: Y-m-d will change to yy-mm-dd
 */
function wemail_js_date_format() {
    $format = get_option( 'date_format', 'wemail_settings_general', 'Y-m-d' );

    return str_replace( [ 'Y', 'm', 'd' ], [ 'yy', 'mm', 'dd' ], $format );
}

/**
 * Converts any value to boolean true or false
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
