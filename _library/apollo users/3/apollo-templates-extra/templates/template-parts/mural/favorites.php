<?php
/**
 * Mural: My Favorites
 *
 * Grid of user's favorited events.
 *
 * @package Apollo\Templates
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Variable: $fav_events (array of WP_Post)
if ( empty( $fav_events ) ) return;
?>

<section class="section-favorites">
	<div class="section-header">
		<h3 class="section-title"><i class="ri-heart-3-fill" style="color:var(--primary);"></i> My Favorites</h3>
		<a href="<?php echo esc_url( home_url( '/favoritos/' ) ); ?>" class="section-link">View All</a>
	</div>
	<div class="cards-grid">
		<?php foreach ( $fav_events as $event ) :
			$thumb    = get_the_post_thumbnail_url( $event, 'medium_large' );
			$ev_date  = get_post_meta( $event->ID, '_apollo_event_date', true );
			$ev_venue = get_post_meta( $event->ID, '_apollo_event_venue', true );
			$formatted_date = $ev_date ? wp_date( 'D, M d', strtotime( $ev_date ) ) : '';
		?>
		<article class="event-card">
			<?php if ( $thumb ) : ?>
				<img src="<?php echo esc_url( $thumb ); ?>" class="ec-img" alt="<?php echo esc_attr( $event->post_title ); ?>">
			<?php else : ?>
				<div class="ec-img ec-img--placeholder"><i class="ri-music-2-fill"></i></div>
			<?php endif; ?>
			<div class="ec-body">
				<?php if ( $formatted_date ) : ?>
					<span class="ec-date"><?php echo esc_html( $formatted_date ); ?></span>
				<?php endif; ?>
				<h4 class="ec-title"><?php echo esc_html( $event->post_title ); ?></h4>
				<?php if ( $ev_venue ) : ?>
					<div class="ec-loc"><i class="ri-map-pin-line"></i> <?php echo esc_html( $ev_venue ); ?></div>
				<?php endif; ?>
			</div>
		</article>
		<?php endforeach; ?>
	</div>
</section>
