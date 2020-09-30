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
        $wp_data = [];

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

        return $upload_dir['baseurl'];
    }
}
