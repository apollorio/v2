<?php
/**
 * Helper Functions
 *
 * @package Apollo\Users
 */

declare(strict_types=1);

namespace Apollo\Users;

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get plugin instance
 *
 * @return Plugin
 */
function apollo_users(): Plugin {
	return Plugin::get_instance();
}

/**
 * Get user profile URL
 *
 * @param int|null $user_id User ID (defaults to current user)
 * @return string Profile URL
 */
function apollo_get_profile_url( ?int $user_id = null ): string {
	if ( null === $user_id ) {
		$user_id = get_current_user_id();
	}

	if ( ! $user_id ) {
		return '';
	}

	$user = get_userdata( $user_id );
	if ( ! $user ) {
		return '';
	}

	return home_url( '/id/' . $user->user_login . '/' );
}

/**
 * Get user by username (case-insensitive)
 *
 * @param string $username Username
 * @return \WP_User|false
 */
function apollo_get_user_by_username( string $username ) {
	global $wpdb;

	// Try exact match first
	$user = get_user_by( 'login', $username );
	if ( $user ) {
		return $user;
	}

	// Case-insensitive search
	$user_id = $wpdb->get_var(
		$wpdb->prepare(
			"SELECT ID FROM {$wpdb->users} WHERE LOWER(user_login) = LOWER(%s) LIMIT 1",
			$username
		)
	);

	if ( $user_id ) {
		return get_user_by( 'ID', $user_id );
	}

	return false;
}

/**
 * Get user avatar URL
 *
 * @param int    $user_id User ID
 * @param string $size Size (thumb, medium, full)
 * @return string Avatar URL
 */
function apollo_get_user_avatar_url( int $user_id, string $size = 'thumb' ): string {
	// Check for custom avatar first
	$custom_avatar_id = get_user_meta( $user_id, 'custom_avatar', true );
	if ( $custom_avatar_id ) {
		$image_size = 'thumb' === $size ? 'thumbnail' : ( 'medium' === $size ? 'medium' : 'full' );
		$image      = wp_get_attachment_image_url( $custom_avatar_id, $image_size );
		if ( $image ) {
			return $image;
		}
	}

	// Check for Instagram avatar (from apollo-login)
	$instagram_avatar = get_user_meta( $user_id, '_apollo_avatar_url', true );
	if ( $instagram_avatar ) {
		return $instagram_avatar;
	}

	// Check avatar_thumb meta
	$avatar_thumb = get_user_meta( $user_id, 'avatar_thumb', true );
	if ( $avatar_thumb ) {
		return $avatar_thumb;
	}

	// Fallback to Gravatar
	return get_avatar_url( $user_id, array( 'size' => 'thumb' === $size ? 96 : ( 'medium' === $size ? 256 : 512 ) ) );
}

/**
 * Get user cover image URL
 *
 * @param int $user_id User ID
 * @return string Cover URL or empty
 */
function apollo_get_user_cover_url( int $user_id ): string {
	$cover_id = get_user_meta( $user_id, 'cover_image', true );
	if ( $cover_id ) {
		$image = wp_get_attachment_image_url( $cover_id, 'full' );
		if ( $image ) {
			return $image;
		}
	}
	return '';
}

/**
 * Get user membership badge
 *
 * @param int $user_id User ID
 * @return array Badge info with type, label, color
 */
function apollo_get_user_membership( int $user_id ): array {
	$type = get_user_meta( $user_id, '_apollo_membership', true );

	if ( empty( $type ) ) {
		$type = 'nao-verificado';
	}

	$badges = array(
		'nao-verificado' => array(
			'label' => 'Não Verificado',
			'color' => '#6b7280',
			'icon'  => 'ri-user-line',
		),
		'apollo'         => array(
			'label' => 'Apollo',
			'color' => '#22c55e',
			'icon'  => 'ri-user-star-line',
		),
		'prod'           => array(
			'label' => 'Produtor',
			'color' => '#8b5cf6',
			'icon'  => 'ri-vip-crown-line',
		),
		'dj'             => array(
			'label' => 'DJ',
			'color' => '#f59e0b',
			'icon'  => 'ri-disc-line',
		),
		'host'           => array(
			'label' => 'Host',
			'color' => '#ec4899',
			'icon'  => 'ri-mic-line',
		),
		'govern'         => array(
			'label' => 'Governança',
			'color' => '#3b82f6',
			'icon'  => 'ri-shield-star-line',
		),
		'business-pers'  => array(
			'label' => 'Business',
			'color' => '#14b8a6',
			'icon'  => 'ri-briefcase-line',
		),
	);

	return array_merge( array( 'type' => $type ), $badges[ $type ] ?? $badges['nao-verificado'] );
}

/**
 * Check if user profile is viewable
 *
 * @param int      $profile_user_id Profile owner ID
 * @param int|null $viewer_id Viewer ID (null for guest)
 * @return bool
 */
