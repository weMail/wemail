/* eslint-disable */
// src: https://github.com/WordPress/gutenberg/blob/master/date/index.js

// Date constants.
/**
 * Number of seconds in one minute.
 *
 * @type {Number}
 */
const MINUTE_IN_SECONDS = 60;
/**
 * Number of minutes in one hour.
 *
 * @type {Number}
 */
const HOUR_IN_MINUTES = 60;
/**
 * Number of seconds in one hour.
 *
 * @type {Number}
 */
const HOUR_IN_SECONDS = 60 * MINUTE_IN_SECONDS;

/**
 * Map of PHP formats to Moment.js formats.
 *
 * These are used internally by {@link wp.date.format}, and are either
 * a string representing the corresponding Moment.js format code, or a
 * function which returns the formatted string.
 *
 * This should only be used through {@link wp.date.format}, not
 * directly.
 *
 * @type {Object}
 */
const formatMap = {
    // Day
    d: 'DD',
    D: 'ddd',
    j: 'D',
    l: 'dddd',
    N: 'E',

    /**
     * Gets the ordinal suffix.
     *
     * @param {moment} momentDate Moment instance.
     * @return {string} Formatted date.
     */
    S( momentDate ) {
        // Do - D
        const num = momentDate.format( 'D' );
        const withOrdinal = momentDate.format( 'Do' );
        return withOrdinal.replace( num, '' );
    },

    w: 'd',
    /**
     * Gets the day of the year (zero-indexed).
     *
     * @param {moment} momentDate Moment instance.
     * @return {string} Formatted date.
     */
    z( momentDate ) {
        // DDD - 1
        return '' + parseInt( momentDate.format( 'DDD' ), 10 ) - 1;
    },

    // Week
    W: 'W',

    // Month
    F: 'MMMM',
    m: 'MM',
    M: 'MMM',
    n: 'M',
    /**
     * Gets the days in the month.
     *
     * @param {moment} momentDate Moment instance.
     * @return {string} Formatted date.
     */
    t( momentDate ) {
        return momentDate.daysInMonth();
    },

    // Year
    /**
     * Gets whether the current year is a leap year.
     *
     * @param {moment} momentDate Moment instance.
     * @return {string} Formatted date.
     */
    L( momentDate ) {
        return momentDate.isLeapYear() ? '1' : '0';
    },
    o: 'GGGG',
    Y: 'YYYY',
    y: 'YY',

    // Time
    a: 'a',
    A: 'A',
    /**
     * Gets the current time in Swatch Internet Time (.beats).
     *
     * @param {moment} momentDate Moment instance.
     * @return {string} Formatted date.
     */
    B( momentDate ) {
        const timezoned = moment( momentDate ).utcOffset( 60 );
        const seconds = parseInt( timezoned.format( 's' ), 10 ),
            minutes = parseInt( timezoned.format( 'm' ), 10 ),
            hours = parseInt( timezoned.format( 'H' ), 10 );
        return parseInt(
            (
                seconds
                + ( minutes * MINUTE_IN_SECONDS )
                + ( hours * HOUR_IN_SECONDS )
            ) / 86.4,
            10
        );
    },
    g: 'h',
    G: 'H',
    h: 'hh',
    H: 'HH',
    i: 'mm',
    s: 'ss',
    u: 'SSSSSS',
    v: 'SSS',
    // Timezone
    e: 'zz',
    /**
     * Gets whether the timezone is in DST currently.
     *
     * @param {moment} momentDate Moment instance.
     * @return {string} Formatted date.
     */
    I( momentDate ) {
        return momentDate.isDST() ? '1' : '0';
    },
    O: 'ZZ',
    P: 'Z',
    T: 'z',
    /**
     * Gets the timezone offset in seconds.
     *
     * @param {moment} momentDate Moment instance.
     * @return {string} Formatted date.
     */
    Z( momentDate ) {
        // Timezone offset in seconds.
        const offset = momentDate.format( 'Z' );
        const sign = offset[ 0 ] === '-' ? -1 : 1;
        const parts = offset.substring( 1 ).split( ':' );
        return sign * ( ( parts[ 0 ] * HOUR_IN_MINUTES ) + parts[ 1 ] ) * MINUTE_IN_SECONDS;
    },
    // Full date/time
    c: 'YYYY-MM-DDTHH:mm:ssZ', // .toISOString
    r: 'ddd, D MMM YYYY HH:mm:ss ZZ',
    U: 'X',
};

/**
 * Formats a date. Does not alter the date's timezone.
 *
 * @param {string}                    dateFormat  PHP-style formatting string.
 *                                                See php.net/date
 * @param {(Date|string|moment|null)} dateValue   Date object or string,
 *                                                parsable by moment.js.
 * @return {string} Formatted date.
 */
export function format( dateFormat, dateValue = new Date() ) {
    let i, char;
    let newFormat = [];
    const momentDate = moment( dateValue );
    for ( i = 0; i < dateFormat.length; i++ ) {
        char = dateFormat[ i ];
        // Is this an escape?
        if ( '\\' === char ) {
            // Add next character, then move on.
            i++;
            newFormat.push( '[' + dateFormat[ i ] + ']' );
            continue;
        }
        if ( char in formatMap ) {
            if ( typeof formatMap[ char ] !== 'string' ) {
                // If the format is a function, call it.
                newFormat.push( '[' + formatMap[ char ]( momentDate ) + ']' );
            } else {
                // Otherwise, add as a formatting string.
                newFormat.push( formatMap[ char ] );
            }
        } else {
            newFormat.push( '[' + char + ']' );
        }
    }
    // Join with [] between to separate characters, and replace
    // unneeded separators with static text.
    newFormat = newFormat.join( '[]' );
    return momentDate.format( newFormat );
}
