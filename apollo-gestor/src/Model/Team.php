<?php

/**
 * Team Model — CRUD for event team assignments
 *
 * @package Apollo\Gestor
 */

declare(strict_types=1);

namespace Apollo\Gestor\Model;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Team {


	private static function table(): string {
		global $wpdb;
		return $wpdb->prefix . 'apollo_gestor_team';
	}

	/**
	 * Get team members for an event
	 *
	 * @param int $event_id
	 * @return array  Rows with user_id, role, job_function, pix_key + WP user data
	 */
	public static function get_by_event( int $event_id ): array {
		global $wpdb;
		$table = self::table();

		$rows = $wpdb->get_results( // phpcs:ignore WordPress.DB.PreparedSQL
			$wpdb->prepare(
				"SELECT t.*, u.display_name, u.user_email
                 FROM {$table} t
                 LEFT JOIN {$wpdb->users} u ON u.ID = t.user_id
                 WHERE t.event_id = %d
                 ORDER BY FIELD(t.role, 'adm', 'gestor', 'tgestor', 'team'), t.id ASC",
				$event_id
			),
			ARRAY_A
		) ?: array();

		// Enrich with avatar and task count
		foreach ( $rows as &$row ) {
			$row['avatar_url']  = get_avatar_url( (int) $row['user_id'], array( 'size' => 112 ) );
			$row['task_count']  = self::get_task_count( (int) $row['user_id'], $event_id );
			$row['event_count'] = self::get_event_count( (int) $row['user_id'] );
			$row['pix_masked']  = self::mask_pix( $row['pix_key'] ?? '' );
		}

		return $rows;
	}

	/**
	 * Add a user to event team
	 *
	 * @param array $data
	 * @return int|false
	 */
	public static function add( array $data ): int|false {
		global $wpdb;

		$insert = array(
			'event_id'     => absint( $data['event_id'] ?? 0 ),
			'user_id'      => absint( $data['user_id'] ?? 0 ),
			'role'         => sanitize_key( $data['role'] ?? 'team' ),
			'job_function' => sanitize_text_field( $data['job_function'] ?? '' ),
			'pix_key'      => sanitize_text_field( $data['pix_key'] ?? '' ),
		);

		$result = $wpdb->insert( self::table(), $insert, array( '%d', '%d', '%s', '%s', '%s' ) );

		return $result ? (int) $wpdb->insert_id : false;
	}

	/**
	 * Update team member
	 *
	 * @param int   $id
	 * @param array $data
	 * @return bool
	 */
	public static function update( int $id, array $data ): bool {
		global $wpdb;

		$allowed = array( 'role', 'job_function', 'pix_key' );
		$update  = array();
		$formats = array();

		foreach ( $allowed as $field ) {
			if ( ! array_key_exists( $field, $data ) ) {
				continue;
			}
			$update[ $field ] = sanitize_text_field( $data[ $field ] );
			$formats[]        = '%s';
		}

		if ( empty( $update ) ) {
			return false;
		}

		return false !== $wpdb->update( self::table(), $update, array( 'id' => $id ), $formats, array( '%d' ) );
	}

	/**
	 * Remove from team
	 *
	 * @param int $id
	 * @return bool
	 */
	public static function remove( int $id ): bool {
		global $wpdb;
		return (bool) $wpdb->delete( self::table(), array( 'id' => $id ), array( '%d' ) );
	}

	/**
	 * Get all event IDs where user is on the team
	 *
	 * @param int $user_id
	 * @return int[]
	 */
	public static function get_user_event_ids( int $user_id ): array {
		global $wpdb;
		$table = self::table();

		$ids = $wpdb->get_col( // phpcs:ignore WordPress.DB.PreparedSQL
			$wpdb->prepare(
				"SELECT DISTINCT event_id FROM {$table} WHERE user_id = %d",
				$user_id
			)
		);

		return array_map( 'intval', $ids ?: array() );
	}

	/**
	 * Check if user has gestor-level access to event
	 *
	 * @param int $user_id
	 * @param int $event_id
	 * @return bool
	 */
	public static function can_manage( int $user_id, int $event_id ): bool {
		if ( current_user_can( 'manage_options' ) ) {
			return true;
		}

		global $wpdb;
		$table = self::table();

		$role = $wpdb->get_var( // phpcs:ignore WordPress.DB.PreparedSQL
			$wpdb->prepare(
				"SELECT role FROM {$table} WHERE event_id = %d AND user_id = %d",
				$event_id,
				$user_id
			)
		);

		return in_array( $role, array( 'adm', 'gestor' ), true );
	}

	/**
	 * Check if user can view finances for an event
	 *
	 * @param int $user_id
	 * @param int $event_id
	 * @return bool
	 */
	public static function can_view_finance( int $user_id, int $event_id ): bool {
		if ( current_user_can( 'manage_options' ) ) {
			return true;
		}

		global $wpdb;
		$table = self::table();

		$role = $wpdb->get_var( // phpcs:ignore WordPress.DB.PreparedSQL
			$wpdb->prepare(
				"SELECT role FROM {$table} WHERE event_id = %d AND user_id = %d",
				$event_id,
				$user_id
			)
		);

		return in_array( $role, array( 'adm', 'gestor', 'tgestor' ), true );
	}

	/**
	 * Get task count for user in event
	 */
	private static function get_task_count( int $user_id, int $event_id ): int {
		global $wpdb;
		$table = $wpdb->prefix . 'apollo_gestor_tasks';

		return (int) $wpdb->get_var( // phpcs:ignore WordPress.DB.PreparedSQL
			$wpdb->prepare(
				"SELECT COUNT(*) FROM {$table} WHERE assignee_id = %d AND event_id = %d AND status != 'cancelled'",
				$user_id,
				$event_id
			)
		);
	}

	/**
	 * Count how many events user is on
	 */
	private static function get_event_count( int $user_id ): int {
		global $wpdb;
		$table = self::table();

		return (int) $wpdb->get_var( // phpcs:ignore WordPress.DB.PreparedSQL
			$wpdb->prepare( "SELECT COUNT(DISTINCT event_id) FROM {$table} WHERE user_id = %d", $user_id )
		);
	}

	/**
	 * Mask PIX key for display
	 * "valle@email.com" → "va***@email.com"
	 * "11999887766" → "(11) 9***-7766"
	 * "12345678900" → "CPF ***456***"
	 */
	private static function mask_pix( string $pix ): string {
		if ( empty( $pix ) ) {
			return '';
		}

		// Email
		if ( str_contains( $pix, '@' ) ) {
			$parts = explode( '@', $pix );
			$name  = $parts[0];
			return substr( $name, 0, 2 ) . '***@' . $parts[1];
		}

		// CPF (11 digits, no special chars)
		if ( preg_match( '/^\d{11}$/', $pix ) ) {
			return 'CPF ***' . substr( $pix, 3, 3 ) . '***';
		}

		// Phone
		if ( preg_match( '/^\d{10,}$/', preg_replace( '/\D/', '', $pix ) ) ) {
			$clean = preg_replace( '/\D/', '', $pix );
			return '(' . substr( $clean, 0, 2 ) . ') 9***-' . substr( $clean, -4 );
		}

		// Generic
		return substr( $pix, 0, 3 ) . '***' . substr( $pix, -3 );
	}
}
