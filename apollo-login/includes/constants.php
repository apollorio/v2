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

// REST API
define( 'APOLLO_LOGIN_REST_NAMESPACE', 'apollo/v1' );

// Quiz System
define( 'APOLLO_LOGIN_QUIZ_STAGES', 4 );
define( 'APOLLO_LOGIN_SIMON_LEVELS', 4 );
define( 'APOLLO_LOGIN_REACTION_TARGETS', 4 );

// Security
define( 'APOLLO_LOGIN_MAX_ATTEMPTS', 3 );
define( 'APOLLO_LOGIN_LOCKOUT_DURATION', 60 ); // seconds
define( 'APOLLO_LOGIN_CUSTOM_LOGIN_SLUG', 'acesso' );
define( 'APOLLO_LOGIN_CUSTOM_REGISTER_SLUG', 'registre' );

// Database Tables (without prefix)
define( 'APOLLO_LOGIN_TABLE_QUIZ_RESULTS', 'apollo_quiz_results' );
define( 'APOLLO_LOGIN_TABLE_SIMON_SCORES', 'apollo_simon_scores' );
define( 'APOLLO_LOGIN_TABLE_LOGIN_ATTEMPTS', 'apollo_login_attempts' );
define( 'APOLLO_LOGIN_TABLE_URL_REWRITES', 'apollo_url_rewrites' );
// User Meta Keys
define( 'APOLLO_META_SOCIAL_NAME', '_apollo_social_name' );
define( 'APOLLO_META_EMAIL_VERIFIED', '_apollo_email_verified' );
define( 'APOLLO_META_VERIFICATION_TOKEN', '_apollo_verification_token' );
define( 'APOLLO_META_PASSWORD_RESET_TOKEN', '_apollo_password_reset_token' );
define( 'APOLLO_META_PASSWORD_RESET_EXPIRES', '_apollo_password_reset_expires' );
define( 'APOLLO_META_LOGIN_ATTEMPTS', '_apollo_login_attempts' );
define( 'APOLLO_META_LAST_LOGIN', '_apollo_last_login' );
define( 'APOLLO_META_LOCKOUT_UNTIL', '_apollo_lockout_until' );

// Pages
define( 'APOLLO_LOGIN_PAGE_RESET', 'reset' );
