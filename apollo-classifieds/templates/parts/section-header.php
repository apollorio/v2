<?php

/**
 * Template Part: Section Header
 *
 * @package Apollo\Classifieds
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$icon  = $args['icon'] ?? 'ri-grid-fill';
$title = $args['title'] ?? 'Section';
$count = $args['count'] ?? '';
?>

<div class="section-header reveal-up">
	<h2 class="section-title">
		<i class="<?php echo esc_attr( $icon ); ?>"></i>
		<?php echo esc_html( $title ); ?>
	</h2>
	<?php if ( $count ) : ?>
		<span class="section-count"><?php echo esc_html( $count ); ?></span>
	<?php endif; ?>
</div>
