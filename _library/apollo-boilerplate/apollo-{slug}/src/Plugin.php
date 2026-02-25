<?php
/**
 * Main Plugin Class (Singleton)
 *
 * @package Apollo\{Namespace}
 */

declare(strict_types=1);

namespace Apollo\{Namespace};

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main Plugin class
 */
final class Plugin {

	/**
	 * Plugin instance
	 *
	 * @var Plugin|null
	 */
	private static ?Plugin $instance = null;

	/**
	 * Get plugin instance (Singleton)
	 *
	 * @return Plugin
	 */
	public static function get_instance(): Plugin {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Private constructor
	 */
	private function __construct() {
		// Singleton - use get_instance()
	}

	/**
	 * Initialize plugin
	 *
	 * @return void
	 */
	public function init(): void {
		// Register hooks
		add_action( 'init', [ $this, 'register_assets' ] );
		add_action( 'rest_api_init', [ $this, 'register_rest_routes' ] );

		// Initialize components
		$this->init_components();
	}

	/**
	 * Initialize plugin components
	 *
	 * @return void
	 */
	private function init_components(): void {
		// Initialize your components here
		// Example:
		// new Components\ExampleHandler();
	}

	/**
	 * Register plugin assets
	 *
	 * @return void
	 */
	public function register_assets(): void {
		// Register styles
		wp_register_style(
			'apollo-{slug}',
			APOLLO_{CONST}_URL . 'assets/css/{slug}.css',
			[],
			APOLLO_{CONST}_VERSION
		);

		// Register scripts
		wp_register_script(
			'apollo-{slug}',
			APOLLO_{CONST}_URL . 'assets/js/{slug}.js',
			[ 'jquery' ],
			APOLLO_{CONST}_VERSION,
			true
		);
	}

	/**
	 * Register REST API routes
	 *
	 * @return void
	 */
	public function register_rest_routes(): void {
		// Register your REST routes here
		// Example:
		// $controller = new API\ExampleController();
		// $controller->register_routes();
	}
}
