<?php
/**
 * Lockout Handler
 *
 * @package Apollo\Login
 */

declare(strict_types=1);

namespace Apollo\Login\Security;

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Lockout class
 */
class Lockout {

	/**
	 * Constructor
	 */
	public function __construct() {
		// Lockout logic handled in LoginHandler
	}
}
