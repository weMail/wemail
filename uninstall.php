<?php

/**
 * Uninstall routine for the weMail plugin
 *
 * Runs when the plugin is deleted from the WordPress admin. Deletes the
 * plugin's table, options, user meta, widget option and transients for the
 * current site. It deliberately does not load the plugin or its API client.
 *
 * The cloud is not notified here on purpose: WordPress only offers "Delete"
 * for an already-deactivated plugin, so the deactivation hook
 * (includes/Uninstall.php) has already told the cloud `deactivated: true`.
 *
 * @since 2.1.7
 */

// don't call the file directly
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

global $wpdb;

// Drop the forms table (schema: includes/Upgrades/upgrade-1.0.0.php).
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.SchemaChange -- one-time uninstall cleanup, table name built from $wpdb->prefix
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}wemail_forms" );

// Delete every weMail option in one prefix query. This runs on the current
// site only; network-wide option cleanup across all sites is out of scope.
$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->options WHERE option_name LIKE %s", $wpdb->esc_like( 'wemail_' ) . '%' ) );
wp_cache_delete( 'alloptions', 'options' );

// The widget option has no wemail_ prefix, so delete it explicitly.
delete_option( 'widget_wemail-form-widget' );

// Delete weMail user meta for every user.
$wemail_meta_keys = array( 'wemail_api_key', 'wemail_user_data', 'wemail_disable_user_access' );
$placeholders     = implode( ', ', array_fill( 0, count( $wemail_meta_keys ), '%s' ) );

// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare -- {$wpdb->usermeta} is a core table property, values prepared
$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->usermeta} WHERE meta_key IN ($placeholders)", $wemail_meta_keys ) );

// Transient rows use a _transient_/_transient_timeout_ prefix, so the option
// query above does not catch them; clear the known ones explicitly.
delete_transient( 'wemail_activation_redirect' );
delete_transient( 'wemail_sent_campaign_count' );
delete_transient( 'wemail_validate_me_key' );
