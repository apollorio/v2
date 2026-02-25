<?php
/**
 * Uninstall Apollo {Name}
 *
 * Removes all plugin data when the plugin is deleted.
 * This file is called by WordPress when the plugin is deleted.
 *
 * @package Apollo\{Namespace}
 */

// If uninstall not called from WordPress, exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

// Delete options
$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE 'apollo_{slug}_%'" );

// Delete user meta
$wpdb->query( "DELETE FROM {$wpdb->usermeta} WHERE meta_key LIKE '_apollo_{slug}_%'" );

// Delete post meta (if applicable)
// $wpdb->query( "DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE '_apollo_{slug}_%'" );

// Drop custom tables (if applicable)
// $wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}apollo_{slug}_example" );

// Clear any cached data
wp_cache_flush();