function apollo_can_view_profile( int $profile_user_id, ?int $viewer_id = null ): bool {
	$privacy = get_user_meta( $profile_user_id, '_apollo_privacy_profile', true );

	if ( empty( $privacy ) || 'public' === $privacy ) {
		return true;
	}

	if ( 'members' === $privacy ) {
		return $viewer_id > 0;
	}

	// Private - only self or admin
	if ( 'private' === $privacy ) {
		if ( null === $viewer_id ) {
			return false;
		}
		return $viewer_id === $profile_user_id || user_can( $viewer_id, 'manage_options' );
	}

	return true;
}

/**
 * Record profile view
 *
 * @param int      $profile_user_id Profile owner ID
 * @param int|null $viewer_id Viewer ID
 * @return void
 */
function apollo_record_profile_view( int $profile_user_id, ?int $viewer_id = null ): void {
	global $wpdb;

	// Don't count self views
	if ( $viewer_id === $profile_user_id ) {
		return;
	}

	$table = $wpdb->prefix . APOLLO_USERS_TABLE_PROFILE_VIEWS;

	// Insert view record
	$wpdb->insert(
		$table,
		array(
			'profile_user_id' => $profile_user_id,
			'viewer_user_id'  => $viewer_id,
			'viewer_ip'       => $_SERVER['REMOTE_ADDR'] ?? '',
			'viewed_at'       => current_time( 'mysql' ),
		),
		array( '%d', '%d', '%s', '%s' )
	);

	// Update total count
	$count = (int) get_user_meta( $profile_user_id, '_apollo_profile_views', true );
	update_user_meta( $profile_user_id, '_apollo_profile_views', $count + 1 );
}

/**
 * Get user's social name (display name preference)
 *
 * @param int $user_id User ID
 * @return string
 */
function apollo_get_social_name( int $user_id ): string {
	// First check apollo social name
	$social_name = get_user_meta( $user_id, '_apollo_social_name', true );
	if ( $social_name ) {
		return $social_name;
	}

	// Fallback to display_name
	$user = get_userdata( $user_id );
	return $user ? $user->display_name : '';
}

/**
 * Get user bio
 *
 * @param int $user_id User ID
 * @return string
 */
function apollo_get_user_bio( int $user_id ): string {
	$bio = get_user_meta( $user_id, '_apollo_bio', true );
	if ( $bio ) {
		return $bio;
	}

	// Fallback to WP description
	$user = get_userdata( $user_id );
	return $user ? $user->description : '';
}

/**
 * Enqueue page-specific assets
 *
 * @param string $page_type The type of page (radar, profile, edit-profile)
 * @return void
 */
function apollo_users_enqueue_page_assets( string $page_type ): void {
	$plugin_url = plugin_dir_url( APOLLO_USERS_FILE );
	$version    = defined( 'WP_DEBUG' ) && WP_DEBUG ? time() : APOLLO_USERS_VERSION;

	switch ( $page_type ) {
		case 'radar':
			wp_enqueue_style(
				'apollo-users-radar',
				$plugin_url . 'assets/css/radar.css',
				array(),
				$version
			);
			break;

		case 'profile':
			wp_enqueue_style(
				'apollo-users-profile',
				$plugin_url . 'assets/css/profile.css',
				array(),
				$version
			);
			wp_enqueue_script(
				'apollo-users-profile',
				$plugin_url . 'assets/js/profile.js',
				array( 'jquery' ),
				$version,
				true
			);
			break;

		case 'edit-profile':
			wp_enqueue_style(
				'apollo-users-edit-profile',
				$plugin_url . 'assets/css/edit-profile.css',
				array(),
				$version
			);
			break;
	}
}

/**
 * Check if user is verified
 *
 * @param int $user_id User ID
 * @return bool
 */
function apollo_is_user_verified( int $user_id ): bool {
	return (bool) get_user_meta( $user_id, '_apollo_user_verified', true );
}

/**
 * Verify user account
 *
 * @param int $user_id User ID
 * @return bool Success
 */
function apollo_verify_user( int $user_id ): bool {
	$result = update_user_meta( $user_id, '_apollo_user_verified', true );

	// Upgrade membership if still unverified
	$membership = get_user_meta( $user_id, '_apollo_membership', true );
	if ( 'nao-verificado' === $membership ) {
		update_user_meta( $user_id, '_apollo_membership', 'apollo' );
	}

	/**
	 * Fires after a user is verified
	 *
	 * @param int $user_id User ID
	 */
	do_action( 'apollo_user_verified', $user_id );

	return $result !== false;
}

/**
 * Get user profile completion percentage
 *
 * @param int $user_id User ID
 * @return int Percentage (0-100)
 */
