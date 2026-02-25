<?php
/**
 * Mural: News Ticker (Airport style)
 *
 * @package Apollo\Templates
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Variable: $ticker_items (array of strings from page-mural.php)
if ( empty( $ticker_items ) ) return;
?>

<div class="news-ticker-bar">
	<div class="news-track">
		<?php foreach ( $ticker_items as $item ) : ?>
			<span class="news-item"><?php echo esc_html( $item ); ?></span>
		<?php endforeach; ?>
		<?php // Duplicate for seamless loop ?>
		<?php foreach ( $ticker_items as $item ) : ?>
			<span class="news-item"><?php echo esc_html( $item ); ?></span>
		<?php endforeach; ?>
	</div>
</div>
