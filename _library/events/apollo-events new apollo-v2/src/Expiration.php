<?php
/**
 * Expiration — Sistema de expiração automática
 *
 * Evento vira "gone" (classe CSS + status visual) exatamente 30 minutos
 * após _event_end_date + _event_end_time.
 *
 * Usa WP-Cron para varredura periódica + verificação on-the-fly.
 *
 * @package Apollo\Event
 */

declare(strict_types=1);

namespace Apollo\Event;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Expiration {

	public function __construct() {
		// Registrar intervalo customizado do cron
		add_filter( 'cron_schedules', [ $this, 'add_cron_interval' ] );

		// Hook do cron para varredura
		add_action( 'apollo_event_check_expiration', [ $this, 'check_all_events' ] );

		// Hook quando evento é visualizado (verificação on-the-fly)
		add_action( 'the_post', [ $this, 'check_single_event' ] );

		// Hook personalizado para quando evento expira
		add_action( 'apollo_event_gone', [ $this, 'on_event_gone' ], 10, 1 );
	}

	/**
	 * Adiciona intervalo de 5 minutos ao cron
	 */
	public function add_cron_interval( array $schedules ): array {
		$schedules['every_five_minutes'] = [
			'interval' => 300,
			'display'  => __( 'A cada 5 minutos', 'apollo-events' ),
		];
		return $schedules;
	}

	/**
	 * Varredura de todos os eventos que podem ter expirado
	 *
	 * Busca apenas eventos publicados que ainda não estão marcados como gone.
	 */
	public function check_all_events(): void {
		// Verificar se expiração está ativa nas configurações
		if ( ! apollo_event_option( 'enable_expiration', true ) ) {
			return;
		}

		$now = current_time( 'Y-m-d H:i:s' );

		$events = get_posts( [
			'post_type'      => APOLLO_EVENT_CPT,
			'post_status'    => 'publish',
			'posts_per_page' => 100,
			'meta_query'     => [
				'relation' => 'AND',
				[
					'key'     => '_event_is_gone',
					'compare' => 'NOT EXISTS',
				],
				[
					'key'     => '_event_start_date',
					'value'   => '',
					'compare' => '!=',
				],
			],
			'fields'         => 'ids',
		] );

		foreach ( $events as $event_id ) {
			if ( apollo_event_is_gone( $event_id ) ) {
				/**
				 * Dispara quando evento acaba de expirar
				 *
				 * @param int $event_id ID do evento.
				 */
				do_action( 'apollo_event_gone', $event_id );
			}
		}
	}

	/**
	 * Verificação on-the-fly quando evento é exibido
	 *
	 * @param \WP_Post $post Post atual.
	 */
	public function check_single_event( \WP_Post $post ): void {
		if ( APOLLO_EVENT_CPT !== $post->post_type ) {
			return;
		}

		if ( apollo_event_is_gone( $post->ID ) ) {
			// Adiciona classe CSS ao body
			add_filter( 'body_class', function ( array $classes ) {
				$classes[] = 'apollo-event-gone';
				return $classes;
			} );

			add_filter( 'post_class', function ( array $classes ) {
				$classes[] = 'a-eve-gone';
				return $classes;
			} );
		}
	}

	/**
	 * Callback quando evento expira — integrações
	 *
	 * @param int $event_id ID do evento.
	 */
	public function on_event_gone( int $event_id ): void {
		// Notificação via apollo-notif (se ativo)
		if ( function_exists( 'apollo_send_notification' ) ) {
			$event = get_post( $event_id );
			if ( $event ) {
				apollo_send_notification( [
					'type'    => 'event_gone',
					'user_id' => (int) $event->post_author,
					'message' => sprintf(
						/* translators: %s: título do evento */
						__( 'Seu evento "%s" foi encerrado.', 'apollo-events' ),
						$event->post_title
					),
					'link'    => get_permalink( $event_id ),
				] );
			}
		}

		// Log via apollo-social (se ativo)
		if ( function_exists( 'apollo_log_activity' ) ) {
			$event = get_post( $event_id );
			if ( $event ) {
				apollo_log_activity( [
					'user_id'     => (int) $event->post_author,
					'component'   => 'events',
					'type'        => 'event_gone',
					'action_text' => sprintf( 'O evento "%s" foi encerrado', $event->post_title ),
					'item_id'     => $event_id,
					'primary_link' => get_permalink( $event_id ),
				] );
			}
		}
	}
}
