<?php

namespace WeDevs\WeMail\Core\Help\Services;

class SystemService {
    private $db;

    public function __construct() {
        global $wpdb;

        $this->db = $wpdb;
    }

    /**
     * Get server related info.
     *
     * @return array
     */
    public function get_server_info() {
        $server_data = [];

        if ( function_exists( 'phpversion' ) ) {
            $server_data['php_version'] = phpversion();
        }

        $extensions   = get_loaded_extensions();
        $curl_version = curl_version();

        $server_data['software']                    = $this->server_software();
        $server_data['ip_address']                  = $this->server_ip_address();
        $server_data['protocol']                    = $this->server_protocol();
        $server_data['administrator']               = $this->server_administrator();
        $server_data['webport']                     = $this->server_webport();
        $server_data['cgi_version']                 = $this->cgi_version();
        $server_data['mysql_version']               = $this->db->db_version();
        $server_data['php_max_upload_size']         = size_format( wp_max_upload_size() );
        $server_data['php_default_timezone']        = date_default_timezone_get();
        $server_data['php_soap']                    = class_exists( 'SoapClient' ) ? 'Yes' : 'No';
        $server_data['php_fsockopen']               = function_exists( 'fsockopen' ) ? 'Yes' : 'No';
        $server_data['php_dom_document']            = class_exists( 'DOMDocument' ) ? 'Yes' : 'No';
        $server_data['php_curl']                    = function_exists( 'curl_init' ) ? 'Yes' : 'No';
        $server_data['php_curl_version']            = $curl_version['version'] . ', ' . $curl_version['ssl_version'];
        $server_data['php_extensions']              = implode( ', ', $extensions );
        $server_data['php_default_timezone']        = date_default_timezone_get();
        $server_data['php_max_input_vars']          = ini_get( 'max_input_vars' );
        $server_data['php_max_execution_time']      = ini_get( 'max_execution_time' );
        $server_data['gzip']                        = is_callable( 'gzopen' ) ? 'Yes' : 'No';
        $server_data['mbstring']                    = extension_loaded( 'mbstring' ) ? 'Yes' : 'No';
        $server_data['is_ssl']                      = is_ssl();

        return $server_data;
    }

    private function server_software() {
        if ( isset( $_SERVER['SERVER_SOFTWARE'] ) && ! empty( $_SERVER['SERVER_SOFTWARE'] ) ) {
            return esc_url_raw( wp_unslash( $_SERVER['SERVER_SOFTWARE'] ) );
        }
    }

    private function server_ip_address() {
        return isset( $_SERVER['SERVER_ADDR'] ) ? esc_url_raw( wp_unslash( $_SERVER['SERVER_ADDR'] ) ) : '';
    }

    private function server_protocol() {
        return php_uname( 'n' );
    }

    private function server_administrator() {
        return isset( $_SERVER['SERVER_ADMIN'] ) ? esc_url_raw( wp_unslash( $_SERVER['SERVER_ADMIN'] ) ) : '';
    }

    private function server_webport() {
        return isset( $_SERVER['SERVER_PORT'] ) ? esc_url_raw( wp_unslash( $_SERVER['SERVER_PORT'] ) ) : '';
    }

    private function cgi_version() {
        return isset( $_SERVER['GATEWAY_INTERFACE'] ) ? esc_url_raw( wp_unslash( $_SERVER['GATEWAY_INTERFACE'] ) ) : '';
    }
}
