<?php
/**
 * Helper Functions
 *
 * @package Apollo\Users
 */

declare(strict_types=1);

namespace Apollo\Users;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get plugin instance
 */
function apollo_users(): Plugin {
	return Plugin::get_instance();
}

/**
 * Get user profile URL - ALWAYS /id/username
 */
function apollo_get_profile_url( $user_id_or_user = null ): string {
	if ( null === $user_id_or_user ) {
		$user_id_or_user = get_current_user_id();
	}

	if ( $user_id_or_user instanceof \WP_User ) {
		return home_url( '/id/' . $user_id_or_user->user_login . '/' );
	}

	$user_id = (int) $user_id_or_user;
	if ( ! $user_id ) return '';

	$user = get_userdata( $user_id );
	if ( ! $user ) return '';

	return home_url( '/id/' . $user->user_login . '/' );
}

/**
 * Get user by username (case-insensitive)
 */
function apollo_get_user_by_username( string $username ) {
	global $wpdb;

	$user = get_user_by( 'login', $username );
	if ( $user ) return $user;

	$user_id = $wpdb->get_var( $wpdb->prepare(
		"SELECT ID FROM {$wpdb->users} WHERE LOWER(user_login) = LOWER(%s) LIMIT 1",
		$username
	) );

	return $user_id ? get_user_by( 'ID', $user_id ) : false;
}

/**
 * Get user avatar URL (cascading: custom_avatar → _apollo_avatar_url → avatar_thumb → gravatar)
 */
function apollo_get_user_avatar_url( int $user_id, string $size = 'thumb' ): string {
	$custom_avatar_id = get_user_meta( $user_id, 'custom_avatar', true );
	if ( $custom_avatar_id ) {
		$image_size = 'thumb' === $size ? 'thumbnail' : ( 'medium' === $size ? 'medium' : 'full' );
		$image = wp_get_attachment_image_url( (int) $custom_avatar_id, $image_size );
		if ( $image ) return $image;
	}

	$instagram_avatar = get_user_meta( $user_id, '_apollo_avatar_url', true );
	if ( $instagram_avatar ) return $instagram_avatar;

	$avatar_thumb = get_user_meta( $user_id, 'avatar_thumb', true );
	if ( $avatar_thumb ) return $avatar_thumb;

	return get_avatar_url( $user_id, [ 'size' => 'thumb' === $size ? 96 : ( 'medium' === $size ? 256 : 512 ) ] );
}

/**
 * Get user cover image URL
 */
function apollo_get_user_cover_url( int $user_id ): string {
	$cover_id = get_user_meta( $user_id, 'cover_image', true );
	if ( $cover_id ) {
		$image = wp_get_attachment_image_url( (int) $cover_id, 'full' );
		if ( $image ) return $image;
	}
	return '';
}

/**
 * Get user membership badge info
 */
function apollo_get_user_membership( int $user_id ): array {
	$type = get_user_meta( $user_id, '_apollo_membership', true ) ?: 'nao-verificado';

	$badges = [
		'nao-verificado' => [ 'label' => 'Não Verificado', 'color' => '#6b7280', 'icon' => 'ri-user-line' ],
		'apollo'         => [ 'label' => 'Apollo', 'color' => '#22c55e', 'icon' => 'ri-user-star-line' ],
		'prod'           => [ 'label' => 'Produtor', 'color' => '#8b5cf6', 'icon' => 'ri-vip-crown-line' ],
		'dj'             => [ 'label' => 'DJ', 'color' => '#f59e0b', 'icon' => 'ri-disc-line' ],
		'host'           => [ 'label' => 'Host', 'color' => '#ec4899', 'icon' => 'ri-mic-line' ],
		'govern'         => [ 'label' => 'Governança', 'color' => '#3b82f6', 'icon' => 'ri-shield-star-line' ],
		'business-pers'  => [ 'label' => 'Business', 'color' => '#14b8a6', 'icon' => 'ri-briefcase-line' ],
	];

	return array_merge( [ 'type' => $type ], $badges[ $type ] ?? $badges['nao-verificado'] );
}

/**
 * Check if user profile is viewable
 */
function apollo_can_view_profile( int $profile_user_id, ?int $viewer_id = null ): bool {
	$privacy = get_user_meta( $profile_user_id, '_apollo_privacy_profile', true );

	if ( empty( $privacy ) || 'public' === $privacy ) return true;
	if ( 'members' === $privacy ) return $viewer_id > 0;
	if ( 'private' === $privacy ) {
		if ( null === $viewer_id ) return false;
		return $viewer_id === $profile_user_id || user_can( $viewer_id, 'manage_options' );
	}

	return true;
}

/**
 * Record profile view
 */
function apollo_record_profile_view( int $profile_user_id, ?int $viewer_id = null ): void {
	global $wpdb;

	if ( $viewer_id === $profile_user_id ) return;

	$table = $wpdb->prefix . APOLLO_USERS_TABLE_PROFILE_VIEWS;
	if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table}'" ) !== $table ) return;

	$wpdb->insert( $table, [
		'profile_user_id' => $profile_user_id,
		'viewer_user_id'  => $viewer_id,
		'viewer_ip'       => $_SERVER['REMOTE_ADDR'] ?? '',
		'viewed_at'       => current_time( 'mysql' ),
	], [ '%d', '%d', '%s', '%s' ] );

	$count = (int) get_user_meta( $profile_user_id, '_apollo_profile_views', true );
	update_user_meta( $profile_user_id, '_apollo_profile_views', $count + 1 );
}

/**
 * Get social name (display preference)
 */
function apollo_get_social_name( int $user_id ): string {
	$social_name = get_user_meta( $user_id, '_apollo_social_name', true );
	if ( $social_name ) return $social_name;

	$user = get_userdata( $user_id );
	return $user ? $user->display_name : '';
}

/**
 * Get user bio
 */
function apollo_get_user_bio( int $user_id ): string {
	$bio = get_user_meta( $user_id, '_apollo_bio', true );
	if ( $bio ) return $bio;

	$user = get_userdata( $user_id );
	return $user ? $user->description : '';
}

/**
 * Get client IP
 */
function apollo_get_client_ip(): string {
	$ip_keys = [ 'HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR' ];
	foreach ( $ip_keys as $key ) {
		if ( ! empty( $_SERVER[ $key ] ) ) {
			$ip = trim( explode( ',', $_SERVER[ $key ] )[0] );
			if ( filter_var( $ip, FILTER_VALIDATE_IP ) ) return $ip;
		}
	}
	return '';
}

/**
 * Enqueue page-specific assets
 */
function apollo_users_enqueue_page_assets( string $page_type ): void {
	$url     = plugin_dir_url( APOLLO_USERS_FILE );
	$version = defined( 'WP_DEBUG' ) && WP_DEBUG ? (string) time() : APOLLO_USERS_VERSION;

	switch ( $page_type ) {
		case 'radar':
			wp_enqueue_style( 'apollo-users-radar', $url . 'assets/css/radar.css', [], $version );
			break;

		case 'profile':
			wp_enqueue_style( 'apollo-users-profile', $url . 'assets/css/profile.css', [], $version );
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'apollo-users-profile', $url . 'assets/js/profile.js', [ 'jquery' ], $version, true );
			break;

		case 'edit-profile':
			wp_enqueue_style( 'apollo-users-edit-profile', $url . 'assets/css/edit-profile.css', [], $version );
			break;

		case 'account':
			wp_enqueue_style( 'apollo-users-account', $url . 'assets/css/account.css', [], $version );
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'apollo-users-account', $url . 'assets/js/account.js', [ 'jquery' ], $version, true );
			break;
	}
}
