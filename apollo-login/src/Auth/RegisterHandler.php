<?php

/**
 * Register Handler
 *
 * @package Apollo\Login
 */

declare(strict_types=1);

namespace Apollo\Login\Auth;

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register Handler class
 */
class RegisterHandler {


	/**
	 * Constructor
	 */
	public function __construct() {
		// AJAX registration handler — JS sends action:'apollo_register' to admin-ajax.php
		add_action( 'wp_ajax_nopriv_apollo_register', array( $this, 'handle_ajax_register' ) );
		add_action( 'wp_ajax_apollo_register', array( $this, 'handle_ajax_register' ) );

		add_filter( 'registration_errors', array( $this, 'validate_quiz_completion' ), 10, 3 );
		add_filter( 'registration_errors', array( $this, 'validate_cpf' ), 15, 3 );

		// NOTE: on_user_register is only for NON-AJAX registrations (e.g. wp-admin).
		// handle_ajax_register() manages its own meta/tokens/hooks to avoid double-processing.
		add_action( 'user_register', array( $this, 'on_user_register' ) );

		// AJAX CPF validation (real-time, both logged-in and not)
		add_action( 'wp_ajax_nopriv_apollo_validate_cpf', array( $this, 'ajax_validate_cpf' ) );
		add_action( 'wp_ajax_apollo_validate_cpf', array( $this, 'ajax_validate_cpf' ) );
	}

	/**
	 * Handle AJAX registration from /acesso → aptitude quiz → FINALIZAR REGISTRO
	 *
	 * @return void
	 */
	public function handle_ajax_register(): void {
		// Verify nonce
		if ( ! check_ajax_referer( 'apollo_auth_nonce', 'nonce', false ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Verificação de segurança falhou. Recarregue a página.', 'apollo-login' ),
					'code'    => 'nonce_failed',
				),
				403
			);
		}

