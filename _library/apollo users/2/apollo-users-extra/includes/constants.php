<?php
/**
 * Plugin Constants
 *
 * @package Apollo\Users
 */

declare(strict_types=1);

namespace Apollo\Users;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// REST API namespace
if ( ! defined( 'APOLLO_USERS_REST_NAMESPACE' ) ) {
	define( 'APOLLO_USERS_REST_NAMESPACE', 'apollo/v1' );
}

// Database tables (without prefix)
if ( ! defined( 'APOLLO_USERS_TABLE_MATCHMAKING' ) ) {
	define( 'APOLLO_USERS_TABLE_MATCHMAKING', 'apollo_matchmaking' );
}
if ( ! defined( 'APOLLO_USERS_TABLE_FIELDS' ) ) {
	define( 'APOLLO_USERS_TABLE_FIELDS', 'apollo_user_fields' );
}
if ( ! defined( 'APOLLO_USERS_TABLE_PROFILE_VIEWS' ) ) {
	define( 'APOLLO_USERS_TABLE_PROFILE_VIEWS', 'apollo_profile_views' );
}
if ( ! defined( 'APOLLO_USERS_TABLE_RATINGS' ) ) {
	define( 'APOLLO_USERS_TABLE_RATINGS', 'apollo_user_ratings' );
}

// Profile URL pattern
if ( ! defined( 'APOLLO_USERS_PROFILE_SLUG' ) ) {
	define( 'APOLLO_USERS_PROFILE_SLUG', 'id' );
}

// Membership types
if ( ! defined( 'APOLLO_USERS_MEMBERSHIP_TYPES' ) ) {
	define( 'APOLLO_USERS_MEMBERSHIP_TYPES', [
		'nao-verificado' => 'Não Verificado',
		'apollo'         => 'Apollo',
		'prod'           => 'Produtor',
		'dj'             => 'DJ',
		'host'           => 'Host',
		'govern'         => 'Governança',
		'business-pers'  => 'Business Personal',
	]);
}

// Privacy levels
if ( ! defined( 'APOLLO_USERS_PRIVACY_LEVELS' ) ) {
	define( 'APOLLO_USERS_PRIVACY_LEVELS', [
		'public'  => 'Público',
		'members' => 'Apenas Membros',
		'private' => 'Privado',
	]);
}
