<?php
/**
 * Fired during plugin activation.
 *
 * @package Apollo_Core
 * @since 6.0.0
 */

class Plugin_Name_Activator {

	public static function activate() {
		// Flush rewrite rules
		flush_rewrite_rules();
	}
}
