<?php
/**
 * Apollo Ecosystem — Database Table Constants
 *
 * Typed constants for ALL 41+ custom tables.
 * Eliminates magic strings — use ApolloTable::full('chat_messages') or ApolloTable::CHAT_MESSAGES.
 *
 * Table names stored WITHOUT wp_prefix. Use ::full() to get {wp_prefix}apollo_{suffix}.
 *
 * Usage:
 *   use Apollo\Core\Config\ApolloTable;
 *   global $wpdb;
 *   $table = ApolloTable::full(ApolloTable::CHAT_MESSAGES); // wp_apollo_chat_messages
 *   $wpdb->query("SELECT * FROM {$table}");
 *
 * @package Apollo\Core\Config
 * @since   6.1.0
 */

declare(strict_types=1);

namespace Apollo\Core\Config;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class ApolloTable {

	/*
	═══════════════════════════════════════════════════════════════════
	 * L0 — CORE
	 * ═══════════════════════════════════════════════════════════════════ */

	public const AUDIT_LOG = 'apollo_audit_log';

	/*
	═══════════════════════════════════════════════════════════════════
	 * L1 — AUTH
	 * ═══════════════════════════════════════════════════════════════════ */

	public const QUIZ_RESULTS   = 'apollo_quiz_results';
	public const SIMON_SCORES   = 'apollo_simon_scores';
	public const LOGIN_ATTEMPTS = 'apollo_login_attempts';
	public const URL_REWRITES   = 'apollo_url_rewrites';
	public const MATCHMAKING    = 'apollo_matchmaking';
	public const USER_FIELDS    = 'apollo_user_fields';
	public const PROFILE_VIEWS  = 'apollo_profile_views';

	/*
	═══════════════════════════════════════════════════════════════════
	 * L1 — MEMBERSHIP / GAMIFICATION
	 * ═══════════════════════════════════════════════════════════════════ */

	public const ACHIEVEMENTS   = 'apollo_achievements';
	public const POINTS         = 'apollo_points';
	public const RANKS          = 'apollo_ranks';
	public const TRIGGERS       = 'apollo_triggers';
	public const STEPS          = 'apollo_steps';
	public const MEMBERSHIP_LOG = 'apollo_membership_log';

	/*
	═══════════════════════════════════════════════════════════════════
	 * L3 — SOCIAL
	 * ═══════════════════════════════════════════════════════════════════ */

	public const ACTIVITY      = 'apollo_activity';
	public const CONNECTIONS   = 'apollo_connections';
	public const GROUPS        = 'apollo_groups';
	public const GROUP_MEMBERS = 'apollo_group_members';
	public const GROUP_META    = 'apollo_group_meta';
	public const FAVS          = 'apollo_favs';
	public const WOWS          = 'apollo_wows';
	public const WOW_TYPES     = 'apollo_wow_types';

	/*
	═══════════════════════════════════════════════════════════════════
	 * L4 — COMMUNICATION
	 * ═══════════════════════════════════════════════════════════════════ */

	public const NOTIFICATIONS     = 'apollo_notifications';
	public const NOTIF_PREFS       = 'apollo_notif_prefs';
	public const EMAIL_QUEUE       = 'apollo_email_queue';
	public const EMAIL_LOG         = 'apollo_email_log';
	public const CHAT_MESSAGES     = 'apollo_chat_messages';
	public const CHAT_THREADS      = 'apollo_chat_threads';
	public const CHAT_PARTICIPANTS = 'apollo_chat_participants';
	public const CHAT_TYPING       = 'apollo_chat_typing';
	public const CHAT_PRESENCE     = 'apollo_chat_presence';
	public const CHAT_BLOCKS       = 'apollo_chat_blocks';
	public const CHAT_ATTACHMENTS  = 'apollo_chat_attachments';

	/*
	═══════════════════════════════════════════════════════════════════
	 * L5 — DOCUMENTS
	 * ═══════════════════════════════════════════════════════════════════ */

