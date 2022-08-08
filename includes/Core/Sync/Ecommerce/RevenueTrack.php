<?php

namespace WeDevs\WeMail\Core\Sync\Ecommerce;

use WeDevs\WeMail\Traits\Hooker;

class RevenueTrack {

    use Hooker;

    /**
     * Tracking key
     */
    const TRACKING_KEY = '_wem_rev_track';

    /**
     * Class constructor
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function __construct() {
        $this->add_action( 'template_redirect', 'track' );
    }


    /**
     * Track campaign revenue
     *
     * @return void
     */
    public function track() {
        if ( ! isset( $_GET[ self::TRACKING_KEY ] ) ) {
            return;
        }

        if ( ! class_exists( 'WooCommerce' ) && ! class_exists( 'Easy_Digital_Downloads' ) ) {
            return;
        }

        if ( isset( $_COOKIE[ self::TRACKING_KEY ] ) ) {
            return;
        }

        setcookie( self::TRACKING_KEY, sanitize_text_field( wp_unslash( $_GET[ self::TRACKING_KEY ] ) ), strtotime( '+2 day' ), '/' );
    }

    /**
     * Extract Tracking ID from Cookie
     *
     * @param array $payload
     *
     * @return void
     */
    public static function track_id( array &$payload ) {
        if ( isset( $_COOKIE[ self::TRACKING_KEY ] ) ) {
            $payload[ self::TRACKING_KEY ] = sanitize_text_field( wp_unslash( $_COOKIE[ self::TRACKING_KEY ] ) );
            unset( $_COOKIE[ self::TRACKING_KEY ] );
            setcookie( self::TRACKING_KEY, '', time() - ( 15 * 60 ) );
        }
    }
}
