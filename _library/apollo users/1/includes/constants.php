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
define( 'APOLLO_USERS_REST_NAMESPACE', 'apollo/v1' );

// Database tables (without prefix)
define( 'APOLLO_USERS_TABLE_MATCHMAKING', 'apollo_matchmaking' );
define( 'APOLLO_USERS_TABLE_FIELDS', 'apollo_user_fields' );
define( 'APOLLO_USERS_TABLE_PROFILE_VIEWS', 'apollo_profile_views' );
define( 'APOLLO_USERS_TABLE_RATINGS', 'apollo_user_ratings' );

// Profile URL pattern - ALWAYS /id/username
define( 'APOLLO_USERS_PROFILE_SLUG', 'id' );

// Rating categories
define( 'APOLLO_USERS_RATING_CATEGORIES', [
	'sexy'      => [ 'label' => 'Sexy', 'icon' => 'ri-heart-3-fill', 'color' => '#f45f00' ],
	'legal'     => [ 'label' => 'Legal', 'icon' => 'ri-user-smile-line', 'color' => '#22c55e' ],
	'confiavel' => [ 'label' => 'Confiável', 'icon' => 'ri-instance-line', 'color' => '#3b82f6' ],
] );

// Membership types
define( 'APOLLO_USERS_MEMBERSHIP_TYPES', [
	'nao-verificado' => 'Não Verificado',
	'apollo'         => 'Apollo',
	'prod'           => 'Produtor',
	'dj'             => 'DJ',
	'host'           => 'Host',
	'govern'         => 'Governança',
	'business-pers'  => 'Business Personal',
] );

// Privacy levels
define( 'APOLLO_USERS_PRIVACY_LEVELS', [
	'public'  => 'Público',
	'members' => 'Apenas Membros',
	'private' => 'Privado',
] );

// Comment type for depoimentos
define( 'APOLLO_USERS_DEPOIMENTO_TYPE', 'apollo_depoimento' );

// Account page sections
define( 'APOLLO_USERS_ACCOUNT_SECTIONS', [
	'account'         => 'Minha Conta',
	'change-password' => 'Alterar Senha',
	'privacy'         => 'Privacidade',
	'delete-account'  => 'Excluir Conta',
] );