function apollo_get_profile_completion( int $user_id ): int {
	$completion = get_user_meta( $user_id, '_apollo_profile_completed', true );
	return $completion ? (int) $completion : 0;
}

/**
 * Check if user has Apollo capability
 *
 * @param int    $user_id User ID
 * @param string $capability Capability name
 * @return bool
 */
function apollo_user_can( int $user_id, string $capability ): bool {
	$user = get_userdata( $user_id );
	if ( ! $user ) {
		return false;
	}

	return $user->has_cap( $capability );
}

/**
 * Get user's Apollo role
 *
 * @param int $user_id User ID
 * @return string|null Apollo role or null
 */
function apollo_get_user_role( int $user_id ): ?string {
	$user = get_userdata( $user_id );
	if ( ! $user ) {
		return null;
	}

	$apollo_roles = array( 'apollo_member', 'apollo_producer', 'apollo_dj', 'apollo_venue', 'apollo_moderator' );

	foreach ( $user->roles as $role ) {
		if ( in_array( $role, $apollo_roles, true ) ) {
			return $role;
		}
	}

	return null;
}

/**
 * Get complete user display data for posts/comments/activities
 * Returns: name, badges, memberships, núcleos, @handle, time since registration
 *
 * @param int $user_id User ID
 * @return array Structured user data
 */
function apollo_get_user_display_data( int $user_id ): array {
	$user = get_userdata( $user_id );
	if ( ! $user ) {
		return array();
	}

	$data = array(
		'id'           => $user_id,
		'display_name' => $user->display_name,
		'user_login'   => $user->user_login,
		'handle'       => '@' . $user->user_login,
		'profile_url'  => home_url( '/id/' . $user->user_login ),
		'avatar_url'   => function_exists( 'apollo_get_user_avatar_url' )
			? apollo_get_user_avatar_url( $user_id )
			: get_avatar_url( $user_id, array( 'size' => 48 ) ),
	);

	// Badge data
	if ( function_exists( 'apollo_get_user_badge_data' ) ) {
		$data['badge'] = apollo_get_user_badge_data( $user_id );
	} else {
		$data['badge'] = array(
			'type'    => 'nao-verificado',
			'label'   => '',
			'ri_icon' => '',
			'color'   => '',
		);
	}

	// Memberships (active subscriptions)
	if ( function_exists( 'apollo_get_user_membership' ) ) {
		$membership         = apollo_get_user_membership( $user_id );
		$data['membership'] = array(
			'level'      => $membership['level'] ?? 'free',
			'status'     => $membership['status'] ?? 'inactive',
			'label'      => $membership['label'] ?? '',
			'is_premium' => isset( $membership['level'] ) && $membership['level'] !== 'free',
		);
	} else {
		$data['membership'] = array(
			'level'      => 'free',
			'status'     => 'inactive',
			'label'      => '',
			'is_premium' => false,
		);
	}

	// Núcleos/Groups user participates in
	if ( function_exists( 'apollo_get_user_groups' ) ) {
		$groups              = apollo_get_user_groups( $user_id, 5 );
		$data['nucleos']     = array();
		$data['nucleos_raw'] = $groups;

		foreach ( $groups as $group ) {
			if ( isset( $group['type'] ) && $group['type'] === 'nucleo' ) {
				$data['nucleos'][] = array(
					'id'   => $group['id'] ?? 0,
					'name' => $group['name'] ?? '',
					'slug' => $group['slug'] ?? '',
				);
			}
		}
	} else {
		$data['nucleos']     = array();
		$data['nucleos_raw'] = array();
	}

	// Time since registration
	if ( isset( $user->user_registered ) ) {
		$registered_timestamp = strtotime( $user->user_registered );
		$now                  = time();
		$diff                 = $now - $registered_timestamp;

		$data['registered_date'] = $user->user_registered;
		$data['member_since']    = date( 'Y', $registered_timestamp );

		// Calculate time ago
		if ( function_exists( 'apollo_time_ago' ) ) {
			$data['member_for'] = apollo_time_ago( $user->user_registered );
		} else {
			$years = floor( $diff / ( 365 * 24 * 60 * 60 ) );
			if ( $years > 0 ) {
				$data['member_for'] = $years . ' ano' . ( $years > 1 ? 's' : '' );
			} else {
				$months = floor( $diff / ( 30 * 24 * 60 * 60 ) );
				if ( $months > 0 ) {
					$data['member_for'] = $months . ' ' . ( $months > 1 ? 'meses' : 'mês' );
				} else {
					$days               = floor( $diff / ( 24 * 60 * 60 ) );
					$data['member_for'] = $days . ' ' . ( $days > 1 ? 'dias' : 'dia' );
				}
			}
		}
	} else {
		$data['registered_date'] = '';
		$data['member_since']    = '';
		$data['member_for']      = '';
	}

	return $data;
}