	public const DOC_VERSIONS      = 'apollo_doc_versions';
	public const DOC_DOWNLOADS     = 'apollo_doc_downloads';
	public const SIGNATURES        = 'apollo_signatures';
	public const SIGNATURE_AUDIT   = 'apollo_signature_audit';
	public const GESTOR_TASKS      = 'apollo_gestor_tasks';
	public const GESTOR_TEAM       = 'apollo_gestor_team';
	public const GESTOR_PAYMENTS   = 'apollo_gestor_payments';
	public const GESTOR_MILESTONES = 'apollo_gestor_milestones';
	public const GESTOR_ACTIVITY   = 'apollo_gestor_activity';

	/*
	═══════════════════════════════════════════════════════════════════
	 * L7 — ADMIN / MODERATION / STATS
	 * ═══════════════════════════════════════════════════════════════════ */

	public const MOD_QUEUE     = 'apollo_mod_queue';
	public const MOD_LOG       = 'apollo_mod_log';
	public const STATS_EVENTS  = 'apollo_stats_events';
	public const STATS_USERS   = 'apollo_stats_users';
	public const STATS_CONTENT = 'apollo_stats_content';

	/*
	═══════════════════════════════════════════════════════════════════
	 * L8 — INDUSTRY
	 * ═══════════════════════════════════════════════════════════════════ */

	public const INDUSTRY_CALENDAR = 'apollo_industry_calendar';

	/*
	═══════════════════════════════════════════════════════════════════
	 * HELPERS
	 * ═══════════════════════════════════════════════════════════════════ */

	/**
	 * Get the full table name including WordPress prefix.
	 *
	 * @param string $suffix Table suffix (use class constants).
	 * @return string Full table name: {wp_prefix}{suffix}
	 */
	public static function full( string $suffix ): string {
		global $wpdb;

		return $wpdb->prefix . $suffix;
	}

	/**
	 * Get all table suffixes.
	 *
	 * @return string[]
	 */
	public static function all(): array {
		return array(
			self::AUDIT_LOG,
			self::QUIZ_RESULTS,
			self::SIMON_SCORES,
			self::LOGIN_ATTEMPTS,
			self::URL_REWRITES,
			self::MATCHMAKING,
			self::USER_FIELDS,
			self::PROFILE_VIEWS,
			self::ACHIEVEMENTS,
			self::POINTS,
			self::RANKS,
			self::TRIGGERS,
			self::STEPS,
			self::MEMBERSHIP_LOG,
			self::ACTIVITY,
			self::CONNECTIONS,
			self::GROUPS,
			self::GROUP_MEMBERS,
			self::GROUP_META,
			self::FAVS,
			self::WOWS,
			self::WOW_TYPES,
			self::NOTIFICATIONS,
			self::NOTIF_PREFS,
			self::EMAIL_QUEUE,
			self::EMAIL_LOG,
			self::CHAT_MESSAGES,
			self::CHAT_THREADS,
			self::CHAT_PARTICIPANTS,
			self::CHAT_TYPING,
			self::CHAT_PRESENCE,
			self::CHAT_BLOCKS,
			self::CHAT_ATTACHMENTS,
			self::DOC_VERSIONS,
			self::DOC_DOWNLOADS,
			self::SIGNATURES,
			self::SIGNATURE_AUDIT,
			self::GESTOR_TASKS,
			self::GESTOR_TEAM,
			self::GESTOR_PAYMENTS,
			self::GESTOR_MILESTONES,
			self::GESTOR_ACTIVITY,
			self::MOD_QUEUE,
			self::MOD_LOG,
			self::STATS_EVENTS,
			self::STATS_USERS,
			self::STATS_CONTENT,
			self::INDUSTRY_CALENDAR,
		);
	}

	/**
	 * Get tables owned by a specific plugin.
	 *
	 * @return string[]
	 */
	public static function by_owner( string $plugin ): array {
		$config = require dirname( __DIR__, 2 ) . '/config/tables.php';
		$result = array();

		foreach ( $config as $table => $def ) {
			if ( ( $def['owner'] ?? '' ) === $plugin ) {
				$result[] = $table;
			}
		}

		return $result;
	}

	/**
	 * Check if a table exists in the database.
	 */
	public static function exists( string $suffix ): bool {
		global $wpdb;

		$table = self::full( $suffix );
		return $wpdb->get_var(
			$wpdb->prepare( 'SHOW TABLES LIKE %s', $table )
		) === $table;
	}

	/** Prevent instantiation. */
	private function __construct() {}
}
