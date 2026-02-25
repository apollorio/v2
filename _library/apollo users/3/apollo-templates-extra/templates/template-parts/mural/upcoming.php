<?php
/**
 * Mural: All Upcoming Events
 *
 * @package Apollo\Templates
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Variable: $upcoming_events (array of WP_Post)
if ( empty( $upcoming_events ) ) return;
?>

<section class="section-upcoming">
	<div class="section-header">
		<h3 class="section-title"><i class="ri-calendar-event-fill"></i> All Upcoming Events</h3>
		<a href="<?php echo esc_url( home_url( '/eventos/' ) ); ?>" class="section-link">Full Calendar</a>
	</div>
	<div class="cards-grid">
		<?php foreach ( $upcoming_events as $event ) :
			$thumb    = get_the_post_thumbnail_url( $event, 'medium_large' );
			$ev_date  = get_post_meta( $event->ID, '_apollo_event_date', true );
			$ev_time  = get_post_meta( $event->ID, '_apollo_event_time', true );
			$ev_venue = get_post_meta( $event->ID, '_apollo_event_venue', true );

			// Smart date formatting.
			$today    = current_time( 'Y-m-d' );
			$tomorrow = wp_date( 'Y-m-d', strtotime( '+1 day' ) );
			if ( $ev_date === $today ) {
				$date_display = 'Tonight' . ( $ev_time ? ' • ' . esc_html( $ev_time ) : '' );
			} elseif ( $ev_date === $tomorrow ) {
				$date_display = 'Tomorrow' . ( $ev_time ? ' • ' . esc_html( $ev_time ) : '' );
			} else {
				$date_display = $ev_date ? wp_date( 'D, M d', strtotime( $ev_date ) ) : '';
				if ( $ev_time ) {
					$date_display .= ' • ' . esc_html( $ev_time );
				}
			}
		?>
		<article class="event-card reveal-up">
			<?php if ( $thumb ) : ?>
				<img src="<?php echo esc_url( $thumb ); ?>" class="ec-img" alt="<?php echo esc_attr( $event->post_title ); ?>" loading="lazy">
			<?php else : ?>
				<div class="ec-img ec-img--placeholder"><i class="ri-music-2-fill"></i></div>
			<?php endif; ?>
			<div class="ec-body">
				<?php if ( $date_display ) : ?>
					<span class="ec-date"><?php echo $date_display; ?></span>
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
