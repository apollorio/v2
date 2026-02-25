<?php

/**
 * Template Part: Ticket Card (Repasse)
 *
 * @package Apollo\Classifieds
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get classified meta
$author_id       = get_the_author_meta( 'ID' );
$author_name     = get_the_author_meta( 'display_name' );
$author_username = get_the_author_meta( 'user_login' );
$author_avatar   = get_avatar_url( $author_id, array( 'size' => 100 ) );

$event_title    = get_post_meta( get_the_ID(), '_event_title', true ) ?: get_the_title();
$event_date     = get_post_meta( get_the_ID(), '_event_date', true );
$event_location = get_post_meta( get_the_ID(), '_event_location', true );
$ticket_price   = get_post_meta( get_the_ID(), '_price', true );
$ticket_image   = get_the_post_thumbnail_url( get_the_ID(), 'medium' ) ?: 'https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?w=600';
?>

<article type="ticket" class="reveal-up" data-classified-id="<?php echo esc_attr( get_the_ID() ); ?>">
	<div class="top">
		<div class="ticket-user-info">
			<img src="<?php echo esc_url( $author_avatar ); ?>" class="ticket-avatar" alt="<?php echo esc_attr( $author_name ); ?>">
			<div class="ticket-user-meta">
				<span class="ticket-bandname"><?php echo esc_html( $author_name ); ?></span>
				<span class="ticket-tourname">@<?php echo esc_html( $author_username ); ?></span>
			</div>
		</div>
		<img src="<?php echo esc_url( $ticket_image ); ?>" class="ticket-img" alt="<?php echo esc_attr( $event_title ); ?>">
		<div class="ticket-deetz">
			<div class="ticket-meta-row">
				<div class="ticket-event-title"><?php echo esc_html( $event_title ); ?></div>
				<?php if ( $event_date ) : ?>
					<div class="ticket-date"><?php echo esc_html( $event_date ); ?></div>
				<?php endif; ?>
				<?php if ( $event_location ) : ?>
					<div class="ticket-location"><?php echo esc_html( $event_location ); ?></div>
				<?php endif; ?>
			</div>
			<?php if ( $ticket_price ) : ?>
				<div class="ticket-price-tag">R$ <?php echo esc_html( number_format( $ticket_price, 0, ',', '.' ) ); ?></div>
			<?php endif; ?>
		</div>
	</div>
	<div class="rip"></div>
	<div class="bottom">
		<div class="barcode"></div>
		<button class="btn-chat-ticket btn-open-modal" data-user-id="<?php echo esc_attr( $author_id ); ?>" data-classified-id="<?php echo esc_attr( get_the_ID() ); ?>">
			<i class="ri-message-3-line"></i>
		</button>
	</div>
</article>
