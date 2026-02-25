<?php
/**
 * Funções auxiliares do plugin
 *
 * @package Apollo\Event
 */

declare(strict_types=1);

namespace Apollo\Event;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Retorna instância principal do plugin
 */
function apollo_event(): Plugin {
	return Plugin::get_instance();
}

/**
 * Obtém opção do dashboard Apollo Events
 *
 * @param string $key     Chave da opção.
 * @param mixed  $default Valor padrão.
 * @return mixed
 */
function apollo_event_option( string $key, $default = null ) {
	$options = get_option( 'apollo_event_settings', [] );
	return $options[ $key ] ?? $default;
}

/**
 * Parseia data de início do evento para exibição
 *
 * @param string $date Data no formato Y-m-d.
 * @return array{timestamp: int, day: string, month_pt: string, iso_date: string, weekday_pt: string}
 */
function apollo_event_parse_date( string $date ): array {
	$meses_pt = [
		1 => 'Jan', 2 => 'Fev', 3 => 'Mar', 4 => 'Abr',
		5 => 'Mai', 6 => 'Jun', 7 => 'Jul', 8 => 'Ago',
		9 => 'Set', 10 => 'Out', 11 => 'Nov', 12 => 'Dez',
	];

	$dias_pt = [
		'Mon' => 'Seg', 'Tue' => 'Ter', 'Wed' => 'Qua',
		'Thu' => 'Qui', 'Fri' => 'Sex', 'Sat' => 'Sáb', 'Sun' => 'Dom',
	];

	$ts  = strtotime( $date );
	$m   = (int) date( 'n', $ts );
	$dow = date( 'D', $ts );

	return [
		'timestamp'  => $ts,
		'day'        => date( 'd', $ts ),
		'month_pt'   => $meses_pt[ $m ] ?? date( 'M', $ts ),
		'iso_date'   => date( 'Y-m-d', $ts ),
		'weekday_pt' => $dias_pt[ $dow ] ?? $dow,
	];
}

/**
 * Verifica se um evento está "gone" (expirado)
 *
 * 30 minutos após _event_end_date + _event_end_time
 *
 * @param int $post_id ID do evento.
 * @return bool
 */
function apollo_event_is_gone( int $post_id ): bool {
	// Checa meta já calculado
	$gone = get_post_meta( $post_id, '_event_is_gone', true );
	if ( '1' === $gone ) {
		return true;
	}

	$end_date = get_post_meta( $post_id, '_event_end_date', true );
	$end_time = get_post_meta( $post_id, '_event_end_time', true );

	if ( empty( $end_date ) ) {
		$end_date = get_post_meta( $post_id, '_event_start_date', true );
	}
	if ( empty( $end_time ) ) {
		$end_time = '23:59';
	}

	$end_ts = strtotime( $end_date . ' ' . $end_time );
	if ( ! $end_ts ) {
		return false;
	}

	$gone_ts = $end_ts + ( APOLLO_EVENT_GONE_OFFSET_MINUTES * 60 );
	$now     = current_time( 'timestamp' );

	if ( $now >= $gone_ts ) {
		update_post_meta( $post_id, '_event_is_gone', '1' );
		return true;
	}

	return false;
}

/**
 * Obtém lineup de DJs de um evento
 *
 * @param int $post_id ID do evento.
 * @return array Lista de DJs com id, title, image.
 */
function apollo_event_get_djs( int $post_id ): array {
	$dj_ids = get_post_meta( $post_id, '_event_dj_ids', true );

	if ( empty( $dj_ids ) || ! is_array( $dj_ids ) ) {
		return [];
	}

	$djs = [];
	foreach ( $dj_ids as $dj_id ) {
		$dj_post = get_post( (int) $dj_id );
		if ( ! $dj_post || 'publish' !== $dj_post->post_status ) {
			continue;
		}

		$djs[] = [
			'id'    => $dj_post->ID,
			'title' => $dj_post->post_title,
			'image' => get_the_post_thumbnail_url( $dj_post->ID, 'thumbnail' ) ?: '',
			'slug'  => $dj_post->post_name,
		];
	}

	return $djs;
}

