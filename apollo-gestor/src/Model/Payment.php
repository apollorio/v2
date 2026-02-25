<?php

/**
 * Payment Model — CRUD for event finances
 *
 * @package Apollo\Gestor
 */

declare(strict_types=1);

namespace Apollo\Gestor\Model;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Payment {


	private static function table(): string {
		global $wpdb;
		return $wpdb->prefix . 'apollo_gestor_payments';
	}

	/**
	 * Get all payments for an event
	 */
	public static function get_by_event( int $event_id ): array {
		global $wpdb;
		$table = self::table();

		$rows = $wpdb->get_results( // phpcs:ignore WordPress.DB.PreparedSQL
			$wpdb->prepare(
				"SELECT * FROM {$table} WHERE event_id = %d ORDER BY status ASC, due_date ASC",
				$event_id
			),
			ARRAY_A
		) ?: array();

		foreach ( $rows as &$row ) {
			if ( $row['payee_type'] === 'staff' ) {
				$user                = get_userdata( (int) $row['payee_id'] );
				$row['payee_name']   = $user ? $user->display_name : 'Desconhecido';
				$row['payee_avatar'] = $user ? get_avatar_url( $user->ID, array( 'size' => 44 ) ) : '';
			} else {
				$row['payee_name']   = get_the_title( (int) $row['payee_id'] ) ?: sanitize_text_field( $row['description'] );
				$row['payee_avatar'] = '';
			}
		}

		return $rows;
	}

	/**
	 * Get financial summary for an event
	 *
	 * @return array { budget, staff_total, supplier_total, production_total, paid, pending, late, balance }
	 */
	public static function get_summary( int $event_id ): array {
		global $wpdb;
		$table = self::table();

		$budget = (float) get_post_meta( $event_id, '_event_budget', true );

		$rows = $wpdb->get_results( // phpcs:ignore WordPress.DB.PreparedSQL
			$wpdb->prepare(
				"SELECT payee_type, category, status, SUM(amount) AS total
                 FROM {$table}
                 WHERE event_id = %d
                 GROUP BY payee_type, category, status",
				$event_id
			),
			ARRAY_A
		) ?: array();

		$summary = array(
			'budget'           => $budget,
			'staff_total'      => 0.0,
			'supplier_total'   => 0.0,
			'production_total' => 0.0,
			'paid'             => 0.0,
			'pending'          => 0.0,
			'late'             => 0.0,
		);

		foreach ( $rows as $row ) {
			$total = (float) $row['total'];

			if ( $row['payee_type'] === 'staff' ) {
				$summary['staff_total'] += $total;
			} else {
				$summary['supplier_total'] += $total;
			}

			if ( $row['category'] === 'production' ) {
				$summary['production_total'] += $total;
			}

			switch ( $row['status'] ) {
				case 'paid':
					$summary['paid'] += $total;
					break;
				case 'pending':
					$summary['pending'] += $total;
					break;
				case 'late':
					$summary['late'] += $total;
					break;
			}
		}

		$total_spent        = $summary['paid'] + $summary['pending'] + $summary['late'];
		$summary['balance'] = $budget - $total_spent;

		return $summary;
	}

	/**
	 * Create a payment
	 */
	public static function create( array $data ): int|false {
		global $wpdb;

		$insert = array(
			'event_id'    => absint( $data['event_id'] ?? 0 ),
			'payee_type'  => sanitize_key( $data['payee_type'] ?? 'staff' ),
			'payee_id'    => absint( $data['payee_id'] ?? 0 ),
			'description' => sanitize_text_field( $data['description'] ?? '' ),
			'category'    => sanitize_text_field( $data['category'] ?? '' ),
			'amount'      => floatval( $data['amount'] ?? 0 ),
			'pix_key'     => sanitize_text_field( $data['pix_key'] ?? '' ),
			'status'      => sanitize_key( $data['status'] ?? 'pending' ),
			'due_date'    => ! empty( $data['due_date'] ) ? sanitize_text_field( $data['due_date'] ) : null,
			'created_by'  => get_current_user_id(),
		);

		$result = $wpdb->insert( self::table(), $insert );

		return $result ? (int) $wpdb->insert_id : false;
	}

	/**
	 * Update payment
	 */
	public static function update( int $id, array $data ): bool {
		global $wpdb;

		$allowed = array( 'description', 'category', 'amount', 'pix_key', 'status', 'due_date', 'paid_at' );
		$update  = array();

		foreach ( $allowed as $field ) {
			if ( ! array_key_exists( $field, $data ) ) {
				continue;
			}
			$update[ $field ] = $field === 'amount' ? floatval( $data[ $field ] ) : sanitize_text_field( $data[ $field ] );
		}

		if ( empty( $update ) ) {
			return false;
		}

		return false !== $wpdb->update( self::table(), $update, array( 'id' => $id ) );
	}

	/**
	 * Delete a payment
	 */
	public static function delete( int $id ): bool {
		global $wpdb;
		return (bool) $wpdb->delete( self::table(), array( 'id' => $id ), array( '%d' ) );
	}
}
