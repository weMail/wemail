<?php

namespace WeDevs\WeMail\Core\Auth;

use WeDevs\WeMail\Traits\Singleton;
use WP_Error;

class Auth {

    use Singleton;

    /**
     * Authenticate the site
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function site() {
        $start_of_week = get_option( 'start_of_week', 1 );
        $week_days = [ 'sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat' ];

        $user = wp_get_current_user();

        $admin_name = $user->data->display_name;

        $lang = get_option( 'WPLANG', 'en' );
        $lang = !empty( $lang ) ? $lang : 'en';

        $data = [
            'site_name'     => get_bloginfo( 'name' ),
            'site_email'    => get_bloginfo( 'admin_email' ),
            'site_url'      => site_url( '/' ),
            'home_url'      => home_url( '/' ),
            'start_of_week' => $week_days[ $start_of_week ],
            'timezone'      => wemail_get_wp_timezone(),
            'language'      => $lang,
            'date_format'   => get_option( 'date_format', 'F j, Y' ),
            'time_format'   => get_option( 'time_format', 'g:i a' ),
            'admin_name'    => $admin_name,
            'admin_email'   => $user->data->user_email
        ];

        $response = wemail()->api->auth()->sites()->post( $data );

        if ( !empty( $response['access_token'] ) ) {
            update_option( 'wemail_site_slug', $response['data']['slug'] );

            update_user_meta( $user->ID, 'wemail_api_key', $response['access_token'] );

            wemail()->api->set_api_key( $response['access_token'] );

            $wp_admins = get_users( [
                'role' => 'administrator',
                'exclude' => [ $user->ID ]
            ] );

            foreach ( $wp_admins as $wp_admin ) {
                $data = [
                    'name' => $wp_admin->data->display_name,
                    'email' => $wp_admin->data->user_email,
                    'role' => 'admin'
                ];

                $wp_admin_response = wemail()->api->auth()->users()->post( $data );

                if ( !empty( $wp_admin_response['access_token'] ) ) {
                    update_user_meta( $wp_admin->ID, 'wemail_api_key', $wp_admin_response['access_token'] );
                }
            }

            return true;
        }

        $message = __( 'Could not connect your site, please try again', 'wemail' );
        $data = [];

        if ( ! empty( $response['message'] ) ) {
            $message = $response['message'];
        }

        if ( ! empty( $response['errors'] ) ) {
            $data = $response['errors'];
        }

        return new WP_Error( 'failed_to_connect_wemail', $message, $data );
    }

}
