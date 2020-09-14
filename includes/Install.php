<?php

namespace WeDevs\WeMail;

/**
 * Installer class
 */
class Install {

    /**
     * Run the installer
     *
     * @return void
     */
    public static function install() {
        // create tables
        self::create_tables();

        // update the current version
        update_option( 'wemail_version', WEMAIL_VERSION );

        // let the API know we are active again
        $api_key    = get_user_meta( get_current_user_id(), 'wemail_api_key', true );
        $api        = apply_filters( 'wemail_api_url', 'https://api.getwemail.io/v1' );
        $wemail_api = untrailingslashit( $api );

        wp_remote_post(
            $wemail_api . '/site/update-activation-status',
            [
                'headers' => [
                    'x-api-key' => $api_key,
                ],
                'body' => [
                    'deactivated' => false,
                ],
            ]
        );

        // set the redirection to setup wizard
        set_transient( 'wemail_activation_redirect', true, 30 );
    }

    /**
     * Create the necessary tables
     *
     * @return void
     */
    public static function create_tables() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();
        $table_schema    = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}wemail_forms` (
            `id` varchar(36) NOT NULL,
            `name` varchar(150) DEFAULT NULL,
            `template` longtext NULL,
            `plugin_version` varchar(10) NULL,
            `settings` longtext NULL,
            `type` varchar(191) DEFAULT 'inline',
            `status` tinyint(1) DEFAULT '1',
            `deleted_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`)
        ) {$charset_collate}";

        if ( ! function_exists( 'dbDelta' ) ) {
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        }

        dbDelta( $table_schema );
    }
}
