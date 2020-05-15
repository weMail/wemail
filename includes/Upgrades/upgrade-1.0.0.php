<?php

/**
 * Create {prefix}wemail_forms table
 */

function wemail_upgrade_1_0_0_create_wemail_tables() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    $table_schema = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}wemail_forms` (
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

wemail_upgrade_1_0_0_create_wemail_tables();
