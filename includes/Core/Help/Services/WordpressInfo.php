<?php

namespace WeDevs\WeMail\Core\Help\Services;

class WordpressInfo {
    private $db;

    public function __construct() {
        global $wpdb;

        $this->db = $wpdb;
    }

    /**
     * Get WordPress related data.
     *
     * @return array
     */
    public function get_wp_info() {
        $wp_data = array();

        $wp_data['site_url']                    = esc_url( home_url() );
        $wp_data['memory_limit']                = WP_MEMORY_LIMIT;
        $wp_data['debug_mode']                  = ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? 'Yes' : 'No';
        $wp_data['locale']                      = get_locale();
        $wp_data['version']                     = get_bloginfo( 'version' );
        $wp_data['multisite']                   = is_multisite() ? 'Yes' : 'No';
        $wp_data['debug_mode']                  = $this->debug_mode();
        $wp_data['debug_log_active']            = $this->debug_log_active();
        $wp_data['debug_log_file_location']     = $this->debug_log_file_location();
        $wp_data['disable_cron']                = $this->disable_cron();
        $wp_data['upload_directory_location']   = $this->upload_directory_location();
        $wp_data['theme']                       = get_stylesheet();

        return $wp_data;
    }

    public function debug_mode() {
        return defined( 'WP_DEBUG' ) && WP_DEBUG;
    }

    public function debug_log_active() {
        return defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG;
    }

    private function debug_log_file_location() {
        if ( ( defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) ) {
            return ini_get( 'error_log' );
        }

        return null;
    }

    private function disable_cron() {
        return defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON;
    }

    private function upload_directory_location() {
        $upload_dir = wp_upload_dir();

        return $upload_dir['basedir'];
    }

    /**
     * Get user IP Address
     */
    public function get_user_ip_address() {
        $response = wp_remote_get( 'https://icanhazip.com/' );

        if ( is_wp_error( $response ) ) {
            return '';
        }

        $ip = trim( wp_remote_retrieve_body( $response ) );

        if ( ! filter_var( $ip, FILTER_VALIDATE_IP ) ) {
            return '';
        }

        return $ip;
    }


    /**
     * Get user totals based on user role.
     *
     * @return array
     */
    public function get_user_counts() {
        $user_count          = array();
        $user_count_data     = count_users();
        $user_count['total'] = $user_count_data['total_users'];

        // Get user count based on user role
        foreach ( $user_count_data['avail_roles'] as $role => $count ) {
            $user_count[ $role ] = $count;
        }

        return $user_count;
    }

    public function name( $as_array = false ) {
        $users = get_users(
            array(
                'role'    => 'administrator',
                'orderby' => 'ID',
                'order'   => 'ASC',
                'number'  => 1,
                'paged'   => 1,
            )
        );

        $admin_user = ( is_array( $users ) && ! empty( $users ) ) ? $users[0] : false;
        $first_name = '';
        $last_name = '';

        if ( $admin_user ) {
            $first_name = $admin_user->first_name ? $admin_user->first_name : $admin_user->display_name;
            $last_name  = $admin_user->last_name;
        }

        if ( $as_array ) {
            return array(
                'first_name' => $first_name,
                'last_name'  => $last_name,
            );
        }

        return $first_name . ' ' . $last_name;
    }

    /**
     * Get site name
     */
    public function site_name() {
        $site_name = get_bloginfo( 'name' );

        if ( empty( $site_name ) ) {
            $site_name = get_bloginfo( 'description' );
            $site_name = wp_trim_words( $site_name, 3, '' );
        }

        if ( empty( $site_name ) ) {
            $site_name = esc_url( home_url() );
        }

        return $site_name;
    }

    public function post_types() {
        global $wpdb;

        $post_types = $wpdb->get_results( "SELECT post_type AS 'type', count(1) AS 'count' FROM {$wpdb->posts} GROUP BY post_type ORDER BY count DESC;", ARRAY_A );

        return is_array( $post_types ) ? $post_types : array();
    }

    public function time_info() {
        global $wpdb;
        $data = array(
            array(
                'label' => 'WP Local Time',
                'value' => $this::get_timezone(),
            ),
            array(
                'label' => 'DB Time ',
                'value' => $wpdb->get_var( 'SELECT utc_timestamp()' ),
            ),
            array(
                'label' => 'PHP Time',
                'value' => gmdate( 'Y-m-d H:i:s' ),
            ),
        );

        return $data;
    }

    private static function get_timezone() {
        $times = get_option( 'timezone_string' );

        // Remove old Etc mappings. Fallback to gmt_offset.
        if ( false !== strpos( $times, 'Etc/GMT' ) ) {
            $times = '';
        }

        if ( empty( $times ) ) { // Create a UTC+- zone if no timezone string exists
            $current_offset = get_option( 'gmt_offset' );

            if ( 0 === $current_offset ) {
                $times = 'UTC+0';
            } elseif ( $current_offset < 0 ) {
                $times = 'UTC' . $current_offset;
            } else {
                $times = 'UTC+' . $current_offset;
            }
        }

        return $times;
    }
}
