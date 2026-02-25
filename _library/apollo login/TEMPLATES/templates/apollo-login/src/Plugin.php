<?php
/**
 * Main Plugin Class (Singleton)
 *
 * @package Apollo\Login
 */

declare(strict_types=1);

namespace Apollo\Login;

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
		// Register assets
		add_action( 'init', [ $this, 'register_assets' ] );
		add_action( 'init', [ $this, 'register_rewrites' ] );
		add_action( 'rest_api_init', [ $this, 'register_rest_routes' ] );
		add_action( 'template_redirect', [ $this, 'handle_virtual_pages' ] );

		// Initialize components
		$this->init_components();
	}

	private function init_components(): void {
		// Security: WP Hide Ghost (URL protection)
		new Security\WPHideGhost();

		// Security: Rate Limiter
		new Security\RateLimiter();

		// Auth handlers (AJAX)
		new Auth\Login();
		new Auth\Register();
		new Auth\PasswordReset();
		new Auth\EmailVerification();

		// Quiz handler (AJAX)
		new Quiz\QuizHandler();

		// Shortcodes
		new Shortcodes\ShortcodeHandler();

		// Blank Canvas Templates
		new Templates\BlankCanvas();
	}

	public function register_assets(): void {
		wp_register_style(
			'apollo-login',
			APOLLO_LOGIN_URL . 'assets/css/login.css',
			[],
			APOLLO_LOGIN_VERSION
		);

		wp_register_script(
			'apollo-login',
			APOLLO_LOGIN_URL . 'assets/js/login.js',
			[],
			APOLLO_LOGIN_VERSION,
			true
		);
	}

	public function register_rewrites(): void {
		add_rewrite_rule( '^' . APOLLO_LOGIN_PAGE_ACESSO . '/?$', 'index.php?apollo_page=acesso', 'top' );
		add_rewrite_rule( '^' . APOLLO_LOGIN_PAGE_REGISTRE . '/?$', 'index.php?apollo_page=registre', 'top' );
		add_rewrite_rule( '^' . APOLLO_LOGIN_PAGE_SAIR . '/?$', 'index.php?apollo_page=sair', 'top' );
		add_rewrite_rule( '^' . APOLLO_LOGIN_PAGE_RESET . '/?$', 'index.php?apollo_page=reset', 'top' );
		add_rewrite_rule( '^' . APOLLO_LOGIN_PAGE_VERIFY . '/?$', 'index.php?apollo_page=verificar-email', 'top' );

		add_rewrite_tag( '%apollo_page%', '([^&]+)' );
	}

	public function handle_virtual_pages(): void {
		$page = get_query_var( 'apollo_page' );

		if ( ! $page ) {
			return;
		}

		// Handle logout
		if ( 'sair' === $page ) {
			wp_logout();
			wp_safe_redirect( home_url( '/' ) );
			exit;
		}

		// Redirect logged-in users away from login/register
		if ( is_user_logged_in() && in_array( $page, [ 'acesso', 'registre' ], true ) ) {
			wp_safe_redirect( home_url( '/mural/' ) );
			exit;
		}

		$template_map = [
			'acesso'          => 'login.php',
			'registre'        => 'register.php',
			'reset'           => 'password-reset.php',
			'verificar-email' => 'verify-email.php',
		];

		if ( isset( $template_map[ $page ] ) ) {
			$template = APOLLO_LOGIN_DIR . 'templates/' . $template_map[ $page ];
			if ( file_exists( $template ) ) {
				// Set 200 status
				status_header( 200 );
				include $template;
				exit;
			}
		}
	}

	public function register_rest_routes(): void {
		$auth_controller     = new API\AuthController();
		$quiz_controller     = new API\QuizController();
		$security_controller = new API\SecurityController();

		$auth_controller->register_routes();
		$quiz_controller->register_routes();
		$security_controller->register_routes();
	}
}
