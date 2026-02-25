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
		add_filter( 'registration_errors', array( $this, 'validate_quiz_completion' ), 10, 3 );
		add_action( 'user_register', array( $this, 'on_user_register' ) );
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
	 * Handle user registration
	 *
	 * @param int $user_id User ID.
	 * @return void
	 */
	public function on_user_register( int $user_id ): void {
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

		// Save social name (TRANS/QUEER INCLUSIVE)
		$social_name = isset( $_POST['social_name'] ) ? sanitize_text_field( $_POST['social_name'] ) : '';
		if ( ! empty( $social_name ) ) {
			update_user_meta( $user_id, '_apollo_social_name', $social_name );
			wp_update_user( array( 'ID' => $user_id, 'display_name' => $social_name ) );
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
			// Validate that sounds exist in taxonomy
			$valid_sounds = array();
			foreach ( $sounds as $sound_id ) {
				$term = get_term( $sound_id, 'sound' );
				if ( $term && ! is_wp_error( $term ) ) {
					$valid_sounds[] = $sound_id;
				}
			}
			// Save to user meta (for matchmaking)
			update_user_meta( $user_id, '_apollo_sound_preferences', $valid_sounds );
		}

		// Generate verification token and send email
		$token = \Apollo\Login\apollo_generate_verification_token( $user_id );
		$this->send_verification_email( $user_id, $token );

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

		$html = curl_exec( $ch );
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
