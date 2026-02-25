<?php
/**
 * Template Name: Apollo Mural
 * Template Post Type: page
 *
 * Logged-in user dashboard (Mural).
 * Loaded instead of page-home.php when is_user_logged_in() === true.
 *
 * @package Apollo\Templates
 * @since   1.1.0
 */

defined( 'ABSPATH' ) || exit;

// Require login — redirect to home if guest.
if ( ! is_user_logged_in() ) {
	wp_redirect( home_url( '/' ) );
	exit;
}

$current_user = wp_get_current_user();
$user_id      = $current_user->ID;

// Display name (prefer Apollo social name).
$social_name  = get_user_meta( $user_id, '_apollo_social_name', true );
$display_name = $social_name ?: $current_user->display_name;
$first_name   = explode( ' ', $display_name )[0];

// Location.
$user_location = get_user_meta( $user_id, 'user_location', true ) ?: 'Rio de Janeiro, Brazil';

// Sound preferences.
$sound_prefs = get_user_meta( $user_id, '_apollo_sound_preferences', true );
$sound_tags  = [];
if ( ! empty( $sound_prefs ) && is_array( $sound_prefs ) ) {
	foreach ( $sound_prefs as $term_id ) {
		$term = get_term( (int) $term_id );
		if ( $term && ! is_wp_error( $term ) ) {
			$sound_tags[] = $term->name;
		}
	}
}

// Favorited events (stored in user meta as array of post IDs).
$fav_ids    = get_user_meta( $user_id, '_apollo_favorite_events', true );
$fav_events = [];
if ( ! empty( $fav_ids ) && is_array( $fav_ids ) ) {
	$fav_events = get_posts( [
		'post_type'      => 'apollo_event',
		'post__in'       => array_map( 'intval', $fav_ids ),
		'posts_per_page' => 6,
		'post_status'    => 'publish',
		'orderby'        => 'meta_value',
		'meta_key'       => '_apollo_event_date',
		'order'          => 'ASC',
	] );
}

// Upcoming events (all, next 30 days).
$upcoming_events = get_posts( [
	'post_type'      => 'apollo_event',
	'posts_per_page' => 8,
	'post_status'    => 'publish',
	'meta_key'       => '_apollo_event_date',
	'orderby'        => 'meta_value',
	'order'          => 'ASC',
	'meta_query'     => [
		[
			'key'     => '_apollo_event_date',
			'value'   => current_time( 'Y-m-d' ),
			'compare' => '>=',
			'type'    => 'DATE',
		],
	],
] );

// Classifieds.
$classifieds_hosting = get_posts( [
	'post_type'      => 'apollo_classified',
	'posts_per_page' => 3,
	'post_status'    => 'publish',
	'tax_query'      => [ [
		'taxonomy' => 'classified_type',
		'field'    => 'slug',
		'terms'    => [ 'hosting', 'renting', 'accommodation' ],
	] ],
] );

$classifieds_tickets = get_posts( [
	'post_type'      => 'apollo_classified',
	'posts_per_page' => 3,
	'post_status'    => 'publish',
	'tax_query'      => [ [
		'taxonomy' => 'classified_type',
		'field'    => 'slug',
		'terms'    => [ 'ticket', 'tickets', 'ingresso' ],
	] ],
] );

// Next event alert (closest favorited event).
$next_event      = null;
$next_event_days = null;
if ( ! empty( $fav_events ) ) {
	foreach ( $fav_events as $ev ) {
		$ev_date = get_post_meta( $ev->ID, '_apollo_event_date', true );
		if ( $ev_date ) {
			$diff = ( strtotime( $ev_date ) - current_time( 'timestamp' ) ) / DAY_IN_SECONDS;
			if ( $diff >= 0 ) {
				$next_event      = $ev;
				$next_event_days = (int) ceil( $diff );
				break;
			}
		}
	}
}

// News ticker items (could be dynamic later).
$ticker_items = apply_filters( 'apollo_mural_ticker_items', [
	'GATE 4: FLIGHT TO BERLIN BOARDING NOW — TECHNO EXPRESS',
	'WEATHER ALERT: HEATWAVE EXPECTED FOR WEEKEND RAVES',
	'NEW VENUE OPENING IN LAPA: "THE BUNKER"',
	'APOLLO COMMUNITY GUIDELINES UPDATED',
	'VINYL MARKET THIS SUNDAY AT GLÓRIA',
] );

// Asset URLs.
$css_url = plugins_url( 'assets/css/mural.css', APOLLO_TEMPLATES_FILE );
$js_url  = plugins_url( 'assets/js/mural.js', APOLLO_TEMPLATES_FILE );
$ver     = defined( 'WP_DEBUG' ) && WP_DEBUG ? time() : APOLLO_TEMPLATES_VERSION;

// Template parts directory.
$parts_dir = APOLLO_TEMPLATES_DIR . 'templates/template-parts/mural/';
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php echo esc_html( get_bloginfo( 'name' ) ); ?> — Mural</title>

	<!-- Apollo CDN -->
	<script src="https://cdn.apollo.rio.br/v1.0.0/core.js" fetchpriority="high"></script>

	<!-- Fonts -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Shrikhand&family=Space+Grotesk:wght@300..700&family=Space+Mono:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">

	<!-- Icons -->
	<link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

	<!-- Mural CSS -->
	<link rel="stylesheet" href="<?php echo esc_url( $css_url ); ?>?v=<?php echo esc_attr( $ver ); ?>">

	<?php wp_head(); ?>
</head>
<body <?php body_class( 'apollo-mural' ); ?>>
<?php
global $wp_admin_bar;
if ( ! is_object( $wp_admin_bar ) ) {
	remove_action( 'wp_body_open', 'wp_admin_bar_render', 0 );
	remove_action( 'wp_body_open', 'wp_admin_bar_render', 10 );
	remove_action( 'wp_body_open', 'wp_admin_bar_render' );
}
wp_body_open();
?>

<main id="main-content" class="mural-content">

	<?php
	// ═══ 1. WEATHER HERO (video on top) ═══
	include $parts_dir . 'weather-hero.php';
	?>

	<div class="app-container">

		<?php
		// ═══ 2. GREETING ═══
		include $parts_dir . 'greeting.php';
		?>

	</div>

	<?php
	// ═══ 3. NEWS TICKER ═══
	include $parts_dir . 'ticker.php';
	?>

	<div class="app-container">

		<?php
		// ═══ 4. MY SOUNDS ═══
		if ( ! empty( $sound_tags ) ) {
			include $parts_dir . 'sounds.php';
		}

		// ═══ 5. FAVORITED EVENTS ═══
		if ( ! empty( $fav_events ) ) {
			include $parts_dir . 'favorites.php';
		}

		// ═══ 6. ALL UPCOMING ═══
		if ( ! empty( $upcoming_events ) ) {
			include $parts_dir . 'upcoming.php';
		}

		// ═══ 7. CLASSIFIEDS ═══
		if ( ! empty( $classifieds_hosting ) || ! empty( $classifieds_tickets ) ) {
			include $parts_dir . 'classifieds.php';
		}
		?>

	</div>

</main>

<script src="<?php echo esc_url( $js_url ); ?>?v=<?php echo esc_attr( $ver ); ?>"></script>

<?php
do_action( 'apollo_after_mural_content' );
wp_footer();
?>
</body>
</html>
