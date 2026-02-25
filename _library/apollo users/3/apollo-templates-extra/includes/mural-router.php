<?php
/**
 * Mural Router
 *
 * When a logged-in user visits the home page, serve page-mural.php
 * instead of page-home.php.
 *
 * Hook this file via:
 *   require_once APOLLO_TEMPLATES_DIR . 'includes/mural-router.php';
 *
 * @package Apollo\Templates
 * @since   1.1.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Intercept the template for the front page when user is logged in.
 *
 * Hooks into `template_include` at priority 99 (after theme/other plugins).
 */
add_filter( 'template_include', function ( string $template ): string {

	// Only on the front page / home.
	if ( ! is_front_page() && ! is_home() ) {
		return $template;
	}

	// Only for logged-in users.
	if ( ! is_user_logged_in() ) {
		return $template;
	}

	$mural_template = APOLLO_TEMPLATES_DIR . 'templates/page-mural.php';

	if ( file_exists( $mural_template ) ) {
		return $mural_template;
	}

	return $template;

}, 99 );

/**
 * Also handle the case where page-home.php is assigned as a WordPress
 * page template via the Template Name header. If someone visits that
 * specific page while logged in, redirect to mural.
 */
add_action( 'template_redirect', function (): void {

	if ( ! is_user_logged_in() ) {
		return;
	}

	// Check if this is a page using the "Apollo Home" template.
	if ( is_page() ) {
		$page_template = get_page_template_slug();
		if ( $page_template && str_contains( $page_template, 'page-home' ) ) {
			// Let template_include handle it — it will load page-mural.php.
			return;
		}
	}
}, 5 );
