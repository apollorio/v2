<?php

/**
 * Template Part: Accommodation Card
 *
 * @package Apollo\Classifieds
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get classified meta
$author_id = get_the_author_meta( 'ID' );
$title     = get_the_title();
$location  = get_post_meta( get_the_ID(), '_location', true );
$price     = get_post_meta( get_the_ID(), '_price', true );
$rating    = get_post_meta( get_the_ID(), '_rating', true ) ?: '4.5';
$badge     = get_post_meta( get_the_ID(), '_badge', true );
$image     = get_the_post_thumbnail_url( get_the_ID(), 'medium' ) ?: 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=600';
?>

<div class="accom-card reveal-up" data-classified-id="<?php echo esc_attr( get_the_ID() ); ?>">
	<div class="accom-img-wrap">
		<?php if ( $badge ) : ?>
			<span class="accom-badge"><?php echo esc_html( $badge ); ?></span>
		<?php endif; ?>
		<img src="<?php echo esc_url( $image ); ?>" class="accom-img" alt="<?php echo esc_attr( $title ); ?>">
	</div>
	<div class="accom-content">
		<div class="accom-header">
			<h3 class="accom-title"><?php echo esc_html( $title ); ?></h3>
			<div class="accom-rating">
				<i class="ri-star-fill"></i> <?php echo esc_html( $rating ); ?>
			</div>
		</div>
		<?php if ( $location ) : ?>
			<div class="accom-loc">
				<i class="ri-map-pin-line"></i> <?php echo esc_html( $location ); ?>
			</div>
		<?php endif; ?>
		<div class="accom-footer">
			<?php if ( $price ) : ?>
				<div class="accom-price">R$ <?php echo esc_html( number_format( $price, 0, ',', '.' ) ); ?><span>/noite</span></div>
			<?php endif; ?>
			<button class="btn-accom btn-open-modal" data-user-id="<?php echo esc_attr( $author_id ); ?>" data-classified-id="<?php echo esc_attr( get_the_ID() ); ?>">Ver</button>
		</div>
	</div>
</div>
