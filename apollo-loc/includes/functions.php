<?php
/**
 * Funções helper do Apollo Local
 *
 * @package Apollo\Local
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Retorna instância do plugin
 */
function apollo_local(): ?\Apollo\Local\Plugin {
	return $GLOBALS['apollo_local'] ?? null;
}

/**
 * Retorna opção do plugin
 */
function apollo_local_option( string $key, $default = null ) {
	$settings = get_option( 'apollo_local_settings', array() );
	return $settings[ $key ] ?? $default;
}

/**
 * Retorna thumbnail do local
 */
function apollo_local_get_image( int $local_id ): string {
	$thumb = get_the_post_thumbnail_url( $local_id, 'large' );
	if ( $thumb ) {
		return $thumb;
	}
	return APOLLO_LOCAL_URL . 'assets/images/placeholder-local.svg';
}

/**
 * Retorna endereço formatado
 */
function apollo_local_get_address( int $local_id ): string {
	$address = get_post_meta( $local_id, '_local_address', true );
	$city    = get_post_meta( $local_id, '_local_city', true );
	$state   = get_post_meta( $local_id, '_local_state', true );

	$parts = array_filter( array( $address, $city, $state ) );
	return implode( ', ', $parts );
}

/**
 * Retorna coordenadas do local
 */
function apollo_local_get_coords( int $local_id ): ?array {
	$lat = get_post_meta( $local_id, '_local_lat', true );
	$lng = get_post_meta( $local_id, '_local_lng', true );

	if ( empty( $lat ) || empty( $lng ) ) {
		return null;
	}

	return array(
		'lat' => (float) $lat,
		'lng' => (float) $lng,
	);
}

/**
 * Retorna eventos futuros no local
 */
function apollo_local_get_upcoming_events( int $local_id, int $limit = 5 ): array {
	$args = array(
		'post_type'      => 'event',
		'posts_per_page' => $limit,
		'post_status'    => 'publish',
		'meta_query'     => array(
			'relation' => 'AND',
			array(
				'key'     => '_event_local_id',
				'value'   => $local_id,
				'compare' => '=',
				'type'    => 'NUMERIC',
			),
			array(
				'key'     => '_event_start_date',
				'value'   => current_time( 'Y-m-d' ),
				'compare' => '>=',
				'type'    => 'DATE',
			),
		),
		'orderby'        => 'meta_value',
		'meta_key'       => '_event_start_date',
		'order'          => 'ASC',
	);

	return get_posts( $args );
}

/**
 * Conta eventos futuros no local
 */
function apollo_local_count_upcoming_events( int $local_id ): int {
	$cache_key = 'local_upcoming_count_' . $local_id;
	$count     = wp_cache_get( $cache_key, APOLLO_LOCAL_CACHE_GROUP );

	if ( false !== $count ) {
		return (int) $count;
	}

	$count = count( apollo_local_get_upcoming_events( $local_id, -1 ) );
	wp_cache_set( $cache_key, $count, APOLLO_LOCAL_CACHE_GROUP, APOLLO_LOCAL_CACHE_TTL );

	return $count;
}

/**
 * Retorna tipo do local (taxonomy)
 */
function apollo_local_get_types( int $local_id ): array {
	$terms = wp_get_post_terms( $local_id, APOLLO_LOCAL_TAX_TYPE, array( 'fields' => 'names' ) );
	if ( is_wp_error( $terms ) ) {
		return array();
	}
	return $terms;
}

/**
 * Retorna zona/área do local
 */
function apollo_local_get_areas( int $local_id ): array {
	$terms = wp_get_post_terms( $local_id, APOLLO_LOCAL_TAX_AREA, array( 'fields' => 'names' ) );
	if ( is_wp_error( $terms ) ) {
		return array();
	}
	return $terms;
}

/**
 * Retorna links sociais do local
 */
function apollo_local_get_links( int $local_id ): array {
	$links = array();

	$website = get_post_meta( $local_id, '_local_website', true );
	if ( $website ) {
		$links[] = array(
			'url'   => $website,
			'icon'  => 'ri-global-line',
			'label' => 'Website',
		);
	}

	$instagram = get_post_meta( $local_id, '_local_instagram', true );
	if ( $instagram ) {
		$links[] = array(
			'url'   => $instagram,
			'icon'  => 'ri-instagram-line',
			'label' => 'Instagram',
		);
	}

	$phone = get_post_meta( $local_id, '_local_phone', true );
	if ( $phone ) {
		$links[] = array(
			'url'   => 'tel:' . $phone,
			'icon'  => 'ri-phone-line',
			'label' => $phone,
		);
	}

	return $links;
}

/**
 * Calcula distância entre dois pontos (Haversine, em km)
 */
function apollo_local_distance( float $lat1, float $lng1, float $lat2, float $lng2 ): float {
	$earth_radius = 6371; // km

	$dlat = deg2rad( $lat2 - $lat1 );
	$dlng = deg2rad( $lng2 - $lng1 );

	$a = sin( $dlat / 2 ) * sin( $dlat / 2 ) +
		cos( deg2rad( $lat1 ) ) * cos( deg2rad( $lat2 ) ) *
		sin( $dlng / 2 ) * sin( $dlng / 2 );

	$c = 2 * atan2( sqrt( $a ), sqrt( 1 - $a ) );

	return $earth_radius * $c;
}

/**
 * Limpa cache do local
 */
function apollo_local_flush_cache( int $post_id ): void {
	if ( get_post_type( $post_id ) === APOLLO_LOCAL_CPT ) {
		wp_cache_delete( 'local_upcoming_count_' . $post_id, APOLLO_LOCAL_CACHE_GROUP );
	}

	if ( get_post_type( $post_id ) === 'event' ) {
		$local_id = (int) get_post_meta( $post_id, '_event_local_id', true );
		if ( $local_id ) {
			wp_cache_delete( 'local_upcoming_count_' . $local_id, APOLLO_LOCAL_CACHE_GROUP );
		}
	}
}
add_action( 'save_post', 'apollo_local_flush_cache' );
