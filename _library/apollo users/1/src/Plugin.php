<?php
/**
 * Main Plugin Class (Singleton)
 *
 * @package Apollo\Users
 */

declare(strict_types=1);

namespace Apollo\Users;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Plugin {

	private static ?Plugin $instance = null;

	public static function get_instance(): Plugin {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {}

	public function init(): void {
		add_action( 'init', [ $this, 'register_assets' ] );
		add_action( 'init', [ $this, 'register_shortcodes' ] );
		add_action( 'rest_api_init', [ $this, 'register_rest_routes' ] );

		// Author protection
		add_action( 'template_redirect', [ $this, 'block_author_enumeration' ] );
		add_filter( 'author_link', [ $this, 'filter_author_link' ], 10, 2 );

		$this->init_components();
	}

	private function init_components(): void {
		new Components\ProfileHandler();
		new Components\UserFields();
		new Components\AuthorProtection();
		new Components\RatingHandler();
		new Components\DepoimentoHandler();
		new Components\AccountHandler();
	}

	public function register_assets(): void {
		$url = APOLLO_USERS_URL;
		$ver = APOLLO_USERS_VERSION;

		wp_register_style( 'apollo-users-profile', $url . 'assets/css/profile.css', [], $ver );
		wp_register_style( 'apollo-users-radar', $url . 'assets/css/radar.css', [], $ver );
		wp_register_style( 'apollo-users-edit-profile', $url . 'assets/css/edit-profile.css', [], $ver );
		wp_register_style( 'apollo-users-account', $url . 'assets/css/account.css', [], $ver );

		wp_register_script( 'apollo-users-profile', $url . 'assets/js/profile.js', [ 'jquery' ], $ver, true );
		wp_register_script( 'apollo-users-account', $url . 'assets/js/account.js', [ 'jquery' ], $ver, true );
	}

	public function register_shortcodes(): void {
		add_shortcode( 'apollo_profile', [ $this, 'shortcode_profile' ] );
		add_shortcode( 'apollo_profile_edit', [ $this, 'shortcode_profile_edit' ] );
		add_shortcode( 'apollo_radar', [ $this, 'shortcode_radar' ] );
		add_shortcode( 'apollo_user_card', [ $this, 'shortcode_user_card' ] );
		add_shortcode( 'apollo_matchmaking', [ $this, 'shortcode_matchmaking' ] );
		add_shortcode( 'apollo_profile_fields', [ $this, 'shortcode_profile_fields' ] );
	}

	public function register_rest_routes(): void {
		$controller = new API\UsersController();
		$controller->register_routes();

		$profile_controller = new API\ProfileController();
		$profile_controller->register_routes();

		$rating_controller = new API\RatingController();
		$rating_controller->register_routes();
	}

	public function block_author_enumeration(): void {
		if ( isset( $_GET['author'] ) && ! is_admin() ) {
			wp_redirect( home_url(), 301 );
			exit;
		}
		if ( is_author() ) {
			$author = get_queried_object();
			if ( $author && get_user_meta( $author->ID, '_apollo_disable_author_url', true ) ) {
				global $wp_query;
				$wp_query->set_404();
				status_header( 404 );
			}
		}
	}

	public function filter_author_link( string $link, int $author_id ): string {
		return apollo_get_profile_url( $author_id );
	}

	// ═══════════════ SHORTCODES ═══════════════

	public function shortcode_profile( $atts ): string {
		$atts = shortcode_atts( [ 'user_id' => 0 ], $atts );
		$user_id = (int) $atts['user_id'] ?: get_current_user_id();
		if ( ! $user_id ) return '';
		ob_start();
		include APOLLO_USERS_DIR . 'templates/parts/profile-display.php';
		return ob_get_clean();
	}

	public function shortcode_profile_edit(): string {
		if ( ! is_user_logged_in() ) {
			return '<p>Você precisa estar logado para editar seu perfil.</p>';
		}
		ob_start();
		include APOLLO_USERS_DIR . 'templates/parts/profile-edit-form.php';
		return ob_get_clean();
	}

	public function shortcode_radar( $atts ): string {
		$atts = shortcode_atts( [
			'limit'   => 24,
			'role'    => '',
			'orderby' => 'registered',
			'layout'  => 'grid',
		], $atts );
		ob_start();
		include APOLLO_USERS_DIR . 'templates/parts/user-radar.php';
		return ob_get_clean();
	}

	public function shortcode_user_card( $atts ): string {
		$atts = shortcode_atts( [ 'user_id' => 0 ], $atts );
		$user_id = (int) $atts['user_id'];
		if ( ! $user_id ) return '';
		ob_start();
		include APOLLO_USERS_DIR . 'templates/parts/user-card.php';
		return ob_get_clean();
	}

	public function shortcode_matchmaking(): string {
		return '<div class="apollo-matchmaking-disabled"><p>' . esc_html__( 'Sistema de matchmaking desabilitado. Todos os usuários estão conectados.', 'apollo-users' ) . '</p></div>';
	}

	public function shortcode_profile_fields( $atts ): string {
		$atts = shortcode_atts( [ 'user_id' => 0, 'edit' => false ], $atts );
		$user_id = (int) $atts['user_id'] ?: get_current_user_id();
		ob_start();
		include APOLLO_USERS_DIR . 'templates/parts/profile-fields.php';
		return ob_get_clean();
	}
}
