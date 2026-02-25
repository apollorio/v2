<?php
/**
 * Apollo Core Public
 *
 * Public-facing functionality
 *
 * @package Apollo_Core
 * @since 6.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Apollo_Core_Public {

	private string $plugin_name;
	private string $version;

	public function __construct( string $plugin_name, string $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	public function enqueue_styles(): void {
		wp_enqueue_style(
			$this->plugin_name,
			plugin_dir_url( __FILE__ ) . 'css/apollo-core-public.css',
			array(),
			$this->version,
			'all'
		);
	}

	public function enqueue_scripts(): void {
		wp_enqueue_script(
			$this->plugin_name,
			plugin_dir_url( __FILE__ ) . 'js/apollo-core-public.js',
			array( 'jquery' ),
			$this->version,
			true
		);

		wp_localize_script(
			$this->plugin_name,
			'apolloCore',
			array(
				'ajaxUrl'  => admin_url( 'admin-ajax.php' ),
				'restUrl'  => rest_url( 'apollo/v1/' ),
				'nonce'    => wp_create_nonce( 'wp_rest' ),
				'version'  => $this->version,
				'isLogged' => is_user_logged_in(),
			)
		);
	}
}
