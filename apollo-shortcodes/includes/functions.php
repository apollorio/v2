<?php
/**
 * Helper Functions
 *
 * @package Apollo\Shortcode
 */

declare(strict_types=1);

namespace Apollo\Shortcode;

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get all registered Apollo shortcodes
 *
 * @return array
 */
function get_apollo_shortcodes(): array {
	global $shortcode_tags;

	$apollo_shortcodes = array();
	foreach ( $shortcode_tags as $tag => $callback ) {
		if ( strpos( $tag, 'apollo_' ) === 0 ) {
			$apollo_shortcodes[ $tag ] = $callback;
		}
	}

	return $apollo_shortcodes;
}

/**
 * Check if a shortcode is registered
 *
 * @param string $tag Shortcode tag.
 * @return bool
 */
function shortcode_exists( string $tag ): bool {
	global $shortcode_tags;
	return isset( $shortcode_tags[ $tag ] );
}
