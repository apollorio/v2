<?php
/**
 * Template part for displaying results in search pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Event_Listing
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> <?php event_listing_do_microdata('article'); ?>>
	<header class="entry-header">
		<?php the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>

		<?php if ( 'post' === get_post_type() ) : ?>
		<div class="entry-meta">
			<?php
			event_listing_posted_on();
			event_listing_posted_by();
			?>
		</div><!-- .entry-meta -->
		<?php endif; ?>
	</header><!-- .entry-header -->

	<?php event_listing_post_thumbnail(); ?>

	<div class="entry-summary">
		<?php the_excerpt(); ?>
	</div><!-- .entry-summary -->
    <div class="read-more-btn">
        <a href="<?php the_permalink(); ?>" class="btn"> <?php _e('Read More', 'event-listing' ); ?></a>
    </div>
</article><!-- #post-<?php the_ID(); ?> -->
