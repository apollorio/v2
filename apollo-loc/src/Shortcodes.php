<?php
/**
 * Shortcodes — [apollo_locals], [apollo_local], [apollo_map]
 *
 * @package Apollo\Local
 */

declare(strict_types=1);

namespace Apollo\Local;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Shortcodes {

	private TemplateLoader $loader;

	public function __construct() {
		$this->loader = new TemplateLoader();

		add_shortcode( 'apollo_locals', array( $this, 'render_locals' ) );
		add_shortcode( 'apollo_local', array( $this, 'render_local' ) );
		add_shortcode( 'apollo_map', array( $this, 'render_map' ) );

		// Registrar no apollo-shortcodes
		add_filter( 'apollo_shortcodes_registry', array( $this, 'register_shortcodes' ) );
	}

	/**
	 * [apollo_locals] — Grid listing
	 */
	public function render_locals( $atts ): string {
		$atts = shortcode_atts(
			array(
				'limit' => 12,
				'type'  => '',
				'area'  => '',
			),
			$atts,
			'apollo_locals'
		);

		$args = array(
			'post_type'      => APOLLO_LOCAL_CPT,
			'posts_per_page' => (int) $atts['limit'],
			'post_status'    => 'publish',
			'orderby'        => 'title',
			'order'          => 'ASC',
		);

		$tax_query = array();

		if ( ! empty( $atts['type'] ) ) {
			$tax_query[] = array(
				'taxonomy' => APOLLO_LOCAL_TAX_TYPE,
				'field'    => 'slug',
				'terms'    => array_map( 'trim', explode( ',', $atts['type'] ) ),
			);
		}

		if ( ! empty( $atts['area'] ) ) {
			$tax_query[] = array(
				'taxonomy' => APOLLO_LOCAL_TAX_AREA,
				'field'    => 'slug',
				'terms'    => array_map( 'trim', explode( ',', $atts['area'] ) ),
			);
		}

		if ( ! empty( $tax_query ) ) {
			$args['tax_query'] = $tax_query;
		}

		$query = new \WP_Query( $args );

		$style   = apollo_local_option( 'default_style', APOLLO_LOCAL_DEFAULT_STYLE );
		$output  = '<div class="apollo-locals-wrap" data-style="' . esc_attr( $style ) . '">';
		$output .= '<div class="apollo-locals-grid">';

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$output .= $this->loader->render_to_string(
					'local-card',
					array(
						'post_id' => get_the_ID(),
					)
				);
			}
			wp_reset_postdata();
		} else {
			$output .= '<p class="apollo-locals-empty">' . esc_html__( 'Nenhum local encontrado.', 'apollo-local' ) . '</p>';
		}

		$output .= '</div></div>';
		return $output;
	}

	/**
	 * [apollo_local] — Single local embed
	 */
	public function render_local( $atts ): string {
		$atts = shortcode_atts(
			array(
				'id' => 0,
			),
			$atts,
			'apollo_local'
		);

		$post_id = (int) $atts['id'];
		if ( ! $post_id ) {
			return '';
		}

		$post = get_post( $post_id );
		if ( ! $post || APOLLO_LOCAL_CPT !== $post->post_type ) {
			return '';
		}

		$style = apollo_local_option( 'default_style', APOLLO_LOCAL_DEFAULT_STYLE );
		return '<div class="apollo-local-embed" data-style="' . esc_attr( $style ) . '">'
			. $this->loader->render_to_string( 'local-card', array( 'post_id' => $post_id ) )
			. '</div>';
	}

	/**
	 * [apollo_map] — Full map component (Leaflet/OSM)
	 */
	public function render_map( $atts ): string {
		$atts = shortcode_atts(
			array(
				'locs'   => '',
				'center' => '',
				'zoom'   => 12,
				'height' => '500px',
			),
			$atts,
			'apollo_map'
		);

		$settings   = get_option( 'apollo_local_settings', array() );
		$center_lat = $settings['default_center_lat'] ?? -22.9068;
		$center_lng = $settings['default_center_lng'] ?? -43.1729;

		if ( ! empty( $atts['center'] ) ) {
			$parts = explode( ',', $atts['center'] );
			if ( count( $parts ) === 2 ) {
				$center_lat = (float) trim( $parts[0] );
				$center_lng = (float) trim( $parts[1] );
			}
		}

		$loc_ids = array();
		if ( ! empty( $atts['locs'] ) ) {
			$loc_ids = array_map( 'intval', explode( ',', $atts['locs'] ) );
		}

		$map_id = 'apollo-map-' . wp_unique_id();

		$output  = '<div class="apollo-map-wrap">';
		$output .= '<div id="' . esc_attr( $map_id ) . '" class="apollo-map-container" ';
		$output .= 'data-lat="' . esc_attr( (string) $center_lat ) . '" ';
		$output .= 'data-lng="' . esc_attr( (string) $center_lng ) . '" ';
		$output .= 'data-zoom="' . esc_attr( (string) $atts['zoom'] ) . '" ';
		if ( ! empty( $loc_ids ) ) {
			$output .= 'data-locs="' . esc_attr( implode( ',', $loc_ids ) ) . '" ';
		}
		$output .= 'style="height:' . esc_attr( $atts['height'] ) . ';width:100%;">';
		$output .= '</div>';
		$output .= '</div>';

		return $output;
	}

	/**
	 * Registra no apollo-shortcodes
	 */
	public function register_shortcodes( array $shortcodes ): array {
		$shortcodes['apollo_locals'] = array(
			'label'       => 'Lista de Locais',
			'plugin'      => 'apollo-local',
			'description' => 'Grid de locais com filtro por tipo e zona.',
			'atts'        => array( 'limit', 'type', 'area' ),
		);

		$shortcodes['apollo_local'] = array(
			'label'       => 'Local Embed',
			'plugin'      => 'apollo-local',
			'description' => 'Embed de um local específico.',
			'atts'        => array( 'id' ),
		);

		$shortcodes['apollo_map'] = array(
			'label'       => 'Mapa',
			'plugin'      => 'apollo-local',
			'description' => 'Mapa interativo com Leaflet/OpenStreetMap.',
			'atts'        => array( 'locs', 'center', 'zoom', 'height' ),
		);

		return $shortcodes;
	}
}
