<?php

/**
 * Template: Single Local Page — GPS / Venue Profile
 * Standalone HTML document (no get_header/get_footer)
 * Ported from 1212 workspace
 *
 * @package Apollo\Local
 * @version 2.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! have_posts() || get_post_type() !== 'local' ) {
	status_header( 404 );
	nocache_headers();
	include get_404_template();
	exit;
}

the_post();
global $post;

$local_id   = $post->ID;
$local_name = get_post_meta( $local_id, '_local_name', true ) ?: get_the_title( $local_id );

// Address
$address      = get_post_meta( $local_id, '_local_address', true ) ?: '';
$city         = get_post_meta( $local_id, '_local_city', true ) ?: '';
$state        = get_post_meta( $local_id, '_local_state', true ) ?: '';
$full_address = trim( implode( ', ', array_filter( array( $address, $city, $state ) ) ) );

// Coordinates
$lat        = get_post_meta( $local_id, '_local_lat', true ) ?: '';
$lng        = get_post_meta( $local_id, '_local_lng', true ) ?: '';
$has_coords = ! empty( $lat ) && ! empty( $lng );

// Description / Bio
$local_desc = get_post_meta( $local_id, '_local_description', true ) ?: '';
$local_bio  = ! empty( $local_desc )
	? apply_filters( 'the_content', $local_desc )
	: apply_filters( 'the_content', get_the_content() );

// Social links
$social_defs  = array(
	'website'   => array( '_local_website', 'ri-global-line', __( 'Site oficial', 'apollo-local' ) ),
	'instagram' => array( '_local_instagram', 'ri-instagram-line', 'Instagram' ),
	'facebook'  => array( '_local_facebook', 'ri-facebook-circle-line', 'Facebook' ),
	'whatsapp'  => array( '_local_whatsapp', 'ri-whatsapp-line', 'WhatsApp' ),
);
$social_links = array();
foreach ( $social_defs as $key => $def ) {
	$url = get_post_meta( $local_id, $def[0], true );
	if ( $url ) {
		if ( $key === 'whatsapp' && is_numeric( preg_replace( '/[^0-9]/', '', $url ) ) ) {
			$url = 'https://wa.me/' . preg_replace( '/[^0-9]/', '', $url );
		}
		$social_links[ $key ] = array(
			'url'   => $url,
			'icon'  => $def[1],
			'label' => $def[2],
		);
	}
}

// Gallery images (up to 5)
$gallery = array();
for ( $i = 1; $i <= 5; $i++ ) {
	$img = get_post_meta( $local_id, "_local_image_{$i}", true );
	if ( $img ) {
		$gallery[] = is_numeric( $img ) ? wp_get_attachment_image_url( $img, 'large' ) : $img;
	}
}
// Featured image as fallback
if ( empty( $gallery ) && has_post_thumbnail( $local_id ) ) {
	$gallery[] = get_the_post_thumbnail_url( $local_id, 'large' );
}

// Venue type from taxonomy
$venue_types = get_the_terms( $local_id, 'local_type' );
$venue_label = '';
if ( ! is_wp_error( $venue_types ) && ! empty( $venue_types ) ) {
	$venue_label = $venue_types[0]->name;
}

// Capacity
$capacity = get_post_meta( $local_id, '_local_capacity', true ) ?: '';

// Phone
$phone = get_post_meta( $local_id, '_local_phone', true ) ?: '';

// Upcoming events
$upcoming_events = array();
if ( post_type_exists( 'event' ) ) {
	$event_query = new WP_Query(
		array(
			'post_type'      => 'event',
			'posts_per_page' => 6,
			'meta_query'     => array(
				'relation' => 'OR',
				array(
					'key'   => '_event_local_id',
					'value' => (string) $local_id,
				),
				array(
					'key'     => '_event_local_ids',
					'value'   => (string) $local_id,
					'compare' => 'LIKE',
				),
			),
			'meta_key'       => '_event_date',
			'orderby'        => 'meta_value',
			'order'          => 'ASC',
			'date_query'     => array( array( 'after' => 'now' ) ),
		)
	);
	if ( $event_query->have_posts() ) {
		while ( $event_query->have_posts() ) {
			$event_query->the_post();
			$ev_id     = get_the_ID();
			$ev_date   = get_post_meta( $ev_id, '_event_date', true );
			$ev_image  = get_the_post_thumbnail_url( $ev_id, 'medium' );
			$ev_sounds = get_the_terms( $ev_id, 'sound' );
			$ev_tags   = array();
			if ( ! is_wp_error( $ev_sounds ) && ! empty( $ev_sounds ) ) {
				$ev_tags = wp_list_pluck( array_slice( $ev_sounds, 0, 3 ), 'name' );
			}
			$upcoming_events[] = array(
				'id'    => $ev_id,
				'title' => get_the_title(),
				'url'   => get_permalink(),
				'date'  => $ev_date,
				'image' => $ev_image,
				'tags'  => $ev_tags,
			);
		}
	}
	wp_reset_postdata();
}

// Testimonials
$testimonials_raw = get_post_meta( $local_id, '_local_testimonials', true );
$testimonials     = array();
if ( ! empty( $testimonials_raw ) ) {
	if ( is_string( $testimonials_raw ) ) {
		$decoded = json_decode( $testimonials_raw, true );
		if ( is_array( $decoded ) ) {
			$testimonials = $decoded;
		}
	} elseif ( is_array( $testimonials_raw ) ) {
		$testimonials = $testimonials_raw;
	}
}

$plugin_url = defined( 'APOLLO_LOCAL_URL' ) ? APOLLO_LOCAL_URL : plugin_dir_url( __DIR__ );
$cdn_base   = 'https://assets.apollo.rio.br/';

// Mapbox static map URL
$map_url   = '';
$route_url = '';
if ( $has_coords ) {
	$map_url   = sprintf(
		'https://api.mapbox.com/styles/v1/mapbox/light-v11/static/pin-s+1e293b(%s,%s)/%s,%s,14/600x300@2x?access_token=%s',
		$lng,
		$lat,
		$lng,
		$lat,
		apply_filters( 'apollo_mapbox_token', '' )
	);
	$route_url = sprintf( 'https://www.google.com/maps/dir/?api=1&destination=%s,%s', $lat, $lng );
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo esc_html( $local_name ); ?> · Apollo GPS</title>
	<link rel="icon" href="<?php echo esc_url( $cdn_base . 'img/neon-green.webp' ); ?>" type="image/webp">

	<!-- Apollo CDN — Canvas Mode -->
	<script src="https://cdn.apollo.rio.br/v1.0.0/core.min.js?v=1.0.0" fetchpriority="high"></script>

	<!-- Local-specific stylesheet -->
	<link rel="stylesheet" href="<?php echo esc_url( $plugin_url . 'assets/css/local-single.css' ); ?>">
	<?php do_action( 'apollo_local_single_head', $local_id ); ?>
</head>

<body class="local-single-page" data-local-id="<?php echo esc_attr( $local_id ); ?>">

	<main class="mobile-container">

		<?php
		// HERO SLIDER
		?>
		<?php if ( ! empty( $gallery ) ) : ?>
			<div class="hero-slider-wrapper">
				<div class="hero-slider-track" id="heroTrack">
					<?php foreach ( $gallery as $idx => $img_url ) : ?>
						<div class="hero-slide<?php echo $idx === 0 ? ' active' : ''; ?>">
							<img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( $local_name ); ?>" loading="<?php echo $idx === 0 ? 'eager' : 'lazy'; ?>" decoding="async">
						</div>
					<?php endforeach; ?>
				</div>
				<?php if ( count( $gallery ) > 1 ) : ?>
					<div class="hero-slider-dots">
						<?php foreach ( $gallery as $idx => $img_url ) : ?>
							<span class="hero-dot<?php echo $idx === 0 ? ' active' : ''; ?>" data-slide="<?php echo $idx; ?>"></span>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
				<a href="javascript:history.back()" class="hero-back-btn" aria-label="<?php esc_attr_e( 'Voltar', 'apollo-local' ); ?>">
					<i class="ri-arrow-left-line"></i>
				</a>
			</div>
		<?php endif; ?>

		<?php
		// VENUE HEADER
		?>
		<div class="venue-body">
			<header class="venue-header">
				<?php if ( $venue_label ) : ?>
					<span class="venue-type-label"><?php echo esc_html( $venue_label ); ?></span>
				<?php endif; ?>
				<h1 class="venue-title"><?php echo esc_html( $local_name ); ?></h1>
				<?php if ( $full_address ) : ?>
					<p class="venue-address"><i class="ri-map-pin-2-line"></i> <?php echo esc_html( $full_address ); ?></p>
				<?php endif; ?>
				<?php if ( $capacity ) : ?>
					<p class="venue-capacity"><i class="ri-group-line"></i> <?php printf( esc_html__( 'Capacidade: %s', 'apollo-local' ), esc_html( $capacity ) ); ?></p>
				<?php endif; ?>
			</header>

			<?php
			// SOCIAL ROW
			?>
			<?php if ( ! empty( $social_links ) ) : ?>
				<div class="social-row">
					<?php foreach ( $social_links as $key => $link ) : ?>
						<a href="<?php echo esc_url( $link['url'] ); ?>" class="social-btn<?php echo $key === 'website' ? ' social-btn--primary' : ''; ?>" target="_blank" rel="noopener noreferrer" title="<?php echo esc_attr( $link['label'] ); ?>">
							<i class="<?php echo esc_attr( $link['icon'] ); ?>"></i>
							<span><?php echo esc_html( $link['label'] ); ?></span>
						</a>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<?php
			// BIO SECTION
			?>
			<?php if ( ! empty( $local_bio ) ) : ?>
				<section class="venue-section">
					<h2 class="section-title"><i class="ri-information-line"></i> <?php esc_html_e( 'Sobre o local', 'apollo-local' ); ?></h2>
					<div class="venue-bio"><?php echo $local_bio; ?></div>
				</section>
			<?php endif; ?>

			<?php
			// MAP SECTION
			?>
			<?php if ( $has_coords ) : ?>
				<section class="venue-section map-section">
					<h2 class="section-title"><i class="ri-map-pin-line"></i> <?php esc_html_e( 'Localização', 'apollo-local' ); ?></h2>
					<?php if ( ! empty( $map_url ) ) : ?>
						<div class="map-wrapper">
							<img src="<?php echo esc_url( $map_url ); ?>" alt="<?php echo esc_attr( sprintf( __( 'Mapa de %s', 'apollo-local' ), $local_name ) ); ?>" class="map-static" loading="lazy">
						</div>
					<?php endif; ?>
					<div class="route-controls">
						<div class="route-address">
							<i class="ri-map-pin-2-fill"></i>
							<span><?php echo esc_html( $full_address ?: __( 'Ver no mapa', 'apollo-local' ) ); ?></span>
						</div>
						<?php if ( ! empty( $route_url ) ) : ?>
							<a href="<?php echo esc_url( $route_url ); ?>" class="route-btn" target="_blank" rel="noopener noreferrer">
								<i class="ri-route-line"></i> <?php esc_html_e( 'Traçar rota', 'apollo-local' ); ?>
							</a>
						<?php endif; ?>
					</div>
				</section>
			<?php endif; ?>

			<?php
			// UPCOMING EVENTS
			?>
			<?php if ( ! empty( $upcoming_events ) ) : ?>
				<section class="venue-section events-section">
					<h2 class="section-title"><i class="ri-calendar-event-line"></i> <?php esc_html_e( 'Próximos eventos', 'apollo-local' ); ?></h2>
					<div class="events-grid">
						<?php
						foreach ( $upcoming_events as $ev ) :
							$ts    = strtotime( $ev['date'] );
							$day   = $ts ? date_i18n( 'd', $ts ) : '--';
							$month = $ts ? strtoupper( date_i18n( 'M', $ts ) ) : '--';
							?>
							<a href="<?php echo esc_url( $ev['url'] ); ?>" class="event-card">
								<?php if ( $ev['image'] ) : ?>
									<div class="event-card__media">
										<img src="<?php echo esc_url( $ev['image'] ); ?>" alt="" loading="lazy" decoding="async">
										<div class="date-box">
											<span class="date-day"><?php echo esc_html( $day ); ?></span>
											<span class="date-month"><?php echo esc_html( $month ); ?></span>
										</div>
									</div>
								<?php endif; ?>
								<div class="event-card__info">
									<h3><?php echo esc_html( $ev['title'] ); ?></h3>
									<?php if ( ! empty( $ev['tags'] ) ) : ?>
										<div class="event-tags">
											<?php foreach ( $ev['tags'] as $tag ) : ?>
												<span class="ap-tag"><?php echo esc_html( $tag ); ?></span>
											<?php endforeach; ?>
										</div>
									<?php endif; ?>
								</div>
							</a>
						<?php endforeach; ?>
					</div>
				</section>
			<?php endif; ?>

			<?php
			// TESTIMONIALS
			?>
			<?php if ( ! empty( $testimonials ) ) : ?>
				<section class="venue-section testimonials-section">
					<h2 class="section-title"><i class="ri-chat-quote-line"></i> <?php esc_html_e( 'Depoimentos', 'apollo-local' ); ?></h2>
					<div class="testimonials-scroller">
						<?php foreach ( $testimonials as $review ) : ?>
							<div class="review-card">
								<div class="reviewer-info">
									<?php if ( ! empty( $review['avatar'] ) ) : ?>
										<img src="<?php echo esc_url( $review['avatar'] ); ?>" alt="" class="reviewer-avatar" loading="lazy">
									<?php endif; ?>
									<div>
										<strong><?php echo esc_html( $review['name'] ?? __( 'Anônimo', 'apollo-local' ) ); ?></strong>
										<?php if ( ! empty( $review['rating'] ) ) : ?>
											<div class="stars"><?php echo str_repeat( '★', min( 5, (int) $review['rating'] ) ); ?></div>
										<?php endif; ?>
									</div>
								</div>
								<p class="review-text"><?php echo esc_html( $review['text'] ?? '' ); ?></p>
							</div>
						<?php endforeach; ?>
					</div>
				</section>
			<?php endif; ?>

			<?php
			// FOOTER
			?>
			<footer class="venue-footer">
				<span>Apollo::rio · GPS</span>
			</footer>
		</div><!-- .venue-body -->
	</main>

	<script src="<?php echo esc_url( $plugin_url . 'assets/js/local-single.js' ); ?>"></script>
	<?php wp_footer(); ?>
</body>

</html>
<?php wp_reset_postdata(); ?>