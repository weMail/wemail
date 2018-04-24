<?php

namespace WeDevs\WeMail\Traits;

use WeDevs\WeMail\Traits\Singleton;

trait Core {

    use Singleton;

    /**
     * A helper function to return api response
     *
     * @since 1.0.0
     *
     * @param array|WP_Error $response
     *
     * @return array|null
     */
    private function data( $response ) {
        if ( ! is_wp_error( $response ) && ! empty( $response['data'] ) ) {
            return $response['data'];
        }

        return null;
    }
}
