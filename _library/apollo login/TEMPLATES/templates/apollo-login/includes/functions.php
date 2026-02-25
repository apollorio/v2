<?php
/**
 * Helper Functions
 *
 * @package Apollo\Login
 */

declare(strict_types=1);

namespace Apollo\Login;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get plugin instance
 */
function apollo_login(): Plugin {
	return Plugin::get_instance();
}

/**
 * Get a virtual page URL
 */
function apollo_login_url( string $page = '' ): string {
	$slug = match ( $page ) {
		'register' => APOLLO_LOGIN_PAGE_REGISTRE,
		'logout'   => APOLLO_LOGIN_PAGE_SAIR,
		'reset'    => APOLLO_LOGIN_PAGE_RESET,
		'verify'   => APOLLO_LOGIN_PAGE_VERIFY,
		default    => APOLLO_LOGIN_PAGE_ACESSO,
	};

	return home_url( '/' . $slug . '/' );
}

/**
 * Check if current user has verified email
 */
function apollo_login_is_email_verified( int $user_id = 0 ): bool {
	if ( ! $user_id ) {
		$user_id = get_current_user_id();
	}
	return (bool) get_user_meta( $user_id, APOLLO_META_EMAIL_VERIFIED, true );
}

/**
 * Get sound taxonomy terms from apollo-core GLOBAL BRIDGE
 *
 * @return array<int, \WP_Term>
 */
function apollo_login_get_sounds(): array {
	if ( ! taxonomy_exists( 'sound' ) ) {
		return [];
	}

	$terms = get_terms([
		'taxonomy'   => 'sound',
		'hide_empty' => false,
		'orderby'    => 'name',
		'order'      => 'ASC',
	]);

	return is_wp_error( $terms ) ? [] : $terms;
}

/**
 * Validate Brazilian CPF (Mod 11 algorithm)
 */
function apollo_login_validate_cpf( string $cpf ): bool {
	$cpf = preg_replace( '/\D/', '', $cpf );

	if ( strlen( $cpf ) !== 11 ) {
		return false;
	}
	if ( preg_match( '/^(\d)\1{10}$/', $cpf ) ) {
		return false;
	}

	$sum = 0;
	for ( $i = 0; $i < 9; $i++ ) {
		$sum += (int) $cpf[ $i ] * ( 10 - $i );
	}
	$d1 = ( $sum % 11 < 2 ) ? 0 : 11 - ( $sum % 11 );
	if ( (int) $cpf[9] !== $d1 ) {
		return false;
	}

	$sum = 0;
	for ( $i = 0; $i < 10; $i++ ) {
		$sum += (int) $cpf[ $i ] * ( 11 - $i );
	}
	$d2 = ( $sum % 11 < 2 ) ? 0 : 11 - ( $sum % 11 );

	return (int) $cpf[10] === $d2;
}

/**
 * Sanitize Instagram username
 */
function apollo_login_sanitize_instagram( string $username ): string {
	$username = strtolower( trim( $username ) );
	$username = ltrim( $username, '@' );
	return preg_replace( '/[^a-z0-9._]/', '', $username );
}

/**
 * Generate a secure random token
 */
function apollo_login_generate_token( int $length = 64 ): string {
	return bin2hex( random_bytes( $length / 2 ) );
}
