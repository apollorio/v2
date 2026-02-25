<?php
/**
 * The core plugin class.
 *
 * @package Apollo_Core
 * @since 6.0.0
 */

class Plugin_Name {

	protected $loader;
	protected $plugin_name;
	protected $version;

	public function __construct() {
		$this->version     = defined( 'APOLLO_VERSION' ) ? APOLLO_VERSION : '6.0.0';
		$this->plugin_name = 'apollo-core';
		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	private function load_dependencies() {
		require_once APOLLO_CORE_PATH . 'includes/class-plugin-name-loader.php';
		require_once APOLLO_CORE_PATH . 'includes/class-plugin-name-i18n.php';
		require_once APOLLO_CORE_PATH . 'admin/class-apollo-core-admin.php';
		require_once APOLLO_CORE_PATH . 'public/class-apollo-core-public.php';
		$this->loader = new Plugin_Name_Loader();
	}

	private function set_locale() {
		$plugin_i18n = new Plugin_Name_i18n();
		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	private function define_admin_hooks() {
		$plugin_admin = new Apollo_Core_Admin( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
	}

	private function define_public_hooks() {
		$plugin_public = new Apollo_Core_Public( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
	}

	public function run() {
		$this->loader->run();
	}

	public function get_plugin_name() {
		return $this->plugin_name;
	}

	public function get_loader() {
		return $this->loader;
	}

	public function get_version() {
		return $this->version;
	}
}
