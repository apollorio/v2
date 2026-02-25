<?php

/**
 * Shortcodes — Embeddable journal components
 *
 * [apollo_journal]         — News grid widget (count, category, columns)
 * [apollo_journal_marquee] — Horizontal scrolling ticker
 * [apollo_journal_card]    — Single article card embed
 *
 * @package Apollo\Journal
 */

declare(strict_types=1);

namespace Apollo\Journal;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Shortcode handler.
 */
class Shortcodes {



	/**
	 * Register all shortcodes.
	 *
	 * @return void
	 */
	public function init(): void {
		add_shortcode( 'apollo_journal', array( $this, 'render_grid' ) );
		add_shortcode( 'apollo_journal_marquee', array( $this, 'render_marquee' ) );
		add_shortcode( 'apollo_journal_card', array( $this, 'render_card' ) );
	}

	// ─────────────────────────────────────────────────────────────────────
	// [apollo_journal] — News Grid
	// ─────────────────────────────────────────────────────────────────────

	/**
	 * Render a news grid.
	 *
	 * Usage:
	 *   [apollo_journal]
	 *   [apollo_journal count="9" category="news" columns="3"]
	 *   [apollo_journal taxonomy="music" term="funk"]
	 *
	 * @param array|string $atts Shortcode attributes.
	 * @return string
	 */
	public function render_grid( $atts = array() ): string {
		$atts = shortcode_atts(
			array(
				'count'    => 6,
				'category' => '',
				'taxonomy' => '',
				'term'     => '',
				'columns'  => 3,
				'offset'   => 0,
				'orderby'  => 'date',
				'loadmore' => 'false',
			),
			$atts,
			'apollo_journal'
		);

		$args = array(
			'post_type'      => 'post',
			'posts_per_page' => absint( $atts['count'] ),
			'post_status'    => 'publish',
			'orderby'        => sanitize_key( $atts['orderby'] ),
			'order'          => 'DESC',
			'offset'         => absint( $atts['offset'] ),
		);

		if ( ! empty( $atts['category'] ) ) {
			$args['category_name'] = sanitize_text_field( $atts['category'] );
		}

		if ( ! empty( $atts['taxonomy'] ) && ! empty( $atts['term'] ) ) {
			$args['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				array(
					'taxonomy' => sanitize_key( $atts['taxonomy'] ),
					'field'    => 'slug',
					'terms'    => sanitize_text_field( $atts['term'] ),
				),
			);
		}

		$posts = get_posts( $args );

		if ( empty( $posts ) ) {
			return '<div class="aj-empty"><i class="ri-newspaper-line"></i><p>' .
				esc_html__( 'Nenhum artigo encontrado.', 'apollo-journal' ) . '</p></div>';
		}

		wp_enqueue_style( 'apollo-journal' );
		wp_enqueue_script( 'apollo-journal' );

		$cols     = absint( $atts['columns'] );
		$cols     = max( 1, min( 4, $cols ) );
		$loadmore = filter_var( $atts['loadmore'], FILTER_VALIDATE_BOOLEAN );

		ob_start();
		?>
		<section class="aj-news-section" data-lazy-section>
			<div class="aj-news-grid" style="--aj-cols:<?php echo $cols; ?>" <?php echo $loadmore ? 'data-aj-loadmore' : ''; ?>>
				<?php
				foreach ( $posts as $p ) :
					setup_postdata( $p );
					?>
					<?php
					echo $this->render_card_html( $p ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
					?>
					<?php
				endforeach;
				wp_reset_postdata();
				?>
			</div>
		</section>
		<?php
		return ob_get_clean();
	}

	// ─────────────────────────────────────────────────────────────────────
	// [apollo_journal_marquee] — Scrolling Ticker
	// ─────────────────────────────────────────────────────────────────────

