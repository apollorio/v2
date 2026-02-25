<?php

/**
 * Main Plugin Class (Singleton)
 *
 * Orchestrates taxonomies, NREP auto-coding, admin columns,
 * template loading and frontend assets.
 *
 * @package Apollo\Journal
 */

declare(strict_types=1);

namespace Apollo\Journal;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main Plugin class.
 */
final class Plugin {


	/** @var Plugin|null */
	private static ?Plugin $instance = null;

	/**
	 * Singleton accessor.
	 *
	 * @return Plugin
	 */
	public static function get_instance(): Plugin {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/** Private constructor — use get_instance(). */
	private function __construct() {}

	/**
	 * Wire all hooks.
	 *
	 * @return void
	 */
	public function init(): void {
		// Taxonomies.
		add_action( 'init', array( $this, 'register_taxonomies' ), 5 );

		// Assets.
		add_action( 'init', array( $this, 'register_assets' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend' ) );

		// NREP auto-coding.
		$nrep = new NREP();
		$nrep->init();

		// Shortcodes.
		$shortcodes = new Shortcodes();
		$shortcodes->init();

		// REST API.
		add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );

		// Admin columns & meta box.
		if ( is_admin() ) {
			$admin = new Admin();
			$admin->init();
		}

		// Template overrides.
		add_filter( 'template_include', array( $this, 'maybe_override_template' ) );

		/**
		 * Fires after Apollo Journal is fully initialised.
		 *
		 * @since 1.0.0
		 */
		do_action( 'apollo/journal/initialized' );
	}

	// ─────────────────────────────────────────────────────────────────────
	// TAXONOMIES
	// ─────────────────────────────────────────────────────────────────────

	/**
	 * Register custom hierarchical taxonomies for editorial content.
	 *
	 * @return void
	 */
	public function register_taxonomies(): void {
		$taxonomies = array(
			'music'   => array(
				'name'     => 'Música',
				'singular' => 'Gênero Musical',
				'slug'     => 'music',
			),
			'culture' => array(
				'name'     => 'Cultura',
				'singular' => 'Cultura',
				'slug'     => 'culture',
			),
			'rio'     => array(
				'name'     => 'Rio',
				'singular' => 'Região',
				'slug'     => 'rio',
			),
			'formato' => array(
				'name'     => 'Formato',
				'singular' => 'Formato',
				'slug'     => 'formato',
			),
		);

		/**
		 * Filter the list of taxonomies registered by Apollo Journal.
		 *
		 * @since 1.0.0
		 * @param array $taxonomies Slug => config array.
		 */
		$taxonomies = apply_filters( 'apollo/journal/taxonomies', $taxonomies );

		foreach ( $taxonomies as $slug => $cfg ) {
			if ( taxonomy_exists( $slug ) ) {
				continue;
			}

			$labels = array(
				'name'              => $cfg['name'],
				'singular_name'     => $cfg['singular'],
				'search_items'      => 'Pesquisar ' . $cfg['name'],
				'all_items'         => 'Todos',
				'parent_item'       => $cfg['singular'] . ' pai',
				'parent_item_colon' => $cfg['singular'] . ' pai:',
				'edit_item'         => 'Editar ' . $cfg['singular'],
				'update_item'       => 'Atualizar ' . $cfg['singular'],
				'add_new_item'      => 'Adicionar ' . $cfg['singular'],
				'new_item_name'     => 'Novo ' . $cfg['singular'],
				'menu_name'         => $cfg['name'],
			);

			register_taxonomy(
				$slug,
				'post',
				array(
					'hierarchical'      => true,
					'labels'            => $labels,
					'show_ui'           => true,
					'show_in_rest'      => true,
					'show_admin_column' => true,
					'rewrite'           => array(
						'slug'         => $cfg['slug'],
						'with_front'   => false,
						'hierarchical' => true,
					),
					'public'            => true,
					'show_in_nav_menus' => true,
					'show_tagcloud'     => false,
				)
			);
		}
	}

	// ─────────────────────────────────────────────────────────────────────
	// ASSETS
	// ─────────────────────────────────────────────────────────────────────

	/**
	 * Register CSS / JS handles.
	 *
	 * @return void
	 */
	public function register_assets(): void {
		wp_register_style(
			'apollo-journal',
			APOLLO_JOURNAL_URL . 'assets/css/journal.css',
			array(),
			APOLLO_JOURNAL_VERSION
		);

		wp_register_script(
			'apollo-journal',
			APOLLO_JOURNAL_URL . 'assets/js/journal.js',
			array( 'jquery' ),
			APOLLO_JOURNAL_VERSION,
			true
		);
	}

	/**
	 * Enqueue on the frontend when needed.
	 *
	 * @return void
	 */
	public function enqueue_frontend(): void {
		if ( is_singular( 'post' ) || is_category() || is_tax( array( 'music', 'culture', 'rio', 'formato' ) ) || is_home() || is_front_page() ) {
			wp_enqueue_style( 'apollo-journal' );
			wp_enqueue_script( 'apollo-journal' );

			wp_localize_script(
				'apollo-journal',
				'apolloJournalConfig',
				array(
					'ajaxUrl' => admin_url( 'admin-ajax.php' ),
					'restUrl' => rest_url( APOLLO_JOURNAL_REST_NAMESPACE . '/' ),
					'nonce'   => wp_create_nonce( 'wp_rest' ),
				)
			);
		}
	}

	// ─────────────────────────────────────────────────────────────────────
	// TEMPLATE OVERRIDE
	// ─────────────────────────────────────────────────────────────────────

	/**
	 * Register REST API routes.
	 *
	 * @return void
	 */
	public function register_rest_routes(): void {
		$controller = new API\PostsController();
		$controller->register_routes();
	}

	/**
	 * Optionally override category / taxonomy archive templates.
	 *
	 * @param string $template Current template path.
	 * @return string
	 */
	public function maybe_override_template( string $template ): string {
		$override = '';

		if ( is_category() ) {
			$override = APOLLO_JOURNAL_DIR . 'templates/archive-journal.php';
		} elseif ( is_tax( array( 'music', 'culture', 'rio', 'formato' ) ) ) {
			$override = APOLLO_JOURNAL_DIR . 'templates/archive-journal.php';
		}

		if ( $override && file_exists( $override ) ) {
			return $override;
		}

		return $template;
	}
}
