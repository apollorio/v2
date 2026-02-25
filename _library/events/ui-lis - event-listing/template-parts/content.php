<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Event_Listing
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> <?php event_listing_do_microdata('article'); ?>>
	<header class="entry-header">
		<?php
		if ( is_singular() ) :
			the_title( '<h1 class="entry-title">', '</h1>' );
		else :
			the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
		endif;

		if ( 'post' === get_post_type() ) :
			?>
			<div class="entry-meta">
				<?php
				event_listing_posted_on();
				event_listing_posted_by();
                event_listing_category_list();
                event_listing_comment_count();
				?>
			</div><!-- .entry-meta -->
		<?php endif; ?>
	</header><!-- .entry-header -->

	<?php event_listing_post_thumbnail(); ?>

	<div class="entry-content">
		<?php
        if(is_singular()) {
            the_content(sprintf(
                wp_kses(
                /* translators: %s: Name of current post. Only visible to screen readers */
                    __('Continue reading<span class="screen-reader-text"> "%s"</span>', 'event-listing'),
                    array(
                        'span' => array(
                            'class' => array(),
                        ),
                    )
                ),
                get_the_title()
            ));
        }else{
            the_excerpt();
        }

		wp_link_pages( array(
			'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'event-listing' ),
			'after'  => '</div>',
		) );
		?>
	</div><!-- .entry-content -->
    <?php
    if(is_singular()) {
        ?>
        <footer class="entry-footer">
            <?php event_listing_entry_footer(); ?>
        </footer><!-- .entry-footer -->
        <?php
    }else{
        ?>
        <div class="read-more-btn">
            <a href="<?php the_permalink(); ?>" class="btn"> <?php _e('Read More', 'event-listing' ); ?></a>
        </div>
    <?php
    }
    ?>
</article><!-- #post-<?php the_ID(); ?> -->