/**
 * Obtém dados de localização (loc CPT) de um evento
 *
 * @param int $post_id ID do evento.
 * @return array|null Dados do local ou null.
 */
function apollo_event_get_loc( int $post_id ): ?array {
	$loc_id = (int) get_post_meta( $post_id, '_event_loc_id', true );
	if ( ! $loc_id ) {
		return null;
	}

	$loc_post = get_post( $loc_id );
	if ( ! $loc_post || 'publish' !== $loc_post->post_status ) {
		return null;
	}

	return [
		'id'      => $loc_post->ID,
		'title'   => $loc_post->post_title,
		'slug'    => $loc_post->post_name,
		'address' => get_post_meta( $loc_id, '_loc_address', true ),
		'city'    => get_post_meta( $loc_id, '_loc_city', true ),
		'lat'     => (float) get_post_meta( $loc_id, '_loc_lat', true ),
		'lng'     => (float) get_post_meta( $loc_id, '_loc_lng', true ),
	];
}

/**
 * Retorna URL do banner do evento com fallback para thumbnail
 *
 * @param int    $post_id ID do evento.
 * @param string $size    Tamanho da imagem.
 * @return string URL da imagem ou placeholder.
 */
function apollo_event_get_banner( int $post_id, string $size = 'large' ): string {
	$banner_id = (int) get_post_meta( $post_id, '_event_banner', true );

	if ( $banner_id ) {
		$url = wp_get_attachment_image_url( $banner_id, $size );
		if ( $url ) {
			return $url;
		}
	}

	$thumb = get_the_post_thumbnail_url( $post_id, $size );
	if ( $thumb ) {
		return $thumb;
	}

	return APOLLO_EVENT_URL . 'assets/images/placeholder-event.svg';
}

/**
 * Retorna estilo ativo para templates
 *
 * Prioridade: shortcode attr > opção dashboard > default (base)
 *
 * @param string $shortcode_style Estilo passado pelo shortcode.
 * @return string Nome do estilo.
 */
function apollo_event_get_active_style( string $shortcode_style = '' ): string {
	if ( $shortcode_style && in_array( $shortcode_style, APOLLO_EVENT_STYLES, true ) ) {
		return $shortcode_style;
	}

	$option = apollo_event_option( 'default_style', APOLLO_EVENT_DEFAULT_STYLE );

	if ( in_array( $option, APOLLO_EVENT_STYLES, true ) ) {
		return $option;
	}

	return APOLLO_EVENT_DEFAULT_STYLE;
}

/**
 * Monta query de eventos com cache
 *
 * @param array $args Argumentos do WP_Query.
 * @return \WP_Query
 */
function apollo_event_query( array $args = [] ): \WP_Query {
	$defaults = [
		'post_type'      => APOLLO_EVENT_CPT,
		'post_status'    => 'publish',
		'posts_per_page' => 12,
		'orderby'        => 'meta_value',
		'meta_key'       => '_event_start_date',
		'order'          => 'ASC',
	];

	$args     = wp_parse_args( $args, $defaults );
	$cache_key = 'apollo_eve_' . md5( wp_json_encode( $args ) );

	// Tenta cache de object cache
	$cached = wp_cache_get( $cache_key, APOLLO_EVENT_CACHE_GROUP );
	if ( false !== $cached && $cached instanceof \WP_Query ) {
		return $cached;
	}

	$query = new \WP_Query( $args );
	wp_cache_set( $cache_key, $query, APOLLO_EVENT_CACHE_GROUP, APOLLO_EVENT_CACHE_TTL );

	return $query;
}

/**
 * Invalida cache de eventos quando um evento é salvo
 *
 * @param int $post_id ID do post.
 */
function apollo_event_flush_cache( int $post_id ): void {
	if ( get_post_type( $post_id ) !== APOLLO_EVENT_CPT ) {
		return;
	}
	wp_cache_flush_group( APOLLO_EVENT_CACHE_GROUP );
}
add_action( 'save_post_' . APOLLO_EVENT_CPT, __NAMESPACE__ . '\\apollo_event_flush_cache' );