		// Check if registration is open
		if ( ! get_option( 'users_can_register' ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Registro de novos usuários está desabilitado.', 'apollo-login' ),
					'code'    => 'registration_disabled',
				),
				403
			);
		}

		// Sanitize input
		$social_name = isset( $_POST['nome'] ) ? sanitize_text_field( wp_unslash( $_POST['nome'] ) ) : '';
		$instagram   = isset( $_POST['instagram'] ) ? sanitize_user( ltrim( wp_unslash( $_POST['instagram'] ), '@' ) ) : '';
		$email       = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
		$password    = isset( $_POST['senha'] ) ? $_POST['senha'] : '';
		$doc_type    = isset( $_POST['doc_type'] ) ? sanitize_text_field( wp_unslash( $_POST['doc_type'] ) ) : 'cpf';
		$cpf         = isset( $_POST['cpf'] ) ? preg_replace( '/[^0-9]/', '', sanitize_text_field( wp_unslash( $_POST['cpf'] ) ) ) : '';
		$passport    = isset( $_POST['passport'] ) ? sanitize_text_field( strtoupper( wp_unslash( $_POST['passport'] ) ) ) : '';
		$passport_co = isset( $_POST['passport_country'] ) ? sanitize_text_field( wp_unslash( $_POST['passport_country'] ) ) : '';
		$sounds      = isset( $_POST['sounds'] ) ? array_map( 'sanitize_text_field', (array) $_POST['sounds'] ) : array();
		$terms       = ! empty( $_POST['terms_accepted'] );
		$quiz_passed = ! empty( $_POST['quiz_passed'] );

		// --- Validation ---
		$errors = array();

		// Social name
		if ( empty( $social_name ) || mb_strlen( $social_name ) < 2 ) {
			$errors[] = __( 'Nome social deve ter pelo menos 2 caracteres.', 'apollo-login' );
		}

		// Instagram (becomes WordPress username)
		if ( empty( $instagram ) ) {
			$errors[] = __( 'Instagram é obrigatório.', 'apollo-login' );
		} elseif ( ! preg_match( '/^[a-z0-9._]{1,30}$/', strtolower( $instagram ) ) ) {
			$errors[] = __( 'Instagram inválido. Use apenas letras, números, ponto e underscore.', 'apollo-login' );
		} elseif ( username_exists( $instagram ) ) {
			$errors[] = __( 'Este Instagram já está registrado.', 'apollo-login' );
		}

		// Email
		if ( empty( $email ) || ! is_email( $email ) ) {
			$errors[] = __( 'E-mail inválido.', 'apollo-login' );
		} elseif ( email_exists( $email ) ) {
			$errors[] = __( 'Este e-mail já está registrado.', 'apollo-login' );
		}

		// Password
		if ( empty( $password ) || strlen( $password ) < 8 ) {
			$errors[] = __( 'Senha deve ter pelo menos 8 caracteres.', 'apollo-login' );
		}

		// Document validation
		if ( 'cpf' === $doc_type ) {
			if ( empty( $cpf ) || ! \Apollo\Login\apollo_validate_cpf( $cpf ) ) {
				$errors[] = __( 'CPF inválido.', 'apollo-login' );
			} elseif ( $this->cpf_exists( $cpf ) ) {
				$errors[] = __( 'Este CPF já está registrado.', 'apollo-login' );
			}
		} elseif ( 'passport' === $doc_type ) {
			if ( empty( $passport ) || strlen( $passport ) < 5 ) {
				$errors[] = __( 'Número de passaporte inválido.', 'apollo-login' );
			}
		}

		// Sounds
		if ( empty( $sounds ) ) {
			$errors[] = __( 'Selecione pelo menos 1 gênero musical.', 'apollo-login' );
		} elseif ( count( $sounds ) > 5 ) {
			$errors[] = __( 'Máximo de 5 gêneros musicais.', 'apollo-login' );
		}

		// Terms
		if ( ! $terms ) {
			$errors[] = __( 'Você deve aceitar os termos de uso.', 'apollo-login' );
		}

		// Quiz
		if ( ! $quiz_passed ) {
			$errors[] = __( 'Teste de aptidão é obrigatório.', 'apollo-login' );
		}

		if ( ! empty( $errors ) ) {
			wp_send_json_error(
				array(
					'message' => implode( ' ', $errors ),
					'errors'  => $errors,
					'code'    => 'validation_failed',
				),
				400
			);
		}

		// --- Create User ---
		$user_id = wp_insert_user(
			array(
				'user_login'   => strtolower( $instagram ),
				'user_email'   => $email,
				'user_pass'    => $password,
				'display_name' => $social_name,
				'role'         => 'subscriber',
			)
		);

		if ( is_wp_error( $user_id ) ) {
			wp_send_json_error(
				array(
					'message' => $user_id->get_error_message(),
					'code'    => 'creation_failed',
				),
				500
			);
		}

		// Flag: prevent on_user_register() from double-processing
		update_user_meta( $user_id, '_apollo_registered_via_ajax', true );

		// --- Save User Meta ---
		update_user_meta( $user_id, '_apollo_social_name', $social_name );
		update_user_meta( $user_id, '_apollo_instagram', strtolower( $instagram ) );
		update_user_meta( $user_id, '_apollo_sound_preferences', $sounds );
		update_user_meta( $user_id, '_apollo_membership', 'nao-verificado' );
		update_user_meta( $user_id, '_apollo_email_verified', false );
		update_user_meta( $user_id, '_apollo_login_attempts', 0 );

		// Document
		update_user_meta( $user_id, '_apollo_doc_type', $doc_type );
		if ( 'cpf' === $doc_type && ! empty( $cpf ) ) {
			update_user_meta( $user_id, '_apollo_cpf', $cpf );
		} elseif ( 'passport' === $doc_type && ! empty( $passport ) ) {
			update_user_meta( $user_id, '_apollo_passport', $passport );
			update_user_meta( $user_id, '_apollo_passport_country', $passport_co );
		}

		// --- Email Verification ---
		$token      = \Apollo\Login\apollo_generate_verification_token( $user_id );
		$verify_url = add_query_arg(
			array(
				'user'  => $user_id,
				'token' => $token,
			),
			home_url( '/verificar-email/' )
		);

		/**
		 * Fires after a new user registers.
		 *
		 * @since 1.0.0
		 * @param int   $user_id User ID.
		 * @param array $data    Registration data.
		 */
		do_action(
			'apollo/login/registered',
			$user_id,
			array(
				'social_name' => $social_name,
			)
		);

		/**
		 * Fires to request a verification email.
		 *
		 * @since 1.0.0
		 * @param int    $user_id    User ID.
		 * @param string $verify_url Verification URL.
		 */
		do_action( 'apollo/login/verification_email', $user_id, $verify_url );

		// Fallback: send plain text if apollo-email plugin is not active
		if ( ! class_exists( 'Apollo\\Email\\Plugin' ) ) {
			$this->send_fallback_verification_email( $user_id, $token );
		}

		// --- Auto-login ---
		wp_set_current_user( $user_id );
		wp_set_auth_cookie( $user_id, false );

		$redirect = apply_filters( 'apollo_login_redirect', home_url( '/explore' ), get_userdata( $user_id ) );

		wp_send_json_success(
			array(
				'message'  => __( 'Cadastro realizado com sucesso!', 'apollo-login' ),
				'redirect' => $redirect,
				'user_id'  => $user_id,
			)
		);
	}

	/**
	 * Send fallback verification email (when apollo-email is not active)
	 *
	 * @param int    $user_id User ID.
	 * @param string $token   Verification token.
	 * @return void
	 */
	private function send_fallback_verification_email( int $user_id, string $token ): void {
		$user = get_userdata( $user_id );
		if ( ! $user ) {
			return;
		}

		$verify_url = add_query_arg(
			array(
				'user'  => $user_id,
				'token' => $token,
			),
			home_url( '/verificar-email/' )
		);

		$social_name = get_user_meta( $user_id, '_apollo_social_name', true ) ?: $user->display_name;

		$subject = __( 'Apollo::Rio — Confirme seu e-mail', 'apollo-login' );
		$message = sprintf(
			__( "Olá %1\$s,\n\nConfirme seu e-mail clicando no link abaixo:\n\n%2\$s\n\nSe você não criou esta conta, ignore este e-mail.\n\n— Apollo::Rio", 'apollo-login' ),
			$social_name,
			$verify_url
		);

		wp_mail( $user->user_email, $subject, $message, array( 'Content-Type: text/plain; charset=UTF-8' ) );
	}

	/**
	 * Validate quiz completion before registration
	 *
	 * @param \WP_Error $errors               Error object.
	 * @param string    $sanitized_user_login Username.
	 * @param string    $user_email           Email.
	 * @return \WP_Error
	 */
	public function validate_quiz_completion( \WP_Error $errors, string $sanitized_user_login, string $user_email ): \WP_Error {
		// Check if quiz completion is stored in session/transient
		$quiz_token = $_POST['apollo_quiz_token'] ?? '';

		if ( empty( $quiz_token ) ) {
			$errors->add(
				'quiz_required',
				__( '<strong>ERROR</strong>: You must complete the aptitude quiz before registering.', 'apollo-login' )
			);
			return $errors;
		}

		// Verify quiz token
		$quiz_data = get_transient( 'apollo_quiz_' . $quiz_token );

		if ( false === $quiz_data ) {
			$errors->add(
				'quiz_expired',
				__( '<strong>ERROR</strong>: Quiz results expired. Please complete the quiz again.', 'apollo-login' )
			);
			return $errors;
		}

		// Check if all 4 stages completed
		$required_stages = array( 'pattern', 'simon', 'ethics', 'reaction' );
		foreach ( $required_stages as $stage ) {
			if ( ! isset( $quiz_data[ $stage ] ) ) {
				$errors->add(
					'quiz_incomplete',
					sprintf(
						__( '<strong>ERROR</strong>: Quiz stage "%s" not completed.', 'apollo-login' ),
						$stage
					)
				);
			}
		}

		return $errors;
	}

	/**
	 * Validate CPF during registration
	 *
	 * Full validation chain:
	 * 1. Strip non-digits
	 * 2. Check 11-digit length
	 * 3. Reject all-same-digit sequences (000…, 111…, etc.)
	 * 4. Mod-11 check-digit algorithm (Receita Federal standard)
	 * 5. Uniqueness — reject if CPF already registered to another user
	 *
	 * @param \WP_Error $errors               Error object.
	 * @param string    $sanitized_user_login Username.
	 * @param string    $user_email           Email.
	 * @return \WP_Error
	 */
	public function validate_cpf( \WP_Error $errors, string $sanitized_user_login, string $user_email ): \WP_Error {
		$doc_type = isset( $_POST['doc_type'] ) ? sanitize_text_field( wp_unslash( $_POST['doc_type'] ) ) : 'cpf';

		// Only validate when document type is CPF
		if ( 'cpf' !== $doc_type ) {
			return $errors;
		}

		$raw_cpf = isset( $_POST['cpf'] ) ? sanitize_text_field( wp_unslash( $_POST['cpf'] ) ) : '';
		$cpf     = preg_replace( '/[^0-9]/', '', $raw_cpf );

		// Required check
		if ( empty( $cpf ) ) {
			$errors->add(
				'cpf_required',
				__( '<strong>ERRO</strong>: CPF é obrigatório.', 'apollo-login' )
			);
			return $errors;
		}

		// Validate format + check digits via helper function
		if ( ! \Apollo\Login\apollo_validate_cpf( $cpf ) ) {
			$errors->add(
				'cpf_invalid',
				__( '<strong>ERRO</strong>: CPF inválido. Verifique os números digitados.', 'apollo-login' )
			);
			return $errors;
		}

		// Uniqueness — check if CPF already belongs to another user
		if ( $this->cpf_exists( $cpf ) ) {
			$errors->add(
				'cpf_exists',
				__( '<strong>ERRO</strong>: Este CPF já está registrado.', 'apollo-login' )
			);
			return $errors;
		}

		return $errors;
	}

	/**
	 * Check if a CPF already exists in the database
	 *
	 * @param string $cpf Clean CPF (11 digits).
	 * @return bool
	 */
	private function cpf_exists( string $cpf ): bool {
		global $wpdb;

		$existing = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT user_id FROM {$wpdb->usermeta} WHERE meta_key = '_apollo_cpf' AND meta_value = %s LIMIT 1",
				$cpf
			)
		);

		return ! empty( $existing );
	}

	/**
	 * AJAX handler — real-time CPF validation
	 *
	 * Performs full validation (format + algorithm + uniqueness) and returns
	 * a structured JSON response for the frontend to display live feedback.
	 *
	 * @return void
	 */
	public function ajax_validate_cpf(): void {
		// Accept both nonce names for flexibility
		$nonce_valid = check_ajax_referer( 'apollo_auth_nonce', 'nonce', false )
			|| check_ajax_referer( 'apollo_register_nonce', 'nonce', false );

		if ( ! $nonce_valid ) {
			wp_send_json_error( array( 'message' => __( 'Sessão expirada.', 'apollo-login' ) ), 403 );
		}

		$raw_cpf = isset( $_POST['cpf'] ) ? sanitize_text_field( wp_unslash( $_POST['cpf'] ) ) : '';
		$cpf     = preg_replace( '/[^0-9]/', '', $raw_cpf );

		// Step 1 — length
		if ( strlen( $cpf ) !== 11 ) {
			wp_send_json_error(
				array(
					'message' => __( 'CPF deve conter 11 dígitos.', 'apollo-login' ),
					'code'    => 'cpf_length',
				)
			);
		}

		// Step 2 — all-same-digit reject
		if ( preg_match( '/^(\d)\1{10}$/', $cpf ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'CPF inválido — sequência rejeitada.', 'apollo-login' ),
					'code'    => 'cpf_sequence',
				)
			);
		}

		// Step 3 — Mod-11 algorithm
		if ( ! \Apollo\Login\apollo_validate_cpf( $cpf ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'CPF inválido — dígitos verificadores incorretos.', 'apollo-login' ),
					'code'    => 'cpf_checkdigit',
				)
			);
		}

		// Step 4 — uniqueness
		if ( $this->cpf_exists( $cpf ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Este CPF já está registrado.', 'apollo-login' ),
					'code'    => 'cpf_exists',
				)
			);
		}

		// All checks passed
		wp_send_json_success(
			array(
				'message' => __( 'CPF válido e disponível.', 'apollo-login' ),
				'code'    => 'cpf_valid',
			)
		);
	}

	/**
	 * Handle user registration (non-AJAX only, e.g. wp-admin user creation)
	 *
	 * AJAX registrations are fully handled by handle_ajax_register() which
	 * sets a _apollo_registered_via_ajax flag to prevent double-processing.
	 *
	 * @param int $user_id User ID.
	 * @return void
	 */
	public function on_user_register( int $user_id ): void {
		// Skip if already processed by handle_ajax_register()
		if ( get_user_meta( $user_id, '_apollo_registered_via_ajax', true ) ) {
			delete_user_meta( $user_id, '_apollo_registered_via_ajax' );
			return;
		}

		// Get quiz token
		$quiz_token = $_POST['apollo_quiz_token'] ?? '';

		if ( empty( $quiz_token ) ) {
			return;
		}

		// Get quiz data
		$quiz_data = get_transient( 'apollo_quiz_' . $quiz_token );

		if ( false === $quiz_data ) {
			return;
		}

		// Save quiz results to database
		global $wpdb;
		$table = $wpdb->prefix . \APOLLO_LOGIN_TABLE_QUIZ_RESULTS;

		foreach ( $quiz_data as $stage => $data ) {
			$wpdb->insert(
				$table,
				array(
					'user_id'      => $user_id,
					'stage'        => $stage,
					'score'        => $data['score'] ?? 0,
					'answers'      => wp_json_encode( $data['answers'] ?? array() ),
					'completed_at' => current_time( 'mysql' ),
				),
				array( '%d', '%s', '%d', '%s', '%s' )
			);
		}

		// Calculate total score
		$total_score = array_sum( array_column( $quiz_data, 'score' ) );
		update_user_meta( $user_id, '_apollo_quiz_score', $total_score );

		// Store quiz answers
		update_user_meta( $user_id, '_apollo_quiz_answers', $quiz_data );

		// Set default membership
		update_user_meta( $user_id, '_apollo_membership', 'nao-verificado' );

		// Email not verified yet
		update_user_meta( $user_id, '_apollo_email_verified', false );

		// Save CPF or Passport (document identity)
		$doc_type = isset( $_POST['doc_type'] ) ? sanitize_text_field( wp_unslash( $_POST['doc_type'] ) ) : 'cpf';
		update_user_meta( $user_id, '_apollo_doc_type', $doc_type );

		if ( 'cpf' === $doc_type ) {
			$cpf = isset( $_POST['cpf'] ) ? preg_replace( '/[^0-9]/', '', sanitize_text_field( wp_unslash( $_POST['cpf'] ) ) ) : '';
			if ( ! empty( $cpf ) && \Apollo\Login\apollo_validate_cpf( $cpf ) ) {
				update_user_meta( $user_id, '_apollo_cpf', $cpf );
			}
		} elseif ( 'passport' === $doc_type ) {
			$passport         = isset( $_POST['passport'] ) ? sanitize_text_field( wp_unslash( $_POST['passport'] ) ) : '';
			$passport_country = isset( $_POST['passport_country'] ) ? sanitize_text_field( wp_unslash( $_POST['passport_country'] ) ) : '';
			if ( ! empty( $passport ) ) {
				update_user_meta( $user_id, '_apollo_passport', strtoupper( $passport ) );
				update_user_meta( $user_id, '_apollo_passport_country', $passport_country );
			}
		}

		// Save social name (TRANS/QUEER INCLUSIVE)
		$social_name = isset( $_POST['social_name'] ) ? sanitize_text_field( $_POST['social_name'] ) : '';
		if ( ! empty( $social_name ) ) {
			update_user_meta( $user_id, '_apollo_social_name', $social_name );
			wp_update_user(
				array(
					'ID'           => $user_id,
					'display_name' => $social_name,
				)
			);
		}

		// Save Instagram username
		$instagram = isset( $_POST['instagram_username'] ) ? sanitize_user( $_POST['instagram_username'] ) : '';
		if ( ! empty( $instagram ) ) {
			update_user_meta( $user_id, '_apollo_instagram', $instagram );

			// Fetch Instagram profile picture on first registration
			// User can edit/update later in profile settings
		}

		// Save sound preferences
		$sounds = isset( $_POST['sounds'] ) ? (array) $_POST['sounds'] : array();
		if ( ! empty( $sounds ) ) {
			// Sanitize sound preferences
			$valid_sounds = array_map( 'sanitize_text_field', $sounds );
			// Save to user meta (for matchmaking)
			update_user_meta( $user_id, '_apollo_sound_preferences', $valid_sounds );
		}

		// Generate verification token and send email
		$token = \Apollo\Login\apollo_generate_verification_token( $user_id );

		$verify_url = add_query_arg(
			array(
				'user'  => $user_id,
				'token' => $token,
			),
			home_url( '/verificar-email/' )
		);

		/**
		 * Fires after a new user registers — triggers apollo-email welcome email.
		 *
		 * @since 1.0.0
		 * @param int   $user_id User ID.
		 * @param array $data    Registration data.
		 */
		do_action(
			'apollo/login/registered',
			$user_id,
			array(
				'social_name' => $social_name,
			)
		);

		/**
		 * Fires to request a verification email — triggers apollo-email if active.
		 *
		 * @since 1.0.0
		 * @param int    $user_id    User ID.
		 * @param string $verify_url Verification URL.
		 */
		do_action( 'apollo/login/verification_email', $user_id, $verify_url );

		// Fallback: send plain text if apollo-email plugin is not active
		if ( ! class_exists( 'Apollo\\Email\\Plugin' ) ) {
			$this->send_verification_email( $user_id, $token );
		}

		// Delete quiz transient
		delete_transient( 'apollo_quiz_' . $quiz_token );
	}

	/**
	 * Send verification email
	 *
	 * @param int    $user_id User ID.
	 * @param string $token   Verification token.
	 * @return void
	 */
	private function send_verification_email( int $user_id, string $token ): void {
		$user = get_userdata( $user_id );

		if ( ! $user ) {
			return;
		}

		$verify_url = add_query_arg(
			array(
				'user'  => $user_id,
				'token' => $token,
			),
			home_url( '/verificar-email/' )
		);

		$subject = __( 'Verify your Apollo account', 'apollo-login' );

		$message = sprintf(
			__( 'Hi %s,', 'apollo-login' ) . "\n\n" .
				__( 'Please verify your email address by clicking the link below:', 'apollo-login' ) . "\n\n" .
				'%s' . "\n\n" .
				__( 'This link will expire in 24 hours.', 'apollo-login' ) . "\n\n" .
				__( 'If you did not create this account, please ignore this email.', 'apollo-login' ),
			$user->display_name,
			$verify_url
		);

		wp_mail( $user->user_email, $subject, $message );
	}

	/**
	 * Fetch Instagram profile picture and save as avatar
	 *
	 * @param int    $user_id  User ID.
	 * @param string $username Instagram username.
	 * @return void
	 */
	private function fetch_instagram_avatar( int $user_id, string $username ): void {
		if ( empty( $username ) ) {
			return;
		}

		$pic_url = $this->get_instagram_profile_pic( $username );

		if ( ! $pic_url ) {
			return;
		}

		// Require WordPress media functions
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		// Download image
		$attachment_id = media_sideload_image( $pic_url, 0, null, 'id' );

		if ( is_wp_error( $attachment_id ) ) {
			// Fallback: just save the URL
			update_user_meta( $user_id, '_apollo_avatar_url', $pic_url );
			return;
		}

		// Save attachment ID and URL
		$local_url = wp_get_attachment_url( $attachment_id );
		update_user_meta( $user_id, '_apollo_avatar_attachment_id', $attachment_id );
		update_user_meta( $user_id, '_apollo_avatar_url', $local_url );

		// Get relative path for UsersWP compatibility
		$upload_dir = wp_upload_dir();
		$relative   = str_replace( $upload_dir['baseurl'], '', $local_url );
		update_user_meta( $user_id, 'avatar_thumb', $relative );
	}

	/**
	 * Get Instagram profile picture URL
	 *
	 * @param string $username Instagram username.
	 * @return string|null Profile picture URL or null on failure.
	 */
	private function get_instagram_profile_pic( string $username ): ?string {
		$url = "https://www.instagram.com/$username/";

		// Initialize cURL
		$ch = curl_init( $url );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
		curl_setopt( $ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36' );
		curl_setopt( $ch, CURLOPT_TIMEOUT, 10 );

		$html      = curl_exec( $ch );
		$http_code = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
		curl_close( $ch );

		if ( 200 !== $http_code || empty( $html ) ) {
			return null;
		}

		// Try to extract profile pic from shared data
		if ( preg_match( '/<script type="text\/javascript">window\._sharedData = (.*);<\/script>/', $html, $matches ) ) {
			$data = json_decode( $matches[1], true );
			if ( isset( $data['entry_data']['ProfilePage'][0]['graphql']['user']['profile_pic_url_hd'] ) ) {
				return $data['entry_data']['ProfilePage'][0]['graphql']['user']['profile_pic_url_hd'];
			}
		}

		// Fallback: try meta og:image
		if ( preg_match( '/<meta property="og:image" content="([^"]+)"/', $html, $matches ) ) {
			return $matches[1];
		}

		return null;
	}
}
