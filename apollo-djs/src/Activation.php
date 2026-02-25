<?php
/**
 * Ativação — apollo-djs
 *
 * @package Apollo\DJs
 */

declare(strict_types=1);

namespace Apollo\DJs;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Activation {

	public static function activate(): void {
		self::set_defaults();
		self::create_pages();
	}

	private static function set_defaults(): void {
		$defaults = array(
			'default_style' => 'apollo-v1',
			'per_page'      => 12,
			'default_view'  => 'grid',
		);

		if ( ! get_option( 'apollo_dj_settings' ) ) {
			update_option( 'apollo_dj_settings', $defaults );
		}
	}

	/**
	 * Cria páginas conforme registry: /portal (DJ portal page)
	 */
	private static function create_pages(): void {
		// A page for /djs is handled by CPT archive
		// /portal is the DJ portal/discovery page
		$pages = array(
			array(
				'slug'    => 'portal',
				'title'   => 'Portal DJ',
				'content' => '[apollo_djs featured="1" limit="12"]',
			),
		);

		foreach ( $pages as $page_data ) {
			$existing = get_page_by_path( $page_data['slug'] );
			if ( $existing ) {
				continue;
			}

			wp_insert_post(
				array(
					'post_title'   => $page_data['title'],
					'post_name'    => $page_data['slug'],
					'post_content' => $page_data['content'],
					'post_status'  => 'publish',
					'post_type'    => 'page',
					'post_author'  => get_current_user_id() ?: 1,
					'meta_input'   => array(
						'_apollo_virtual_page' => true,
					),
				)
			);
		}
	}
}
