<?php
/**
 * Main Plugin Class (Singleton)
 *
 * @package Apollo\Shortcode
 */

declare(strict_types=1);

namespace Apollo\Shortcode;

// Prevent direct access.
if ( ! \defined( 'ABSPATH' ) ) {
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
		// Load text domain
		$this->load_textdomain();

		// Register hooks
		add_action( 'init', array( $this, 'register_assets' ) );
		add_action( 'init', array( $this, 'load_shortcodes' ) );
		add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );
		add_action( 'rest_api_init', array( $this, 'register_search_routes' ) );
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );

		// Enqueue search assets globally (frontend + admin)
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_search_assets' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_search_assets' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_search_enhancer' ) );
	}

	/**
	 * Load plugin textdomain
	 *
	 * @return void
	 */
	private function load_textdomain(): void {
		load_plugin_textdomain(
			'apollo-shortcodes',
			false,
			\dirname( APOLLO_SHORTCODE_BASENAME ) . '/languages'
		);
	}

	/**
	 * Register plugin assets
	 *
	 * @return void
	 */
	public function register_assets(): void {
		// Register styles
		wp_register_style(
			'apollo-shortcodes',
			APOLLO_SHORTCODE_URL . 'assets/css/shortcodes.css',
			array(),
			APOLLO_SHORTCODE_VERSION
		);

		// Register scripts
		wp_register_script(
			'apollo-shortcodes',
			APOLLO_SHORTCODE_URL . 'assets/js/shortcodes.js',
			array( 'jquery' ),
			APOLLO_SHORTCODE_VERSION,
			true
		);
	}

	/**
	 * Load shortcode classes
	 *
	 * @return void
	 */
	public function load_shortcodes(): void {
		// Load shortcode classes from includes directory
		$shortcode_files = array(
			'class-apollo-native-newsletter.php',
			'class-apollo-shortcode-registry.php',
			'class-interesse-ranking.php',
			'class-user-dashboard-interesse.php',
			'class-user-stats-widget.php',
			'class-cena-rio-submissions.php',
		);

		foreach ( $shortcode_files as $file ) {
			$file_path = APOLLO_SHORTCODE_DIR . 'includes/' . $file;
			if ( \file_exists( $file_path ) ) {
				require_once $file_path;
			}
		}
	}

	/**
	 * Register REST API routes
	 *
	 * @return void
	 */
	public function register_rest_routes(): void {
		// GET /shortcodes - List all shortcodes
		register_rest_route(
			'apollo/v1',
			'/shortcodes',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'rest_list_shortcodes' ),
				'permission_callback' => '__return_true',
			)
		);

		// GET /shortcodes/{tag} - Get shortcode info
		register_rest_route(
			'apollo/v1',
			'/shortcodes/(?P<tag>[a-z_]+)',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'rest_get_shortcode' ),
				'permission_callback' => '__return_true',
			)
		);

		// POST /shortcodes/render - Render shortcode
		register_rest_route(
			'apollo/v1',
			'/shortcodes/render',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'rest_render_shortcode' ),
				'permission_callback' => array( $this, 'check_edit_permission' ),
			)
		);
	}

	/**
	 * REST: List all registered shortcodes
	 *
	 * @return \WP_REST_Response
	 */
	public function rest_list_shortcodes(): \WP_REST_Response {
		global $shortcode_tags;

		$apollo_shortcodes = array();
		foreach ( $shortcode_tags as $tag => $callback ) {
			if ( \strpos( $tag, 'apollo_' ) === 0 ) {
				$apollo_shortcodes[] = array(
					'tag'      => $tag,
					'callback' => \is_array( $callback ) ? \get_class( $callback[0] ) . '::' . $callback[1] : $callback,
				);
			}
		}

		return new \WP_REST_Response(
			array(
				'success'    => true,
				'shortcodes' => $apollo_shortcodes,
				'total'      => \count( $apollo_shortcodes ),
			),
			200
		);
	}

	/**
	 * REST: Get shortcode info
	 *
	 * @param \WP_REST_Request $request Request object.
	 * @return \WP_REST_Response
	 */
	public function rest_get_shortcode( \WP_REST_Request $request ): \WP_REST_Response {
		global $shortcode_tags;

		$tag = $request->get_param( 'tag' );

		if ( ! isset( $shortcode_tags[ $tag ] ) ) {
			return new \WP_REST_Response(
				array(
					'success' => false,
					'message' => 'Shortcode not found',
				),
				404
			);
		}

		$callback = $shortcode_tags[ $tag ];

		return new \WP_REST_Response(
			array(
				'success'  => true,
				'tag'      => $tag,
				'callback' => \is_array( $callback ) ? \get_class( $callback[0] ) . '::' . $callback[1] : $callback,
			),
			200
		);
	}

	/**
	 * REST: Render shortcode
	 *
	 * @param \WP_REST_Request $request Request object.
	 * @return \WP_REST_Response
	 */
	public function rest_render_shortcode( \WP_REST_Request $request ): \WP_REST_Response {
		$shortcode = $request->get_param( 'shortcode' );

		if ( empty( $shortcode ) ) {
			return new \WP_REST_Response(
				array(
					'success' => false,
					'message' => 'Shortcode is required',
				),
				400
			);
		}

		$output = do_shortcode( $shortcode );

		return new \WP_REST_Response(
			array(
				'success' => true,
				'html'    => $output,
			),
			200
		);
	}

	/**
	 * Enqueue admin-only search enhancer (auto-filter for list tables with 10+ rows).
	 *
	 * @return void
	 */
	public function enqueue_admin_search_enhancer(): void {
		wp_enqueue_script(
			'apollo-search-admin',
			APOLLO_SHORTCODE_URL . 'assets/js/apollo-search-admin.js',
			array( 'apollo-search' ),
			APOLLO_SHORTCODE_VERSION,
			true
		);
	}

	/**
	 * Register Apollo Search REST API routes
	 *
	 * @return void
	 */
	public function register_search_routes(): void {
		require_once APOLLO_SHORTCODE_DIR . 'includes/class-apollo-search-controller.php';
		$controller = new \Apollo_Search_Controller();
		$controller->register_routes();
	}

	/**
	 * Enqueue Apollo Search assets globally (frontend + admin).
	 *
	 * @return void
	 */
	public function enqueue_search_assets(): void {
		wp_enqueue_style(
			'apollo-search',
			APOLLO_SHORTCODE_URL . 'assets/css/apollo-search.css',
			array(),
			APOLLO_SHORTCODE_VERSION
		);

		wp_enqueue_script(
			'apollo-search',
			APOLLO_SHORTCODE_URL . 'assets/js/apollo-search.js',
			array(),
			APOLLO_SHORTCODE_VERSION,
			true
		);

		wp_localize_script(
			'apollo-search',
			'apolloSearch',
			array(
				'restUrl' => esc_url_raw( rest_url( 'apollo/v1/' ) ),
				'nonce'   => wp_create_nonce( 'wp_rest' ),
			)
		);
	}

	/**
	 * Check edit permission for REST routes
	 *
	 * @return bool
	 */
	public function check_edit_permission(): bool {
		return current_user_can( 'edit_posts' );
	}

	/**
	 * Add admin menu
	 *
	 * @return void
	 */
	public function add_admin_menu(): void {
		add_menu_page(
			__( 'Apollo Shortcodes', 'apollo-shortcodes' ),
			__( 'Shortcodes', 'apollo-shortcodes' ),
			'manage_options',
			'apollo-shortcodes',
			array( $this, 'admin_page' ),
			'dashicons-shortcode',
			31
		);
	}

	/**
	 * Admin page callback
	 *
	 * @return void
	 */
	public function admin_page(): void {
		?>
<div class="wrap">
	<h1><?php esc_html_e( 'Apollo Shortcodes', 'apollo-shortcodes' ); ?></h1>
	<p><?php esc_html_e( 'ALL frontend shortcodes registry organized here.', 'apollo-shortcodes' ); ?></p>
	<div class="apollo-shortcodes-dashboard">
		<?php $this->render_shortcode_list(); ?>
	</div>
</div>
		<?php
	}

	/**
	 * Render shortcode list in admin
	 *
	 * @return void
	 */
	private function render_shortcode_list(): void {
		global $shortcode_tags;

		$apollo_shortcodes = array();
		foreach ( $shortcode_tags as $tag => $callback ) {
			if ( \strpos( $tag, 'apollo_' ) === 0 ) {
				$apollo_shortcodes[ $tag ] = $callback;
			}
		}

		if ( empty( $apollo_shortcodes ) ) {
			echo '<p>' . esc_html__( 'No Apollo shortcodes registered.', 'apollo-shortcodes' ) . '</p>';
			return;
		}

		echo '<table class="wp-list-table widefat fixed striped">';
		echo '<thead><tr><th>Shortcode</th><th>Callback</th></tr></thead>';
		echo '<tbody>';
		foreach ( $apollo_shortcodes as $tag => $callback ) {
			$callback_name = \is_array( $callback )
				? ( \is_object( $callback[0] ) ? \get_class( $callback[0] ) : $callback[0] ) . '::' . $callback[1]
				: $callback;
			echo '<tr>';
			echo '<td><code>[' . esc_html( $tag ) . ']</code></td>';
			echo '<td><code>' . esc_html( $callback_name ) . '</code></td>';
			echo '</tr>';
		}
		echo '</tbody></table>';
	}
}
