<?php
/**
 * Registration Handler
 *
 * 7-step multi-step registration flow per apollo-registry.json:
 * 1. Social name, 2. Instagram, 3. Email, 4. Password,
 * 5. Confirm password, 6. Sound preferences, 7. Quiz
 *
 * Inspired by UsersWP form handling patterns (nonce, validation, wp_insert_user).
 *
 * @package Apollo\Login
 */

declare(strict_types=1);

namespace Apollo\Login\Auth;

use function Apollo\Login\apollo_login_validate_cpf;
use function Apollo\Login\apollo_login_sanitize_instagram;
use function Apollo\Login\apollo_login_generate_token;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Register {

	public function __construct() {
		add_action( 'wp_ajax_nopriv_apollo_register', [ $this, 'handle_register' ] );
		add_action( 'wp_ajax_apollo_register', [ $this, 'handle_register' ] );
		add_action( 'wp_ajax_nopriv_apollo_check_username', [ $this, 'check_username' ] );
		add_action( 'wp_ajax_nopriv_apollo_check_email', [ $this, 'check_email' ] );
	}

	public function handle_register(): void {
		// Verify nonce
		if ( ! check_ajax_referer( 'apollo_auth_nonce', 'nonce', false ) ) {
			wp_send_json_error([
				'message' => __( 'Verificação de segurança falhou.', 'apollo-login' ),
				'code'    => 'nonce_failed',
			], 403 );
		}

		// Check if registration is open
		if ( ! get_option( 'users_can_register' ) ) {
			wp_send_json_error([
				'message' => __( 'Registro de novos usuários está desabilitado.', 'apollo-login' ),
				'code'    => 'registration_disabled',
			], 403 );
		}

		// Honeypot check (anti-spam à la UsersWP)
		if ( ! empty( $_POST['apollo_register_hp'] ?? '' ) ) {
			wp_die( 'No spam please!' );
		}

		// Validate all fields
		$data   = $this->sanitize_input( $_POST );
		$errors = $this->validate( $data );

		if ( ! empty( $errors ) ) {
			wp_send_json_error([
				'message' => implode( ' ', $errors ),
				'errors'  => $errors,
				'code'    => 'validation_failed',
			], 400 );
		}

		// Verify quiz was passed
		$quiz_passed = ! empty( $_POST['quiz_passed'] );
		$require_quiz = get_option( 'apollo_login_require_quiz', true );

		if ( $require_quiz && ! $quiz_passed ) {
			wp_send_json_error([
				'message' => __( 'Teste de aptidão é obrigatório.', 'apollo-login' ),
				'code'    => 'quiz_required',
			], 400 );
		}

		/**
		 * Action before user creation
		 *
		 * @param array $data Sanitized registration data
		 */
		do_action( 'apollo_login_before_register', $data );

		// Create user (Instagram username = WordPress username per registry)
		$instagram = $data['instagram'];
		$user_args = [
			'user_login'   => $instagram,
			'user_email'   => $data['email'],
			'user_pass'    => $data['password'],
			'display_name' => $data['social_name'],
			'role'         => 'subscriber',
		];

		$user_id = wp_insert_user( $user_args );

		if ( is_wp_error( $user_id ) ) {
			wp_send_json_error([
				'message' => $user_id->get_error_message(),
				'code'    => 'creation_failed',
			], 500 );
		}

		// Save user meta per apollo-registry.json
		$this->save_user_meta( $user_id, $data );

		// Save quiz data if present
		if ( ! empty( $_POST['quiz_data'] ) ) {
			$quiz_data = json_decode( stripslashes( $_POST['quiz_data'] ), true );
			if ( is_array( $quiz_data ) ) {
				update_user_meta( $user_id, APOLLO_META_QUIZ_SCORE, (int) ( $quiz_data['total_score'] ?? 0 ) );
				update_user_meta( $user_id, APOLLO_META_SIMON_HIGHSCORE, (int) ( $quiz_data['simon_score'] ?? 0 ) );
				update_user_meta( $user_id, APOLLO_META_QUIZ_ANSWERS, $quiz_data['answers'] ?? [] );
			}
		}

		// Send verification email
		if ( get_option( 'apollo_login_require_email_verification', true ) ) {
			$token = apollo_login_generate_token();
			update_user_meta( $user_id, APOLLO_META_VERIFICATION_TOKEN, $token );
			update_user_meta( $user_id, APOLLO_META_EMAIL_VERIFIED, false );
			$this->send_verification_email( $user_id, $token );
		} else {
			update_user_meta( $user_id, APOLLO_META_EMAIL_VERIFIED, true );
		}

		// Fetch Instagram avatar in background
		$this->schedule_avatar_fetch( $user_id, $instagram );

		/**
		 * Action after successful registration
		 *
		 * @param int   $user_id Created user ID
		 * @param array $data    Registration data
		 */
		do_action( 'apollo_login_after_register', $user_id, $data );

		// Auto-login
		wp_set_current_user( $user_id );
		wp_set_auth_cookie( $user_id, false );

		$redirect = get_option( 'apollo_login_redirect_after_login', '/mural/' );

		wp_send_json_success([
			'message'  => __( 'Cadastro realizado com sucesso!', 'apollo-login' ),
			'redirect' => home_url( $redirect ),
			'user_id'  => $user_id,
		]);
	}

	private function sanitize_input( array $raw ): array {
		return [
			'social_name' => sanitize_text_field( $raw['social_name'] ?? $raw['nome'] ?? '' ),
			'instagram'   => apollo_login_sanitize_instagram( $raw['instagram'] ?? '' ),
			'email'       => sanitize_email( $raw['email'] ?? '' ),
			'password'    => $raw['senha'] ?? $raw['password'] ?? '',
			'doc_type'    => sanitize_text_field( $raw['doc_type'] ?? 'cpf' ),
			'cpf'         => preg_replace( '/\D/', '', $raw['cpf'] ?? '' ),
			'passport'    => sanitize_text_field( strtoupper( $raw['passport'] ?? '' ) ),
			'passport_country' => sanitize_text_field( $raw['passport_country'] ?? '' ),
			'sounds'      => array_map( 'intval', (array) ( $raw['sounds'] ?? [] ) ),
			'terms'       => ! empty( $raw['terms_accepted'] ) && '1' === $raw['terms_accepted'],
		];
	}

	private function validate( array $data ): array {
		$errors = [];

		// Step 1: Social name
		if ( empty( $data['social_name'] ) || mb_strlen( $data['social_name'] ) < 2 ) {
			$errors[] = __( 'Nome social deve ter pelo menos 2 caracteres.', 'apollo-login' );
		}

		// Step 2: Instagram (becomes username)
		if ( empty( $data['instagram'] ) ) {
			$errors[] = __( 'Instagram é obrigatório.', 'apollo-login' );
		} elseif ( ! preg_match( '/^[a-z0-9._]{1,30}$/', $data['instagram'] ) ) {
			$errors[] = __( 'Instagram inválido. Use apenas letras, números, ponto e underscore.', 'apollo-login' );
		} elseif ( username_exists( $data['instagram'] ) ) {
			$errors[] = __( 'Este Instagram já está registrado.', 'apollo-login' );
		}

		// Step 3: Email
		if ( empty( $data['email'] ) || ! is_email( $data['email'] ) ) {
			$errors[] = __( 'E-mail inválido.', 'apollo-login' );
		} elseif ( email_exists( $data['email'] ) ) {
			$errors[] = __( 'Este e-mail já está registrado.', 'apollo-login' );
		}

		// Step 4: Password
		if ( empty( $data['password'] ) || strlen( $data['password'] ) < 8 ) {
			$errors[] = __( 'Senha deve ter pelo menos 8 caracteres.', 'apollo-login' );
		}

		// Document validation
		if ( 'cpf' === $data['doc_type'] ) {
			if ( empty( $data['cpf'] ) || ! apollo_login_validate_cpf( $data['cpf'] ) ) {
				$errors[] = __( 'CPF inválido.', 'apollo-login' );
			}
		} elseif ( 'passport' === $data['doc_type'] ) {
			if ( empty( $data['passport'] ) || strlen( $data['passport'] ) < 5 ) {
				$errors[] = __( 'Número de passaporte inválido.', 'apollo-login' );
			}
		}

		// Step 6: Sounds (1-5)
		if ( empty( $data['sounds'] ) ) {
			$errors[] = __( 'Selecione pelo menos 1 gênero musical.', 'apollo-login' );
		} elseif ( count( $data['sounds'] ) > 5 ) {
			$errors[] = __( 'Máximo de 5 gêneros musicais.', 'apollo-login' );
		}

		// Terms
		if ( ! $data['terms'] ) {
			$errors[] = __( 'Você deve aceitar os termos de uso.', 'apollo-login' );
		}

		return $errors;
	}

	private function save_user_meta( int $user_id, array $data ): void {
		update_user_meta( $user_id, APOLLO_META_SOCIAL_NAME, $data['social_name'] );
		update_user_meta( $user_id, APOLLO_META_INSTAGRAM, $data['instagram'] );
		update_user_meta( $user_id, APOLLO_META_SOUND_PREFERENCES, $data['sounds'] );
		update_user_meta( $user_id, APOLLO_META_LOGIN_ATTEMPTS, 0 );

		if ( 'cpf' === $data['doc_type'] && ! empty( $data['cpf'] ) ) {
			update_user_meta( $user_id, '_apollo_cpf', $data['cpf'] );
			update_user_meta( $user_id, '_apollo_doc_type', 'cpf' );
		} elseif ( 'passport' === $data['doc_type'] ) {
			update_user_meta( $user_id, '_apollo_passport', $data['passport'] );
			update_user_meta( $user_id, '_apollo_passport_country', $data['passport_country'] );
			update_user_meta( $user_id, '_apollo_doc_type', 'passport' );
		}
	}

	private function send_verification_email( int $user_id, string $token ): void {
		$user = get_userdata( $user_id );
		if ( ! $user ) {
			return;
		}

		$verify_url = add_query_arg([
			'token'   => $token,
			'user_id' => $user_id,
		], home_url( '/' . APOLLO_LOGIN_PAGE_VERIFY . '/' ) );

		$social_name = get_user_meta( $user_id, APOLLO_META_SOCIAL_NAME, true ) ?: $user->display_name;

		$subject = __( 'Apollo::Rio — Confirme seu e-mail', 'apollo-login' );
		$message = sprintf(
			/* translators: 1: Social name, 2: Verification URL */
			__( "Olá %1\$s,\n\nConfirme seu e-mail clicando no link abaixo:\n\n%2\$s\n\nSe você não criou esta conta, ignore este e-mail.\n\n— Apollo::Rio", 'apollo-login' ),
			$social_name,
			$verify_url
		);

		$headers = [ 'Content-Type: text/plain; charset=UTF-8' ];

		wp_mail( $user->user_email, $subject, $message, $headers );
	}

	private function schedule_avatar_fetch( int $user_id, string $instagram ): void {
		if ( ! wp_next_scheduled( 'apollo_login_fetch_avatar', [ $user_id, $instagram ] ) ) {
			wp_schedule_single_event( time() + 5, 'apollo_login_fetch_avatar', [ $user_id, $instagram ] );
		}
	}

	public function check_username(): void {
		$username = apollo_login_sanitize_instagram( $_GET['username'] ?? '' );

		if ( empty( $username ) ) {
			wp_send_json_error([ 'available' => false ]);
		}

		wp_send_json_success([
			'available' => ! username_exists( $username ),
			'username'  => $username,
		]);
	}

	public function check_email(): void {
		$email = sanitize_email( $_GET['email'] ?? '' );

		if ( empty( $email ) || ! is_email( $email ) ) {
			wp_send_json_error([ 'available' => false ]);
		}

		wp_send_json_success([
			'available' => ! email_exists( $email ),
			'email'     => $email,
		]);
	}
}
