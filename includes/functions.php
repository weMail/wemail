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

function wemail_validate_boolean( $var ) {
    return filter_var( $var, FILTER_VALIDATE_BOOLEAN );
}
