<?php
/**
 * Fired during plugin deactivation.
 *
 * @package Apollo_Core
 * @since 6.0.0
 */

class Plugin_Name_Deactivator {

	public static function deactivate() {
		// Flush rewrite rules
		flush_rewrite_rules();
	}
}
