<?php
/**
 * Define the internationalization functionality.
 *
 * @package Apollo_Core
 * @since 6.0.0
 */

class Plugin_Name_i18n {

	public function load_plugin_textdomain() {
		load_plugin_textdomain(
			'apollo-core',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);
	}
}
