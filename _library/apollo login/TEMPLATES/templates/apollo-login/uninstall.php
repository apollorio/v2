<?php
/**
 * Uninstall Apollo Login
 *
 * @package Apollo\Login
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

// Delete options
$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE 'apollo_login_%'" );

// Delete user meta
$meta_keys = [
	'_apollo_social_name', '_apollo_instagram', '_apollo_avatar_url',
	'_apollo_avatar_attachment_id', '_apollo_sound_preferences',
	'_apollo_quiz_score', '_apollo_simon_highscore', '_apollo_quiz_answers',
	'_apollo_email_verified', '_apollo_verification_token',
	'_apollo_password_reset_token', '_apollo_password_reset_expires',
	'_apollo_login_attempts', '_apollo_last_login', '_apollo_lockout_until',
	'_apollo_cpf', '_apollo_passport', '_apollo_passport_country', '_apollo_doc_type',
];

foreach ( $meta_keys as $key ) {
	$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->usermeta} WHERE meta_key = %s", $key ) );
}

// Drop custom tables
$tables = [
	'apollo_quiz_results',
	'apollo_simon_scores',
	'apollo_login_attempts',
	'apollo_url_rewrites',
];

foreach ( $tables as $table ) {
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}{$table}" );
}

wp_cache_flush();