	/**
	 * Render a horizontal scrolling news marquee/ticker.
	 *
	 * Usage:
	 *   [apollo_journal_marquee]
	 *   [apollo_journal_marquee count="10" speed="30" category="news"]
	 *
	 * @param array|string $atts Shortcode attributes.
	 * @return string
	 */
	public function render_marquee( $atts = array() ): string {
		$atts = shortcode_atts(
			array(
				'count'    => 8,
				'category' => '',
				'speed'    => 30,
				'pausable' => 'true',
			),
			$atts,
			'apollo_journal_marquee'
		);

		$args = array(
			'post_type'      => 'post',
			'posts_per_page' => absint( $atts['count'] ),
			'post_status'    => 'publish',
			'orderby'        => 'date',
			'order'          => 'DESC',
		);

		if ( ! empty( $atts['category'] ) ) {
			$args['category_name'] = sanitize_text_field( $atts['category'] );
		}

		$posts = get_posts( $args );

		if ( empty( $posts ) ) {
			return '';
		}

		wp_enqueue_style( 'apollo-journal' );

		$speed    = absint( $atts['speed'] );
		$pausable = filter_var( $atts['pausable'], FILTER_VALIDATE_BOOLEAN );
		$duration = max( 10, $speed );
		$uid      = 'aj-marquee-' . wp_unique_id();

		ob_start();
		?>
		<div class="aj-marquee <?php echo $pausable ? 'aj-marquee--pausable' : ''; ?>" id="<?php echo esc_attr( $uid ); ?>">
			<div class="aj-marquee__track" style="animation-duration:<?php echo $duration; ?>s">
				<?php
				foreach ( $posts as $p ) :
					$cats     = get_the_category( $p->ID );
					$cat_name = ! empty( $cats ) ? $cats[0]->name : 'News';
					$nrep     = get_post_meta( $p->ID, '_nrep_code', true );
					$badge    = $nrep ?: strtoupper( $cat_name );
					$badge_cl = $nrep ? 'aj-marquee__badge aj-marquee__badge--nrep' : 'aj-marquee__badge';
					?>
					<a href="<?php echo esc_url( get_permalink( $p ) ); ?>" class="aj-marquee__item">
						<span class="<?php echo esc_attr( $badge_cl ); ?>"><?php echo esc_html( $badge ); ?></span>
						<span class="aj-marquee__title"><?php echo esc_html( get_the_title( $p ) ); ?></span>
						<span class="aj-marquee__sep">|</span>
					</a>
					<?php
				endforeach;
				wp_reset_postdata();
				?>
				<?php
				// Duplicate for seamless loop
				?>
				<?php
				foreach ( $posts as $p ) :
					$cats     = get_the_category( $p->ID );
					$cat_name = ! empty( $cats ) ? $cats[0]->name : 'News';
					$nrep     = get_post_meta( $p->ID, '_nrep_code', true );
					$badge    = $nrep ?: strtoupper( $cat_name );
					$badge_cl = $nrep ? 'aj-marquee__badge aj-marquee__badge--nrep' : 'aj-marquee__badge';
					?>
					<a href="<?php echo esc_url( get_permalink( $p ) ); ?>" class="aj-marquee__item" aria-hidden="true">
						<span class="<?php echo esc_attr( $badge_cl ); ?>"><?php echo esc_html( $badge ); ?></span>
						<span class="aj-marquee__title"><?php echo esc_html( get_the_title( $p ) ); ?></span>
						<span class="aj-marquee__sep">|</span>
					</a>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	// ─────────────────────────────────────────────────────────────────────
	// [apollo_journal_card] — Single Article Card
	// ─────────────────────────────────────────────────────────────────────

	/**
	 * Render a single article card by ID or latest.
	 *
	 * Usage:
	 *   [apollo_journal_card id="123"]
	 *   [apollo_journal_card category="nota-de-repudio"]
	 *   [apollo_journal_card style="featured"]
	 *
	 * @param array|string $atts Shortcode attributes.
	 * @return string
	 */
	public function render_card( $atts = array() ): string {
		$atts = shortcode_atts(
			array(
				'id'       => 0,
				'category' => '',
				'style'    => 'card',
			),
			$atts,
			'apollo_journal_card'
		);

		$post = null;

		if ( absint( $atts['id'] ) > 0 ) {
			$post = get_post( absint( $atts['id'] ) );
		} else {
			$args = array(
				'post_type'      => 'post',
				'posts_per_page' => 1,
				'post_status'    => 'publish',
				'orderby'        => 'date',
				'order'          => 'DESC',
			);

			if ( ! empty( $atts['category'] ) ) {
				$args['category_name'] = sanitize_text_field( $atts['category'] );
			}

			$found = get_posts( $args );
			$post  = $found[0] ?? null;
		}

		if ( ! $post ) {
			return '';
		}

		wp_enqueue_style( 'apollo-journal' );

		$style = sanitize_key( $atts['style'] );

		if ( 'featured' === $style ) {
			return $this->render_featured_html( $post );
		}

		return $this->render_card_html( $post );
	}

	// ─────────────────────────────────────────────────────────────────────
	// PRIVATE RENDERERS
	// ─────────────────────────────────────────────────────────────────────

	/**
	 * Render a standard card HTML block.
	 *
	 * @param \WP_Post $post Post object.
	 * @return string
	 */
	private function render_card_html( \WP_Post $post ): string {
		$cats     = get_the_category( $post->ID );
		$cat_name = ! empty( $cats ) ? $cats[0]->name : 'News';
		$nrep     = get_post_meta( $post->ID, '_nrep_code', true );
		$badge    = $nrep ?: strtoupper( $cat_name );
		$badge_cl = $nrep ? 'aj-ng-badge aj-ng-badge--nrep' : 'aj-ng-badge';

		$time_ago_dt = get_post_time( 'Y-m-d H:i:s', false, $post );

		$author = get_the_author_meta( 'display_name', $post->post_author );

		ob_start();
		?>
		<a href="<?php echo esc_url( get_permalink( $post ) ); ?>" class="aj-ng-item">
			<?php if ( has_post_thumbnail( $post ) ) : ?>
				<img class="aj-ng-item__img"
					src="<?php echo esc_url( get_the_post_thumbnail_url( $post, 'medium' ) ); ?>"
					alt="<?php echo esc_attr( get_the_title( $post ) ); ?>"
					loading="lazy">
			<?php endif; ?>
			<div class="aj-ng-item__body">
				<div class="aj-ng-item__top">
					<span class="<?php echo esc_attr( $badge_cl ); ?>"><?php echo esc_html( $badge ); ?></span>
					<span class="aj-ng-item__time"><?php echo wp_kses_post( apollo_time_ago_html( $time_ago_dt ) ); ?></span>
				</div>
				<div class="aj-ng-item__title"><?php echo esc_html( get_the_title( $post ) ); ?></div>
				<div class="aj-ng-item__author"><?php echo esc_html( $author ); ?></div>
			</div>
		</a>
		<?php
		return ob_get_clean();
	}

	/**
	 * Render a featured (large) card HTML block.
	 *
	 * @param \WP_Post $post Post object.
	 * @return string
	 */
	private function render_featured_html( \WP_Post $post ): string {
		$cats     = get_the_category( $post->ID );
		$cat_name = ! empty( $cats ) ? $cats[0]->name : 'Journal';
		$nrep     = get_post_meta( $post->ID, '_nrep_code', true );
		$badge    = $nrep ?: strtoupper( $cat_name );
		$badge_cl = $nrep ? 'aj-featured__badge aj-card__badge--nrep' : 'aj-featured__badge';

		$time_ago_dt = get_post_time( 'Y-m-d H:i:s', false, $post );

		ob_start();
		?>
		<a href="<?php echo esc_url( get_permalink( $post ) ); ?>" class="aj-featured">
			<?php if ( has_post_thumbnail( $post ) ) : ?>
				<img class="aj-featured__img"
					src="<?php echo esc_url( get_the_post_thumbnail_url( $post, 'large' ) ); ?>"
					alt="<?php echo esc_attr( get_the_title( $post ) ); ?>"
					loading="lazy">
			<?php endif; ?>
			<div class="aj-featured__body">
				<span class="<?php echo esc_attr( $badge_cl ); ?>"><?php echo esc_html( $badge ); ?></span>
				<h2 class="aj-featured__title"><?php echo esc_html( get_the_title( $post ) ); ?></h2>
				<div class="aj-featured__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt( $post ), 30 ) ); ?></div>
				<div class="aj-featured__footer">
					<span><?php echo esc_html( get_the_author_meta( 'display_name', $post->post_author ) ); ?></span>
					<span>&middot;</span>
					<span><?php echo wp_kses_post( apollo_time_ago_html( $time_ago_dt ) ); ?></span>
				</div>
			</div>
		</a>
		<?php
		return ob_get_clean();
	}
}
