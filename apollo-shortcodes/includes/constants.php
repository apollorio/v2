<?php
/**
 * Plugin Constants
 *
 * @package Apollo\Shortcode
 */

declare(strict_types=1);

namespace Apollo\Shortcode;

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// REST API namespace
define( 'APOLLO_SHORTCODE_REST_NAMESPACE', 'apollo/v1' );
