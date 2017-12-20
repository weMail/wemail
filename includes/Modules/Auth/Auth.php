<?php

namespace WeDevs\WeMail\Modules\Auth;

use WP_REST_Response;
use WeDevs\WeMail\Framework\Traits\Ajax;
use WeDevs\WeMail\Framework\Traits\Hooker;


class Auth {

    use Ajax;
    use Hooker;

    /**
     * Class constructor
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function __construct() {
        $this->add_ajax_action( 'auth_site' );
        $this->add_action( 'rest_api_init', 'add_validate_me_endpoint' );
    }

    /**
     * Authenticate the site
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function auth_site() {
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

            $this->send_success();
        }

        $message = __( 'Could not connect your site, please try again', 'wemail' );

        if ( !empty( $response['message'] ) ) {
            $message = $response['message'];
        }

        if ( !empty( $response['errors'] ) ) {
            $message .= '<ul>';

            foreach ( $response['errors'] as $field => $error ) {
                $message .= "<li>{$field} : " . implode( ' ', $error );
            }

            $message .= '</ul>';
        }

        status_header( 422 );

        $this->send_error( ['message' => $message ] );
    }

    public function add_validate_me_endpoint() {
        register_rest_route( 'wemail', '/validate-me', array(
            'methods' => 'GET',
            'callback' => [ $this, 'validate_me' ],
        ) );
    }

    public function validate_me() {
        $data = [
            'success' => true
        ];

        return new WP_REST_Response( $data, 200 );
    }

}
