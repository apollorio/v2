<?php
/**
 * Plugin Constants
 *
 * @package Apollo\Login
 */

declare(strict_types=1);

namespace Apollo\Login;

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// REST API namespace (shared across Apollo ecosystem)
define( 'APOLLO_LOGIN_REST_NAMESPACE', 'apollo/v1' );

// Database tables (without prefix) - per apollo-registry.json
define( 'APOLLO_LOGIN_TABLE_QUIZ_RESULTS', 'apollo_quiz_results' );
define( 'APOLLO_LOGIN_TABLE_SIMON_SCORES', 'apollo_simon_scores' );
define( 'APOLLO_LOGIN_TABLE_LOGIN_ATTEMPTS', 'apollo_login_attempts' );
define( 'APOLLO_LOGIN_TABLE_URL_REWRITES', 'apollo_url_rewrites' );

// User meta keys - per apollo-registry.json
define( 'APOLLO_META_SOCIAL_NAME', '_apollo_social_name' );
define( 'APOLLO_META_INSTAGRAM', '_apollo_instagram' );
define( 'APOLLO_META_AVATAR_URL', '_apollo_avatar_url' );
define( 'APOLLO_META_AVATAR_ATTACHMENT_ID', '_apollo_avatar_attachment_id' );
define( 'APOLLO_META_SOUND_PREFERENCES', '_apollo_sound_preferences' );
define( 'APOLLO_META_QUIZ_SCORE', '_apollo_quiz_score' );
define( 'APOLLO_META_SIMON_HIGHSCORE', '_apollo_simon_highscore' );
define( 'APOLLO_META_QUIZ_ANSWERS', '_apollo_quiz_answers' );
define( 'APOLLO_META_EMAIL_VERIFIED', '_apollo_email_verified' );
define( 'APOLLO_META_VERIFICATION_TOKEN', '_apollo_verification_token' );
define( 'APOLLO_META_PASSWORD_RESET_TOKEN', '_apollo_password_reset_token' );
define( 'APOLLO_META_PASSWORD_RESET_EXPIRES', '_apollo_password_reset_expires' );
define( 'APOLLO_META_LOGIN_ATTEMPTS', '_apollo_login_attempts' );
define( 'APOLLO_META_LAST_LOGIN', '_apollo_last_login' );
define( 'APOLLO_META_LOCKOUT_UNTIL', '_apollo_lockout_until' );

// Virtual page slugs - per apollo-registry.json
define( 'APOLLO_LOGIN_PAGE_ACESSO', 'acesso' );
define( 'APOLLO_LOGIN_PAGE_REGISTRE', 'registre' );
define( 'APOLLO_LOGIN_PAGE_SAIR', 'sair' );
define( 'APOLLO_LOGIN_PAGE_RESET', 'reset' );
define( 'APOLLO_LOGIN_PAGE_VERIFY', 'verificar-email' );

// Security
define( 'APOLLO_LOGIN_MAX_ATTEMPTS', 5 );
define( 'APOLLO_LOGIN_LOCKOUT_DURATION', 900 ); // 15 minutes
define( 'APOLLO_LOGIN_RATE_LIMIT_WINDOW', 300 ); // 5 minutes
