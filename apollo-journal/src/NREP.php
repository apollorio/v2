<?php

/**
 * NREP — Nota de Repúdio Auto-Coding System
 *
 * Hooks into transition_post_status to auto-generate sequential
 * NREP codes for posts in the "nota-de-repudio" category.
 *
 * Format: NREP.YYYY-NNN (e.g., NREP.2026-001)
 *
 * @package Apollo\Journal
 */

declare(strict_types=1);

namespace Apollo\Journal;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * NREP handler.
 */
class NREP {


	/**
	 * Wire hooks.
	 *
	 * @return void
	 */
	public function init(): void {
		add_action( 'transition_post_status', array( $this, 'maybe_assign_code' ), 10, 3 );
	}

	/**
	 * Assign NREP code when a post is published for the first time
	 * and belongs to the "nota-de-repudio" category.
	 *
	 * @param string   $new_status New post status.
	 * @param string   $old_status Previous post status.
	 * @param \WP_Post $post       Post object.
	 * @return void
	 */
	public function maybe_assign_code( string $new_status, string $old_status, \WP_Post $post ): void {
		// Only on first publish of a regular post.
		if ( 'publish' !== $new_status || 'publish' === $old_status ) {
			return;
		}

		if ( 'post' !== $post->post_type ) {
			return;
		}

		// Must be in the "nota-de-repudio" category.
		if ( ! has_term( 'nota-de-repudio', 'category', $post ) ) {
			return;
		}

		// Skip if already coded.
		if ( get_post_meta( $post->ID, '_nrep_code', true ) ) {
			return;
		}

		$code = $this->generate_code( $post->ID );

		if ( ! $code ) {
			return;
		}

		// Store meta.
		update_post_meta( $post->ID, '_nrep_code', $code['code'] );
		update_post_meta( $post->ID, '_nrep_year', $code['year'] );
		update_post_meta( $post->ID, '_nrep_seq', $code['seq'] );

		/**
		 * Fires after a NREP code is assigned to a post.
		 *
		 * @since 1.0.0
		 * @param int    $post_id Post ID.
		 * @param string $code    The assigned NREP code string.
		 * @param array  $meta    Full code array (code, year, seq).
		 */
		do_action( 'apollo/journal/nrep_assigned', $post->ID, $code['code'], $code );

		// Prepend code to the title (avoid infinite loop).
		$current_title = get_the_title( $post->ID );

		if ( str_starts_with( $current_title, 'NREP.' ) ) {
			return;
		}

		remove_action( 'transition_post_status', array( $this, 'maybe_assign_code' ), 10 );

		wp_update_post(
			array(
				'ID'         => $post->ID,
				'post_title' => $code['code'] . ' — ' . $current_title,
			)
		);

		add_action( 'transition_post_status', array( $this, 'maybe_assign_code' ), 10, 3 );
	}

	/**
	 * Generate the next sequential NREP code for the current year.
	 *
	 * Uses get_posts with meta_query + orderby meta_value_num
	 * for clean, performant retrieval.
	 *
	 * @param int $exclude_id Post ID to exclude from query.
	 * @return array{code: string, year: string, seq: int}|null
	 */
	private function generate_code( int $exclude_id ): ?array {
		$year = gmdate( 'Y' );

		$last = get_posts(
			array(
				'posts_per_page' => 1,
				'post_type'      => 'post',
				'post_status'    => 'publish',
				'orderby'        => 'meta_value_num',
				'meta_key'       => '_nrep_seq',
				'order'          => 'DESC',
				'tax_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
					array(
						'taxonomy' => 'category',
						'field'    => 'slug',
						'terms'    => 'nota-de-repudio',
					),
				),
				'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
					array(
						'key'   => '_nrep_year',
						'value' => $year,
					),
				),
				'post__not_in'   => array( $exclude_id ),
				'no_found_rows'  => true,
				'fields'         => 'ids',
			)
		);

		$max  = $last ? (int) get_post_meta( $last[0], '_nrep_seq', true ) : 0;
		$next = $max + 1;

		$prefix = get_option( 'aj_nrep_prefix', APOLLO_JOURNAL_NREP_PREFIX );
		$padded = str_pad( (string) $next, 3, '0', STR_PAD_LEFT );
		$code   = $prefix . $year . '-' . $padded;

		return array(
			'code' => $code,
			'year' => $year,
			'seq'  => $next,
		);
	}
}
