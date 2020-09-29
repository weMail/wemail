<?php

namespace WeDevs\WeMail\Core\Help;

use WeDevs\WeMail\Core\Help\Services\SystemService;
use WeDevs\WeMail\Core\Help\Services\WordpressInfo;

class SystemInfo {
    protected $system;

    protected $wp;

    public function __construct() {
        $this->system = new SystemService();
        $this->wp     = new WordpressInfo();
    }

    public function allInfo() {
        $all_plugins    = $this->get_all_plugins();
        $name           = $this->name( true );

        return [
            'admin_email'               => get_option( 'admin_email' ),
            'first_name'                => $name['first_name'],
            'last_name'                 => $name['last_name'],
            'ip_address'                => $this->get_user_ip_address(),
            'plugins'                   => $all_plugins,
            'site_name'                 => $this->site_name(),
            'users'                     => $this->get_user_counts(),
            'wp'                        => $this->wp->get_wp_info(),
            'server'                    => $this->system->get_server_info(),
            'post_types'                => $this->post_types(),
            'time_info'                 => $this->time_info(),
        ];
    }

    /**
     * Get user IP Address
     */
    private function get_user_ip_address() {
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
     * Get the list of active and inactive plugins
     *
     * @return array
     */
    private function get_all_plugins() {
        // Ensure get_plugins function is loaded
        if ( ! function_exists( 'get_plugins' ) ) {
            include ABSPATH . '/wp-admin/includes/plugin.php';
        }

        $plugins             = get_plugins();
        $active_plugins_keys = get_option( 'active_plugins', [] );
        $active_plugins      = [];

        foreach ( $plugins as $k => $v ) {
            // Take care of formatting the data how we want it.
            $formatted         = [];
            $formatted['name'] = strip_tags( $v['Name'] );

            if ( isset( $v['Version'] ) ) {
                $formatted['version'] = strip_tags( $v['Version'] );
            }

            if ( isset( $v['Author'] ) ) {
                $formatted['author'] = strip_tags( $v['Author'] );
            }

            if ( isset( $v['Network'] ) ) {
                $formatted['network'] = strip_tags( $v['Network'] );
            }

            if ( isset( $v['AuthorURI'] ) ) {
                $formatted['author_uri'] = strip_tags( $v['AuthorURI'] );
            }

            if ( isset( $v['PluginURI'] ) ) {
                $formatted['plugin_uri'] = strip_tags( $v['PluginURI'] );
            }

            if ( in_array( $k, $active_plugins_keys, true ) ) {
                // Remove active plugins from list so we can show active and inactive separately
                unset( $plugins[ $k ] );
                $active_plugins[ $k ] = $formatted;
            } else {
                $plugins[ $k ] = $formatted;
            }
        }

        return [
			'active_plugins' => $active_plugins,
			'inactive_plugins' => $plugins,
		];
    }

    /**
     * Get user totals based on user role.
     *
     * @return array
     */
    public function get_user_counts() {
        $user_count          = [];
        $user_count_data     = count_users();
        $user_count['total'] = $user_count_data['total_users'];

        // Get user count based on user role
        foreach ( $user_count_data['avail_roles'] as $role => $count ) {
            $user_count[ $role ] = $count;
        }

        return $user_count;
    }

    private function name( $as_array = false ) {
        $users = get_users(
            [
				'role'    => 'administrator',
				'orderby' => 'ID',
				'order'   => 'ASC',
				'number'  => 1,
				'paged'   => 1,
			]
        );

        $admin_user = ( is_array( $users ) && ! empty( $users ) ) ? $users[0] : false;
        $first_name = '';
        $last_name = '';

        if ( $admin_user ) {
            $first_name = $admin_user->first_name ? $admin_user->first_name : $admin_user->display_name;
            $last_name  = $admin_user->last_name;
        }

        if ( $as_array ) {
            return [
                'first_name' => $first_name,
                'last_name'  => $last_name,
            ];
        }

        return $first_name . ' ' . $last_name;
    }

    /**
     * Get site name
     */
    private function site_name() {
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

    private function post_types() {
        global $wpdb;

        $post_types = $wpdb->get_results( "SELECT post_type AS 'type', count(1) AS 'count' FROM {$wpdb->posts} GROUP BY post_type ORDER BY count DESC;", ARRAY_A );

        return is_array( $post_types ) ? $post_types : [];
    }

    private function time_info() {
        global $wpdb;
        $data = [
            [
                'label' => 'WP Local Time',
                'value' => $this::get_timezone(),
            ],
            [
                'label' => 'DB Time ',
                'value' => $wpdb->get_var( 'SELECT utc_timestamp()' ),
            ],
            [
                'label' => 'PHP Time',
                'value' => gmdate( 'Y-m-d H:i:s' ),
            ],
        ];

        return $data;
    }

    public static function get_timezone() {
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
