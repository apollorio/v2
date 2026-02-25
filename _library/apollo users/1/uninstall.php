<?php
/**
 * Uninstall Handler
 *
 * Cleans up all plugin data on full uninstall.
 *
 * @package Apollo\Users
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

// Drop custom tables
$tables = [
	$wpdb->prefix . 'apollo_matchmaking',
	$wpdb->prefix . 'apollo_user_fields',
	$wpdb->prefix . 'apollo_profile_views',
	$wpdb->prefix . 'apollo_user_ratings',
];

foreach ( $tables as $table ) {
	$wpdb->query( "DROP TABLE IF EXISTS {$table}" );
}

// Remove user meta keys
$meta_keys = [
	'_apollo_user_verified',
	'_apollo_membership',
	'_apollo_profile_completed',
	'_apollo_matchmaking_data',
	'cover_image',
	'custom_avatar',
	'avatar_thumb',
	'instagram',
	'user_location',
	'_apollo_bio',
	'_apollo_website',
	'_apollo_phone',
	'_apollo_birth_date',
	'_apollo_privacy_profile',
	'_apollo_privacy_email',
	'_apollo_disable_author_url',
	'_apollo_profile_views',
	'_apollo_social_name',
	'_apollo_fav_count',
];

foreach ( $meta_keys as $key ) {
	$wpdb->query( $wpdb->prepare(
		"DELETE FROM {$wpdb->usermeta} WHERE meta_key = %s",
		$key
	) );
}

// Remove depoimentos (comments of type apollo_depoimento)
$wpdb->query(
	"DELETE FROM {$wpdb->comments} WHERE comment_type = 'apollo_depoimento'"
);
$wpdb->query(
	"DELETE FROM {$wpdb->commentmeta} WHERE comment_id NOT IN (SELECT comment_ID FROM {$wpdb->comments})"
);

// Remove options
$options = [
	'apollo_users_activated',
	'apollo_users_version',
	'apollo_users_profile_slug',
	'apollo_users_radar_slug',
	'apollo_users_block_author_enum',
	'apollo_users_default_privacy',
];

foreach ( $options as $opt ) {
	delete_option( $opt );
}

// Flush rewrite rules
flush_rewrite_rules();
