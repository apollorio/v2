<?php
/**
 * Shortcode Handler
 *
 * Shortcodes per apollo-registry.json:
 * [apollo_login], [apollo_register], [apollo_quiz],
 * [apollo_simon], [apollo_password_reset], [apollo_verify_email]
 *
 * @package Apollo\Login
 */

declare(strict_types=1);

namespace Apollo\Login\Shortcodes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ShortcodeHandler {

	public function __construct() {
		add_shortcode( 'apollo_login', [ $this, 'render_login' ] );
		add_shortcode( 'apollo_register', [ $this, 'render_register' ] );
		add_shortcode( 'apollo_quiz', [ $this, 'render_quiz' ] );
		add_shortcode( 'apollo_simon', [ $this, 'render_simon' ] );
		add_shortcode( 'apollo_password_reset', [ $this, 'render_password_reset' ] );
		add_shortcode( 'apollo_verify_email', [ $this, 'render_verify_email' ] );
	}

	private function enqueue_assets(): void {
		wp_enqueue_style( 'apollo-login' );
		wp_enqueue_script( 'apollo-login' );

		wp_localize_script( 'apollo-login', 'apolloAuthConfig', $this->get_js_config() );
	}

	private function get_js_config(): array {
		return [
			'ajaxUrl'            => admin_url( 'admin-ajax.php' ),
			'nonce'              => wp_create_nonce( 'apollo_auth_nonce' ),
			'maxFailedAttempts'  => (int) get_option( 'apollo_login_max_attempts', APOLLO_LOGIN_MAX_ATTEMPTS ),
			'lockoutDuration'    => (int) get_option( 'apollo_login_lockout_duration', APOLLO_LOGIN_LOCKOUT_DURATION ),
			'simonLevels'        => 4,
			'reactionTargets'    => 4,
			'redirectAfterLogin' => home_url( get_option( 'apollo_login_redirect_after_login', '/mural/' ) ),
			'strings'            => [
				'loginSuccess'   => __( 'Acesso autorizado. Redirecionando...', 'apollo-login' ),
				'loginFailed'    => __( 'Credenciais incorretas. Tente novamente.', 'apollo-login' ),
				'warningState'   => __( 'Atenção: última tentativa antes do bloqueio.', 'apollo-login' ),
				'lockedOut'      => __( 'Sistema bloqueado por segurança.', 'apollo-login' ),
				'quizComplete'   => __( 'Teste de aptidão concluído com sucesso!', 'apollo-login' ),
				'quizFailed'     => __( 'Resposta incorreta. Reiniciando pergunta...', 'apollo-login' ),
				'patternCorrect' => '♫♫♫',
				'ethicsCorrect'  => __( 'É trabalho, renda, a sonoridade e arte favorita de alguem.', 'apollo-login' ),
			],
		];
	}

	public function render_login( array $atts = [] ): string {
		if ( is_user_logged_in() ) {
			return '<p>' . __( 'Você já está logado.', 'apollo-login' ) . '</p>';
		}

		$this->enqueue_assets();

		ob_start();
		$auth_config      = $this->get_js_config();
		$available_sounds = \Apollo\Login\apollo_login_get_sounds();
		include APOLLO_LOGIN_DIR . 'templates/parts/login-form.php';
		return ob_get_clean();
	}

	public function render_register( array $atts = [] ): string {
		if ( is_user_logged_in() ) {
			return '<p>' . __( 'Você já está logado.', 'apollo-login' ) . '</p>';
		}

		$this->enqueue_assets();

		ob_start();
		$auth_config      = $this->get_js_config();
		$available_sounds = \Apollo\Login\apollo_login_get_sounds();
		include APOLLO_LOGIN_DIR . 'templates/parts/register-form.php';
		return ob_get_clean();
	}

	public function render_quiz( array $atts = [] ): string {
		$this->enqueue_assets();

		ob_start();
		$auth_config = $this->get_js_config();
		include APOLLO_LOGIN_DIR . 'templates/parts/aptitude-quiz.php';
		return ob_get_clean();
	}

	public function render_simon( array $atts = [] ): string {
		$this->enqueue_assets();
		return '<div id="apollo-simon-standalone"></div>';
	}

	public function render_password_reset( array $atts = [] ): string {
		$this->enqueue_assets();

		ob_start();
		include APOLLO_LOGIN_DIR . 'templates/password-reset.php';
		return ob_get_clean();
	}

	public function render_verify_email( array $atts = [] ): string {
		$this->enqueue_assets();

		ob_start();
		include APOLLO_LOGIN_DIR . 'templates/verify-email.php';
		return ob_get_clean();
	}
}
