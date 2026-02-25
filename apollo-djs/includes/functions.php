<?php
/**
 * Funções helper do Apollo DJs
 *
 * @package Apollo\DJs
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Retorna instância do plugin
 */
function apollo_dj(): ?\Apollo\DJs\Plugin {
	return $GLOBALS['apollo_dj'] ?? null;
}

/**
 * Retorna opção do plugin
 */
function apollo_dj_option( string $key, $default = null ) {
	$settings = get_option( 'apollo_dj_settings', array() );
	return $settings[ $key ] ?? $default;
}

/**
 * Retorna a foto do DJ (banner ou thumbnail como fallback)
 */
function apollo_dj_get_image( int $dj_id ): string {
	$image_id = (int) get_post_meta( $dj_id, '_dj_image', true );
	if ( $image_id ) {
		$url = wp_get_attachment_image_url( $image_id, 'large' );
		if ( $url ) {
			return $url;
		}
	}

	$thumb = get_the_post_thumbnail_url( $dj_id, 'large' );
	if ( $thumb ) {
		return $thumb;
	}

	return APOLLO_DJ_URL . 'assets/images/placeholder-dj.svg';
}

/**
 * Retorna banner do DJ
 */
function apollo_dj_get_banner( int $dj_id ): string {
	$banner_id = (int) get_post_meta( $dj_id, '_dj_banner', true );
	if ( $banner_id ) {
		$url = wp_get_attachment_image_url( $banner_id, 'full' );
		if ( $url ) {
			return $url;
		}
	}

	return apollo_dj_get_image( $dj_id );
}

/**
 * Retorna links sociais agrupados por categoria
 */
function apollo_dj_get_links( int $dj_id ): array {
	$music     = array();
	$social    = array();
	$platforms = array();

	$mappings = array(
		'_dj_soundcloud' => array(
			'group' => 'music',
			'icon'  => 'ri-soundcloud-line',
			'label' => 'SoundCloud',
		),
		'_dj_spotify'    => array(
			'group' => 'music',
			'icon'  => 'ri-spotify-line',
			'label' => 'Spotify',
		),
		'_dj_youtube'    => array(
			'group' => 'music',
			'icon'  => 'ri-youtube-line',
			'label' => 'YouTube',
		),
		'_dj_instagram'  => array(
			'group' => 'social',
			'icon'  => 'ri-instagram-line',
			'label' => 'Instagram',
		),
		'_dj_mixcloud'   => array(
			'group' => 'platforms',
			'icon'  => 'ri-disc-line',
			'label' => 'Mixcloud',
		),
		'_dj_website'    => array(
			'group' => 'platforms',
			'icon'  => 'ri-global-line',
			'label' => 'Website',
		),
	);

	foreach ( $mappings as $meta_key => $info ) {
		$value = get_post_meta( $dj_id, $meta_key, true );
		if ( empty( $value ) ) {
			continue;
		}

		$link = array(
			'url'   => esc_url( $value ),
			'icon'  => $info['icon'],
			'label' => $info['label'],
		);

		switch ( $info['group'] ) {
			case 'music':
				$music[] = $link;
				break;
			case 'social':
				$social[] = $link;
				break;
			case 'platforms':
				$platforms[] = $link;
				break;
		}
	}

	return array(
		'music'     => $music,
		'social'    => $social,
		'platforms' => $platforms,
	);
}

/**
 * Retorna gêneros musicais do DJ (taxonomy sound)
 */
function apollo_dj_get_sounds( int $dj_id ): array {
	$terms = wp_get_post_terms( $dj_id, APOLLO_DJ_TAX_SOUND, array( 'fields' => 'names' ) );
	if ( is_wp_error( $terms ) ) {
		return array();
	}
	return $terms;
}

/**
 * Verifica se DJ é verificado
 */
function apollo_dj_is_verified( int $dj_id ): bool {
	return (bool) get_post_meta( $dj_id, '_dj_verified', true );
}

/**
 * Retorna eventos futuros do DJ
 */
function apollo_dj_get_upcoming_events( int $dj_id, int $limit = 5 ): array {
	$args = array(
		'post_type'      => 'event',
		'posts_per_page' => $limit,
		'post_status'    => 'publish',
		'meta_query'     => array(
			'relation' => 'AND',
			array(
				'key'     => '_event_dj_ids',
				'value'   => $dj_id,
				'compare' => 'LIKE',
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
 * Conta eventos futuros do DJ
 */
function apollo_dj_count_upcoming_events( int $dj_id ): int {
	$cache_key = 'dj_upcoming_count_' . $dj_id;
	$count     = wp_cache_get( $cache_key, APOLLO_DJ_CACHE_GROUP );

	if ( false !== $count ) {
		return (int) $count;
	}

	$count = count( apollo_dj_get_upcoming_events( $dj_id, -1 ) );
	wp_cache_set( $cache_key, $count, APOLLO_DJ_CACHE_GROUP, APOLLO_DJ_CACHE_TTL );

	return $count;
}

/**
 * Limpa cache do DJ quando um evento é salvo
 */
function apollo_dj_flush_cache( int $post_id ): void {
	$post_type = get_post_type( $post_id );

	if ( $post_type === APOLLO_DJ_CPT ) {
		wp_cache_delete( 'dj_upcoming_count_' . $post_id, APOLLO_DJ_CACHE_GROUP );
	}

	if ( $post_type === 'event' ) {
		$dj_ids = get_post_meta( $post_id, '_event_dj_ids', true );
		if ( is_array( $dj_ids ) ) {
			foreach ( $dj_ids as $dj_id ) {
				wp_cache_delete( 'dj_upcoming_count_' . $dj_id, APOLLO_DJ_CACHE_GROUP );
			}
		}
	}
}
add_action( 'save_post', 'apollo_dj_flush_cache' );
