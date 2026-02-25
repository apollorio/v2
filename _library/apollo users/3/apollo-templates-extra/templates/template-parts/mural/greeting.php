<?php
/**
 * Mural: Greeting
 *
 * Personalized hello + location + next event alert.
 *
 * @package Apollo\Templates
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Variables from page-mural.php:
// $first_name, $user_location, $next_event, $next_event_days

// Time-based greeting.
$hour = (int) current_time( 'G' );
if ( $hour < 6 ) {
	$greeting = 'Boa madrugada';
} elseif ( $hour < 12 ) {
	$greeting = 'Bom dia';
} elseif ( $hour < 18 ) {
	$greeting = 'Boa tarde';
} else {
	$greeting = 'Boa noite';
}
?>

<header class="mural-greeting">
	<h1 class="greet-title"><?php echo esc_html( $greeting ); ?>, <?php echo esc_html( $first_name ); ?>!</h1>
	<div class="greet-temp">
		<i class="ri-map-pin-2-fill"></i>
		<?php echo esc_html( $user_location ); ?>
	</div>

	<?php if ( $next_event ) :
		$ev_venue = get_post_meta( $next_event->ID, '_apollo_event_venue', true ) ?: '';
		$days_text = $next_event_days === 0
			? 'hoje'
			: ( $next_event_days === 1 ? 'amanhã' : "em {$next_event_days} dias" );
	?>
	<div class="greet-alert">
		Não perca <strong><?php echo esc_html( $next_event->post_title ); ?></strong>
		<?php echo esc_html( $days_text ); ?><?php if ( $ev_venue ) : ?>
			no <strong><?php echo esc_html( $ev_venue ); ?></strong><?php endif; ?>.
	</div>
	<?php endif; ?>
</header>
