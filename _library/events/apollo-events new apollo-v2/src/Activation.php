<?php
/**
 * Ativação do plugin
 *
 * @package Apollo\Event
 */

declare(strict_types=1);

namespace Apollo\Event;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Activation {

	/**
	 * Executa na ativação do plugin
	 */
	public static function activate(): void {
		self::set_defaults();
		self::create_pages();

		// Agenda cron de expiração
		if ( ! wp_next_scheduled( 'apollo_event_check_expiration' ) ) {
			wp_schedule_event( time(), 'every_five_minutes', 'apollo_event_check_expiration' );
		}
	}

	/**
	 * Cria páginas virtuais conforme apollo-registry.json
	 * - criar-evento → [apollo_event_form]
	 */
	private static function create_pages(): void {
		$pages = [
			[
				'slug'    => 'criar-evento',
				'title'   => 'Criar Evento',
				'content' => '[apollo_event_form]',
			],
		];

		foreach ( $pages as $page_data ) {
			$existing = get_page_by_path( $page_data['slug'] );

			if ( $existing ) {
				continue;
			}

			wp_insert_post( [
				'post_title'   => $page_data['title'],
				'post_name'    => $page_data['slug'],
				'post_content' => $page_data['content'],
				'post_status'  => 'publish',
				'post_type'    => 'page',
				'post_author'  => get_current_user_id() ?: 1,
				'meta_input'   => [
					'_apollo_virtual_page' => true,
					'_apollo_require_auth' => true,
				],
			] );
		}
	}

	/**
	 * Define opções padrão
	 */
	private static function set_defaults(): void {
		$defaults = [
			'default_style'      => 'base',
			'enable_osm_map'     => true,
			'enable_expiration'  => true,
			'show_gone_events'   => true,
			'styles_enabled'     => [ 'base', 'apollo-v1', 'ui-thim', 'ui-lis' ],
			'gone_offset_minutes' => 30,
		];

		if ( ! get_option( 'apollo_event_settings' ) ) {
			update_option( 'apollo_event_settings', $defaults );
		}
	}
}
