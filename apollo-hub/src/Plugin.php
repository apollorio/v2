<?php

/**
 * Plugin — Singleton orquestrador do Apollo Hub
 *
 * @package Apollo\Hub
 */

declare(strict_types=1);

namespace Apollo\Hub;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Plugin {


	private static ?Plugin $instance = null;

	public function __construct() {
		if ( null !== self::$instance ) {
			return;
		}
		self::$instance = $this;

		new Registry();
		new TemplateLoader();
		new Shortcodes();
		new Integrations();

		// Wire block migration hooks
		new Migration();

		if ( is_admin() ) {
			new Admin\HubAdmin();
		}

		add_action( 'wp_enqueue_scripts', array( $this, 'register_assets' ) );
		add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );
		add_action( 'init', array( $this, 'register_edit_hub_page' ), 20 );
	}

	/**
	 * Registra rewrite virtual para /editar-hub
	 */
	public function register_edit_hub_page(): void {
		add_rewrite_rule(
			'^' . APOLLO_HUB_EDIT_SLUG . '/?$',
			'index.php?apollo_hub_edit=1',
			'top'
		);
		add_filter( 'query_vars', array( $this, 'add_query_vars' ) );
		add_action( 'template_redirect', array( $this, 'handle_edit_hub_page' ) );
	}

	/**
	 * Adiciona query var
	 *
	 * @param array $vars Query vars.
	 * @return array
	 */
	public function add_query_vars( array $vars ): array {
		$vars[] = 'apollo_hub_edit';
		return $vars;
	}

	/**
	 * Renderiza página do editor do hub
	 */
	public function handle_edit_hub_page(): void {
		if ( ! get_query_var( 'apollo_hub_edit' ) ) {
			return;
		}

		if ( ! is_user_logged_in() ) {
			wp_redirect( wp_login_url( home_url( '/' . APOLLO_HUB_EDIT_SLUG ) ) );
			exit;
		}

		$template = APOLLO_HUB_DIR . 'templates/edit-hub.php';
		if ( file_exists( $template ) ) {
			include $template;
			exit;
		}
	}

	/**
	 * Registra assets CSS/JS
	 */
	public function register_assets(): void {
		// CSS principal do Hub
		wp_register_style(
			'apollo-hub',
			APOLLO_HUB_URL . 'assets/css/hub.css',
			array(),
			APOLLO_HUB_VERSION
		);

		// JS do Hub público
		wp_register_script(
			'apollo-hub',
			APOLLO_HUB_URL . 'assets/js/hub.js',
			array(),
			APOLLO_HUB_VERSION,
			true
		);

		// JS do Hub Builder (editor)
		wp_register_script(
			'apollo-hub-builder',
			APOLLO_HUB_URL . 'assets/js/hub-builder.js',
			array( 'apollo-hub' ),
			APOLLO_HUB_VERSION,
			true
		);

		wp_localize_script(
			'apollo-hub',
			'apolloHub',
			array(
				'rest_url'     => esc_url_raw( rest_url( APOLLO_HUB_REST_NAMESPACE ) ),
				'nonce'        => wp_create_nonce( 'wp_rest' ),
				'plugin_url'   => APOLLO_HUB_URL,
				'edit_url'     => home_url( '/' . APOLLO_HUB_EDIT_SLUG ),
				'logged_in'    => is_user_logged_in(),
				'current_user' => is_user_logged_in() ? wp_get_current_user()->user_login : '',
				'themes'       => APOLLO_HUB_THEMES,
				'social_icons' => APOLLO_HUB_SOCIAL_ICONS,
				'block_types'  => APOLLO_HUB_BLOCK_TYPES,
				'i18n'         => array(
					'copy_link'   => __( 'Copiar link', 'apollo-hub' ),
					'copied'      => __( 'Link copiado!', 'apollo-hub' ),
					'save'        => __( 'Salvar', 'apollo-hub' ),
					'saving'      => __( 'Salvando...', 'apollo-hub' ),
					'saved'       => __( 'Salvo!', 'apollo-hub' ),
					'add_link'    => __( 'Adicionar link', 'apollo-hub' ),
					'delete_link' => __( 'Remover link', 'apollo-hub' ),
					'share'       => __( 'Compartilhar', 'apollo-hub' ),
					'edit_hub'    => __( 'Editar Hub', 'apollo-hub' ),
					'no_links'    => __( 'Nenhum link adicionado ainda.', 'apollo-hub' ),
					'max_links'   => sprintf( __( 'Máximo de %d links atingido.', 'apollo-hub' ), APOLLO_HUB_LINKS_MAX ),
					'bio_max'     => sprintf( __( 'Bio: máximo %d caracteres.', 'apollo-hub' ), APOLLO_HUB_BIO_MAX_LEN ),
					'error_save'  => __( 'Erro ao salvar. Tente novamente.', 'apollo-hub' ),
				),
			)
		);
	}

	/**
	 * Registra rotas REST
	 */
	public function register_rest_routes(): void {
		$controller = new API\HubController();
		$controller->register_routes();
	}

	/**
	 * Retorna instância singleton.
	 */
	public static function instance(): ?Plugin {
		return self::$instance;
	}
}
