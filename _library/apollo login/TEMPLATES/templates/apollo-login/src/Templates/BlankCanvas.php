<?php
/**
 * Blank Canvas Templates
 *
 * Login/Register pages load ONLY Apollo CSS/JS - zero theme conflicts.
 * Per apollo-registry.json: removed wp_head/wp_footer, direct assets,
 * custom hooks: apollo_login_head, apollo_register_head, etc.
 *
 * @package Apollo\Login
 */

declare(strict_types=1);

namespace Apollo\Login\Templates;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BlankCanvas {

	public function __construct() {
		add_action( 'apollo_login_head', [ $this, 'output_head_assets' ] );
		add_action( 'apollo_register_head', [ $this, 'output_head_assets' ] );
		add_action( 'apollo_login_footer', [ $this, 'output_footer_assets' ] );
		add_action( 'apollo_register_footer', [ $this, 'output_footer_assets' ] );
	}

	/**
	 * Get the JS config for localization
	 */
	public static function get_js_config(): array {
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

	public function output_head_assets(): void {
		// Apollo CDN
		echo '<script src="https://cdn.apollo.rio.br/v1.0.0/core.js" fetchpriority="high"></script>' . "\n";

		// RemixIcon
		echo '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css">' . "\n";

		// Plugin CSS
		echo '<link rel="stylesheet" href="' . esc_url( APOLLO_LOGIN_URL . 'assets/css/login.css' ) . '?v=' . APOLLO_LOGIN_VERSION . '">' . "\n";
	}

	public function output_footer_assets(): void {
		// jQuery CDN (per registry)
		echo '<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>' . "\n";

		// JS Config
		echo '<script>window.apolloAuthConfig = ' . wp_json_encode( self::get_js_config() ) . ';</script>' . "\n";

		// Plugin JS
		echo '<script src="' . esc_url( APOLLO_LOGIN_URL . 'assets/js/login.js' ) . '?v=' . APOLLO_LOGIN_VERSION . '" defer></script>' . "\n";
	}
}
