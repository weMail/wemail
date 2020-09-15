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
    public function site( $api = '' ) {
        $start_of_week = get_option( 'start_of_week', 1 );
        $week_days = [ 'sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat' ];

        $user = wp_get_current_user();

        $admin_name = $user->data->display_name;

        $lang = get_option( 'WPLANG', 'en' );
        $lang = ! empty( $lang ) ? $lang : 'en';

        /**
         * App.getwemail.io will validate this site using this key
         *
         * @since 1.0.0
         *
         * @param string
         */
        $key = apply_filters( 'wemail_validate_me_key', wp_generate_password( 32 ) );

        set_transient( 'wemail_validate_me_key', $key, 5 * MINUTE_IN_SECONDS );

        $data = [
            'api_key'           => $api,
            'site_name'         => get_bloginfo( 'name' ),
            'site_email'        => get_bloginfo( 'admin_email' ),
            'site_url'          => untrailingslashit( site_url( '/' ) ),
            'home_url'          => untrailingslashit( home_url( '/' ) ),
            'rest_url_prefix'   => rest_get_url_prefix(),
            'start_of_week'     => $week_days[ $start_of_week ],
            'timezone'          => wemail_get_wp_timezone(),
            'language'          => $lang,
            'date_format'       => get_option( 'date_format', 'F j, Y' ),
            'time_format'       => get_option( 'time_format', 'g:i a' ),
            'admin_name'        => $admin_name,
            'admin_email'       => $user->data->user_email,
            'rest_url'          => untrailingslashit( get_rest_url( null, '/wemail/v1/auth/validate-me' ) ),
            'key'               => $key,
        ];

        $response = wemail()->api->auth()->sites()->post( $data );

        if ( is_wp_error( $response ) ) {
            $data = $response->get_error_data();
            return new WP_Error( 'failed_to_connect_wemail', $response->get_error_message(), $data );
        }

        if ( ! empty( $response['access_token'] ) ) {
            update_option( 'wemail_site_slug', $response['data']['slug'] );
            $available_roles = [ 'administrator', 'editor' ];
            update_option( 'wemail_accessible_roles', $available_roles );

            update_user_meta( $user->ID, 'wemail_api_key', $response['access_token'] );

            wemail()->api->set_api_key( $response['access_token'] );

            $wp_admins = get_users(
                [
                    'role__in' => $available_roles,
                    'exclude' => [ $user->ID ],
                ]
            );

            foreach ( $wp_admins as $wp_admin ) {
                $roles = array_values( $wp_admin->roles );
                $data = [
                    'name' => $wp_admin->data->display_name,
                    'email' => $wp_admin->data->user_email,
                    'role' => in_array( 'administrator', $roles, true ) ? 'admin' : 'team',
                ];

                $wp_admin_response = wemail()->api->auth()->users()->post( $data );

                if ( ! empty( $wp_admin_response['access_token'] ) ) {
                    update_user_meta( $wp_admin->ID, 'wemail_api_key', $wp_admin_response['access_token'] );
                }
            }

            /**
             * Action hook fires after a site is authenticated
             *
             * @since 1.0.0
             *
             * @param array $response weMail API response after site authentication
             */
            do_action( 'wemail_site_authenticated', $response );

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
