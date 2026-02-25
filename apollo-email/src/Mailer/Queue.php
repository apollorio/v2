<?php

/**
 * Email Queue Manager — batch sending with retry, priority, scheduling.
 *
 * @package Apollo\Email\Mailer
 * @since   1.0.0
 */

declare(strict_types=1);

namespace Apollo\Email\Mailer;

use Apollo\Email\Plugin;
use Apollo\Email\Log\Logger;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Queue {

	private Sender $sender;
	private Logger $logger;

	public function __construct( Sender $sender, Logger $logger ) {
		$this->sender = $sender;
		$this->logger = $logger;
	}

	/**
	 * Add a message to the queue.
	 *
	 * @param string      $to       Recipient email.
	 * @param string      $subject  Email subject.
	 * @param string      $body     Rendered HTML body (or empty if using template).
	 * @param string      $template Template slug.
	 * @param array       $data     Template data.
	 * @param int         $priority Priority (1=highest, 10=lowest).
	 * @param string|null $scheduled_at Schedule for later (Y-m-d H:i:s).
	 * @return int|false Queue ID or false on failure.
	 */
	public function enqueue(
		string $to,
		string $subject,
		string $body = '',
		string $template = '',
		array $data = array(),
		int $priority = 5,
		?string $scheduled_at = null
	): int|false {
		global $wpdb;
		$table = $wpdb->prefix . 'apollo_email_queue';

		$result = $wpdb->insert(
			$table,
			array(
				'to_email'      => sanitize_email( $to ),
				'to_name'       => $data['user_name'] ?? '',
				'subject'       => sanitize_text_field( $subject ),
				'body'          => $body,
				'template'      => $template ?: null,
				'template_data' => ! empty( $data ) ? wp_json_encode( $data ) : null,
				'priority'      => $priority,
				'status'        => 'pending',
				'attempts'      => 0,
				'max_attempts'  => (int) Plugin::setting( 'max_retries', APOLLO_EMAIL_MAX_RETRIES ),
				'scheduled_at'  => $scheduled_at ?: current_time( 'mysql' ),
				'created_at'    => current_time( 'mysql' ),
			)
		);

		if ( $result === false ) {
			return false;
		}

		$queue_id = (int) $wpdb->insert_id;

		/**
		 * Fires when a message is added to the queue.
		 *
		 * @param int    $queue_id Queue item ID.
		 * @param string $to       Recipient.
		 * @param string $template Template slug.
		 */
		do_action( 'apollo/email/queued', $queue_id, $to, $template );

		return $queue_id;
	}

	/**
	 * Process the next batch of pending messages (called by cron).
	 */
	public function processNext(): void {
		// Prevent concurrent processing
		$lock = get_transient( 'apollo_email_queue_lock' );
		if ( $lock ) {
			return;
		}
		set_transient( 'apollo_email_queue_lock', true, 300 ); // 5 min max lock

		try {
			$batch_size = (int) Plugin::setting( 'batch_size', APOLLO_EMAIL_BATCH_SIZE );
			$items      = $this->fetchPending( $batch_size );

			if ( empty( $items ) ) {
				delete_transient( 'apollo_email_queue_lock' );
				return;
			}

			foreach ( $items as $item ) {
				$this->markStatus( (int) $item->id, 'processing' );

				// Build message
				$message = new Message();
				$message->setTo( $item->to_email )
					->setSubject( $item->subject );

				if ( ! empty( $item->template ) ) {
					$data = $item->template_data ? json_decode( $item->template_data, true ) : array();
					$message->setTemplate( $item->template )
						->setData( $data ?: array() );
				} else {
					$message->setHtml( $item->body );
				}

				// Send
				$result = $this->sender->send( $message );

				if ( $result['success'] ) {
					$this->markSent( (int) $item->id );
				} else {
					$attempts = (int) $item->attempts + 1;
					$max      = (int) $item->max_attempts;

					if ( $attempts >= $max ) {
						$this->markStatus( (int) $item->id, 'failed', $result['error'] ?? '', $attempts );
					} else {
						$this->markStatus( (int) $item->id, 'pending', $result['error'] ?? '', $attempts );
					}
				}

				// Small delay between sends
				usleep( 100000 ); // 100ms
			}

			/**
			 * Fires after a queue batch is processed.
			 *
			 * @param int $count Number of messages processed.
			 */
			do_action( 'apollo/email/queue_processed', count( $items ) );
		} finally {
			delete_transient( 'apollo_email_queue_lock' );
		}
	}

	/**
	 * Fetch pending queue items.
	 */
	public function fetchPending( int $limit = 50 ): array {
		global $wpdb;
		$table = $wpdb->prefix . 'apollo_email_queue';
		$now   = current_time( 'mysql' );

		return $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM {$table}
             WHERE status = 'pending'
               AND scheduled_at <= %s
               AND attempts < max_attempts
             ORDER BY priority ASC, scheduled_at ASC, id ASC
             LIMIT %d",
				$now,
				$limit
			)
		);
	}

	/**
	 * Mark item as sent.
	 */
	private function markSent( int $id ): void {
		global $wpdb;
		$table = $wpdb->prefix . 'apollo_email_queue';

		$wpdb->update(
			$table,
			array(
				'status'  => 'sent',
				'sent_at' => current_time( 'mysql' ),
			),
			array( 'id' => $id )
		);
	}

	/**
	 * Mark item with a status.
	 */
	private function markStatus( int $id, string $status, string $error = '', int $attempts = 0 ): void {
		global $wpdb;
		$table = $wpdb->prefix . 'apollo_email_queue';

		$data = array( 'status' => $status );
		if ( $error ) {
			$data['error_message'] = $error;
		}
		if ( $attempts > 0 ) {
			$data['attempts'] = $attempts;
		}

		$wpdb->update( $table, $data, array( 'id' => $id ) );
	}

	/**
	 * Cancel a queued email.
	 */
	public function cancel( int $id ): bool {
		global $wpdb;
		$table = $wpdb->prefix . 'apollo_email_queue';

		return (bool) $wpdb->update(
			$table,
			array( 'status' => 'cancelled' ),
			array(
				'id'     => $id,
				'status' => 'pending',
			)
		);
	}

	/**
	 * Retry a failed email.
	 */
	public function retry( int $id ): bool {
		global $wpdb;
		$table = $wpdb->prefix . 'apollo_email_queue';

		return (bool) $wpdb->update(
			$table,
			array(
				'status'        => 'pending',
				'attempts'      => 0,
				'error_message' => null,
			),
			array(
				'id'     => $id,
				'status' => 'failed',
			)
		);
	}

	/**
	 * Get queue statistics.
	 *
	 * @return array{pending: int, processing: int, sent: int, failed: int, total: int}
	 */
	public function getStats(): array {
		global $wpdb;
		$table = $wpdb->prefix . 'apollo_email_queue';

		$results = $wpdb->get_results(
			"SELECT status, COUNT(*) as count FROM {$table} GROUP BY status"
		);

		$stats = array(
			'pending'    => 0,
			'processing' => 0,
			'sent'       => 0,
			'failed'     => 0,
			'cancelled'  => 0,
			'total'      => 0,
		);
		foreach ( $results as $row ) {
			$stats[ $row->status ] = (int) $row->count;
			$stats['total']       += (int) $row->count;
		}

		return $stats;
	}

	/**
	 * Get queue items with pagination.
	 *
	 * @param string $status Filter by status (or empty for all).
	 * @param int    $per_page Items per page.
	 * @param int    $page Current page.
	 * @return array{items: array, total: int, pages: int}
	 */
	public function getItems( string $status = '', int $per_page = 20, int $page = 1 ): array {
		global $wpdb;
		$table  = $wpdb->prefix . 'apollo_email_queue';
		$offset = ( $page - 1 ) * $per_page;

		$where  = '1=1';
		$params = array();

		if ( $status ) {
			$where   .= ' AND status = %s';
			$params[] = $status;
		}

		// Count total
		$count_query = "SELECT COUNT(*) FROM {$table} WHERE {$where}";
		$total       = $params
			? (int) $wpdb->get_var( $wpdb->prepare( $count_query, ...$params ) )
			: (int) $wpdb->get_var( $count_query );

		// Fetch items
		$query    = "SELECT * FROM {$table} WHERE {$where} ORDER BY id DESC LIMIT %d OFFSET %d";
		$params[] = $per_page;
		$params[] = $offset;

		$items = $wpdb->get_results( $wpdb->prepare( $query, ...$params ) );

		return array(
			'items' => $items,
			'total' => $total,
			'pages' => (int) ceil( $total / $per_page ),
		);
	}

	/**
	 * Purge old sent/failed items.
	 *
	 * @param int $days Items older than this many days.
	 * @return int Number of deleted items.
	 */
	public function purge( int $days = 30 ): int {
		global $wpdb;
		$table  = $wpdb->prefix . 'apollo_email_queue';
		$cutoff = gmdate( 'Y-m-d H:i:s', time() - ( $days * DAY_IN_SECONDS ) );

		return (int) $wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$table} WHERE status IN ('sent', 'failed', 'cancelled') AND created_at < %s",
				$cutoff
			)
		);
	}
}
