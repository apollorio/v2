<?php
/**
 * Main Plugin Class
 *
 * @package Apollo\Login
 */

declare(strict_types=1);

namespace Apollo\Login\Core;

use Apollo\Login\Auth;
use Apollo\Login\Quiz;
use Apollo\Login\Security;
use Apollo\Login\API;

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main Plugin class (Singleton)
 */
final class Plugin {

	/**
	 * Plugin instance
	 *
	 * @var Plugin|null
	 */
	private static ?Plugin $instance = null;

	/**
	 * Get plugin instance
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
	 * Private constructor (Singleton pattern)
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
		add_action( 'init', array( $this, 'register_virtual_pages' ), 1 );
		// add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );
		// add_action( 'init', array( $this, 'register_shortcodes' ) );
		add_filter( 'template_include', array( $this, 'load_templates' ), 99 );

		// Flush rewrite rules if needed
		// add_action( 'init', array( $this, 'maybe_flush_rewrites' ), 99 );

		// Initialize components
		// $this->init_components();
	}

	/**
	 * Flush rewrite rules if needed
	 *
	 * @return void
	 */
	public function maybe_flush_rewrites(): void {
		$current_version = get_option( 'apollo_login_flush_rewrites' );
		if ( $current_version !== APOLLO_LOGIN_VERSION ) {
			flush_rewrite_rules( true );
			update_option( 'apollo_login_flush_rewrites', APOLLO_LOGIN_VERSION );
		}
	}

	/**
	 * Initialize plugin components
	 *
	 * @return void
	 */
	private function init_components(): void {
		// Auth handlers
		new Auth\LoginHandler();
		new Auth\RegisterHandler();
		new Auth\PasswordReset();
		new Auth\EmailVerification();

		// Quiz system
		new Quiz\QuizManager();
		new Quiz\SimonGame();

		// Security
		new Security\URLRewriter();
		new Security\RateLimiter();
		new Security\Lockout();
	}

	/**
	 * Register virtual pages
	 *
	 * @return void
	 */
	public function register_virtual_pages(): void {
		// /acesso - Login page
		add_rewrite_rule(
			'^acesso/?$',
			'index.php?apollo_login_page=login',
			'top'
		);

		// /registre - Register page
		add_rewrite_rule(
			'^registre/?$',
			'index.php?apollo_login_page=register',
			'top'
		);

		// /reset - Password reset
		add_rewrite_rule(
			'^reset/?$',
			'index.php?apollo_login_page=reset',
			'top'
		);

		// /verificar-email - Email verification
		add_rewrite_rule(
			'^verificar-email/?$',
			'index.php?apollo_login_page=verify-email',
			'top'
		);

		// /sair - Logout redirect
		add_rewrite_rule(
			'^sair/?$',
			'index.php?apollo_login_page=logout',
			'top'
		);

		// Add query vars
		add_filter( 'query_vars', function( $vars ) {
			$vars[] = 'apollo_login_page';
			return $vars;
		});
	}

	/**
	 * Register REST API routes
	 *
	 * @return void
	 */
	public function register_rest_routes(): void {
		$controllers = array(
			new API\AuthController(),
			new API\QuizController(),
			new API\SecurityController(),
		);

		foreach ( $controllers as $controller ) {
			$controller->register_routes();
		}
	}

	/**
	 * Register shortcodes
	 *
	 * @return void
	 */
	public function register_shortcodes(): void {
		add_shortcode( 'apollo_login', array( $this, 'shortcode_login' ) );
		add_shortcode( 'apollo_register', array( $this, 'shortcode_register' ) );
		add_shortcode( 'apollo_quiz', array( $this, 'shortcode_quiz' ) );
		add_shortcode( 'apollo_simon', array( $this, 'shortcode_simon' ) );
		add_shortcode( 'apollo_password_reset', array( $this, 'shortcode_password_reset' ) );
		add_shortcode( 'apollo_verify_email', array( $this, 'shortcode_verify_email' ) );
	}

	/**
	 * Load templates for virtual pages
	 *
	 * @param string $template Template path.
	 * @return string
	 */
	public function load_templates( string $template ): string {
		$page = get_query_var( 'apollo_login_page', '' );

		// Debug: Check if query var is being captured
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( 'Apollo Login - Query var apollo_login_page: ' . $page );
		}

		if ( empty( $page ) ) {
			return $template;
		}

		// Handle logout redirect
		if ( 'logout' === $page ) {
			wp_logout();
			wp_redirect( home_url() );
			exit;
		}

		// Load template
		$template_file = APOLLO_LOGIN_DIR . 'templates/' . $page . '.php';

		if ( file_exists( $template_file ) ) {
			global $wp_query;
			$wp_query->is_404 = false;
			status_header( 200 );
			return $template_file;
		}

		return $template;
	}

	/**
	 * Shortcode: Login form
	 *
	 * @return string
	 */
	public function shortcode_login(): string {
		ob_start();
		include APOLLO_LOGIN_DIR . 'templates/parts/login-form.php';
		return ob_get_clean();
	}

	/**
	 * Shortcode: Register form
	 *
	 * @return string
	 */
	public function shortcode_register(): string {
		ob_start();
		include APOLLO_LOGIN_DIR . 'templates/parts/register-form.php';
		return ob_get_clean();
	}

	/**
	 * Shortcode: Quiz component
	 *
	 * @return string
	 */
	public function shortcode_quiz(): string {
		ob_start();
		include APOLLO_LOGIN_DIR . 'templates/parts/quiz-overlay.php';
		return ob_get_clean();
	}

	/**
	 * Shortcode: Simon game
	 *
	 * @return string
	 */
	public function shortcode_simon(): string {
		return '<div id="apollo-simon-game" class="apollo-simon-standalone"></div>';
	}

	/**
	 * Shortcode: Password reset form
	 *
	 * @return string
	 */
	public function shortcode_password_reset(): string {
		ob_start();
		include APOLLO_LOGIN_DIR . 'templates/parts/password-reset-form.php';
		return ob_get_clean();
	}

	/**
	 * Shortcode: Email verification
	 *
	 * @return string
	 */
	public function shortcode_verify_email(): string {
		ob_start();
		include APOLLO_LOGIN_DIR . 'templates/parts/email-verification.php';
		return ob_get_clean();
	}
}
