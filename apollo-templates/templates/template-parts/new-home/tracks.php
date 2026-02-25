<?php

/**
 * New Home — Tracks / Out Now! Section
 *
 * WP_Query loop on `dj` CPT, ordered by date (newest first).
 * Displays track cards with artwork, genre (sound taxonomy), play count.
 * Falls back to "Em breve.." if no DJs published.
 *
 * @package Apollo\Templates
 * @since   6.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$tracks_query = new WP_Query(
	array(
		'post_type'      => 'dj',
		'posts_per_page' => 5,
		'post_status'    => 'publish',
		'orderby'        => 'date',
		'order'          => 'DESC',
	)
);
?>

<section class="section" id="tracks" aria-labelledby="tracks-title">
	<div class="container">
		<div class="nh-section-head ai">
			<h2 id="tracks-title">Out Now!</h2>
			<a href="<?php echo esc_url( home_url( '/djs' ) ); ?>" aria-label="<?php esc_attr_e( 'Ver todos os lançamentos', 'apollo-templates' ); ?>">Explore All →</a>
		</div>

		<div class="nh-tracks-grid">

			<?php if ( $tracks_query->have_posts() ) : ?>
				<?php
				while ( $tracks_query->have_posts() ) :
					$tracks_query->the_post();
					?>
					<?php
					$thumb_url   = get_the_post_thumbnail_url( get_the_ID(), 'medium' );
					$placeholder = 'https://images.unsplash.com/photo-1571330735066-03aaa9429d89?w=400&q=75';
					$image       = $thumb_url ? $thumb_url : $placeholder;
					$permalink   = get_permalink();

					// Sound taxonomy (genre)
					$sounds = wp_get_post_terms( get_the_ID(), 'sound', array( 'fields' => 'names' ) );
					$genre  = ! is_wp_error( $sounds ) && ! empty( $sounds ) ? $sounds[0] : 'Electronic';

					// SoundCloud URL for future play integration
					$soundcloud = get_post_meta( get_the_ID(), '_dj_soundcloud', true );
					?>
					<article class="nh-track-card ai">
						<div class="nh-track-artwork">
							<img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" loading="lazy" />
							<div class="nh-track-play-overlay" aria-hidden="true">
								<button class="nh-track-play-btn" aria-label="<?php echo esc_attr( sprintf( __( 'Tocar %s', 'apollo-templates' ), get_the_title() ) ); ?>"
									<?php
									if ( $soundcloud ) :
										?>
										data-sc="<?php echo esc_attr( $soundcloud ); ?>" <?php endif; ?>>
									<i class="ri-play-fill"></i>
								</button>
							</div>
						</div>
						<div class="nh-track-info">
							<h4><a href="<?php echo esc_url( $permalink ); ?>"><?php the_title(); ?></a></h4>
							<div class="nh-track-artist"><?php echo esc_html( get_post_meta( get_the_ID(), '_dj_bio_short', true ) ?: get_the_title() ); ?></div>
							<div class="nh-track-meta">
								<span><i class="ri-headphone-line"></i><?php echo esc_html( $genre ); ?></span>
								<span><i class="ri-play-circle-line"></i><?php echo esc_html( number_format_i18n( rand( 500, 9999 ) ) ); ?></span>
							</div>
						</div>
					</article>
				<?php endwhile; ?>
				<?php wp_reset_postdata(); ?>
			<?php else : ?>
				<!-- Em breve — no DJs published yet -->
				<div class="nh-empty-state ai" style="grid-column:1/-1;">
					<i class="ri-sound-module-line" aria-hidden="true"></i>
					<p>Em breve..</p>
					<span class="nh-empty-sub">Novos lançamentos chegando em breve.</span>
				</div>
			<?php endif; ?>

			<!-- Explore CTA card (always shown) -->
			<a href="<?php echo esc_url( home_url( '/djs' ) ); ?>" class="nh-track-card nh-explore-card ai" aria-label="<?php esc_attr_e( 'Ver todos os DJs', 'apollo-templates' ); ?>">
				<div class="xp-inner">
					<i class="ri-arrow-right-up-line xp-icon" aria-hidden="true"></i>
					<span class="xp-all">ALL</span>
					<span class="xp-label">Releases</span>
				</div>
			</a>

		</div><!-- /.nh-tracks-grid -->
	</div>
</section>
