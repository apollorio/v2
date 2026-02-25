<?php
/**
 * Shortcodes — apollo-djs
 *
 * Registry shortcodes:
 * - [apollo_djs]       → DJ listing (attrs: limit, sound, featured)
 * - [apollo_dj]        → Single DJ embed (attrs: id)
 * - [apollo_dj_carousel] → DJ carousel slider
 *
 * @package Apollo\DJs
 */

declare(strict_types=1);

namespace Apollo\DJs;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Shortcodes {

	public function __construct() {
		add_shortcode( 'apollo_djs', array( $this, 'render_djs' ) );
		add_shortcode( 'apollo_dj', array( $this, 'render_dj' ) );
		add_shortcode( 'apollo_dj_carousel', array( $this, 'render_carousel' ) );

		// Registra no apollo-shortcodes se disponível
		add_filter( 'apollo_shortcodes_registry', array( $this, 'register_in_apollo_shortcodes' ) );
	}

	/**
	 * [apollo_djs] — DJ listing grid
	 */
	public function render_djs( $atts ): string {
		$atts = shortcode_atts(
			array(
				'limit'    => 12,
				'sound'    => '',
				'featured' => '',
			),
			$atts,
			'apollo_djs'
		);

		wp_enqueue_style( 'apollo-djs-v1' );
		wp_enqueue_script( 'apollo-djs' );

		$args = array(
			'post_type'      => APOLLO_DJ_CPT,
			'posts_per_page' => (int) $atts['limit'],
			'post_status'    => 'publish',
			'orderby'        => 'title',
			'order'          => 'ASC',
		);

		if ( ! empty( $atts['sound'] ) ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => APOLLO_DJ_TAX_SOUND,
					'field'    => 'slug',
					'terms'    => array_map( 'trim', explode( ',', $atts['sound'] ) ),
				),
			);
		}

		if ( $atts['featured'] ) {
			$args['meta_query'] = array(
				array(
					'key'   => '_dj_verified',
					'value' => '1',
				),
			);
		}

		$query = new \WP_Query( $args );

		if ( ! $query->have_posts() ) {
			return '<div class="a-dj-empty">' . esc_html__( 'Nenhum DJ encontrado.', 'apollo-djs' ) . '</div>';
		}

		ob_start();
		echo '<div class="a-dj-grid">';

		while ( $query->have_posts() ) {
			$query->the_post();
			$this->load_template(
				'dj-card',
				array(
					'dj_id'             => get_the_ID(),
					'dj_name'           => get_the_title(),
					'dj_image'          => apollo_dj_get_image( get_the_ID() ),
					'dj_sounds'         => apollo_dj_get_sounds( get_the_ID() ),
					'dj_verified'       => apollo_dj_is_verified( get_the_ID() ),
					'dj_bio'            => get_post_meta( get_the_ID(), '_dj_bio_short', true ),
					'dj_links'          => apollo_dj_get_links( get_the_ID() ),
					'dj_url'            => get_permalink(),
					'dj_upcoming_count' => apollo_dj_count_upcoming_events( get_the_ID() ),
				)
			);
		}
		wp_reset_postdata();

		echo '</div>';
		return ob_get_clean();
	}

	/**
	 * [apollo_dj id="123"] — Single DJ embed
	 */
	public function render_dj( $atts ): string {
		$atts = shortcode_atts(
			array(
				'id' => 0,
			),
			$atts,
			'apollo_dj'
		);

		$dj_id = (int) $atts['id'];
		if ( ! $dj_id || get_post_type( $dj_id ) !== APOLLO_DJ_CPT ) {
			return '';
		}

		wp_enqueue_style( 'apollo-djs-v1' );
		wp_enqueue_script( 'apollo-djs' );

		ob_start();
		$this->load_template(
			'dj-card',
			array(
				'dj_id'             => $dj_id,
				'dj_name'           => get_the_title( $dj_id ),
				'dj_image'          => apollo_dj_get_image( $dj_id ),
				'dj_sounds'         => apollo_dj_get_sounds( $dj_id ),
				'dj_verified'       => apollo_dj_is_verified( $dj_id ),
				'dj_bio'            => get_post_meta( $dj_id, '_dj_bio_short', true ),
				'dj_links'          => apollo_dj_get_links( $dj_id ),
				'dj_url'            => get_permalink( $dj_id ),
				'dj_upcoming_count' => apollo_dj_count_upcoming_events( $dj_id ),
			)
		);
		return ob_get_clean();
	}

	/**
	 * [apollo_dj_carousel] — Carousel/slider de DJs
	 */
	public function render_carousel( $atts ): string {
		$atts = shortcode_atts(
			array(
				'limit' => 10,
				'sound' => '',
			),
			$atts,
			'apollo_dj_carousel'
		);

		wp_enqueue_style( 'apollo-djs-v1' );
		wp_enqueue_script( 'apollo-djs' );

		$args = array(
			'post_type'      => APOLLO_DJ_CPT,
			'posts_per_page' => (int) $atts['limit'],
			'post_status'    => 'publish',
			'orderby'        => 'rand',
		);

		if ( ! empty( $atts['sound'] ) ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => APOLLO_DJ_TAX_SOUND,
					'field'    => 'slug',
					'terms'    => array_map( 'trim', explode( ',', $atts['sound'] ) ),
				),
			);
		}

		$query = new \WP_Query( $args );
		if ( ! $query->have_posts() ) {
			return '';
		}

		ob_start();
		echo '<div class="a-dj-carousel" data-carousel>';

		echo '<div class="a-dj-carousel__track">';
		while ( $query->have_posts() ) {
			$query->the_post();
			echo '<div class="a-dj-carousel__slide">';
			$this->load_template(
				'dj-card',
				array(
					'dj_id'             => get_the_ID(),
					'dj_name'           => get_the_title(),
					'dj_image'          => apollo_dj_get_image( get_the_ID() ),
					'dj_sounds'         => apollo_dj_get_sounds( get_the_ID() ),
					'dj_verified'       => apollo_dj_is_verified( get_the_ID() ),
					'dj_bio'            => get_post_meta( get_the_ID(), '_dj_bio_short', true ),
					'dj_links'          => apollo_dj_get_links( get_the_ID() ),
					'dj_url'            => get_permalink(),
					'dj_upcoming_count' => apollo_dj_count_upcoming_events( get_the_ID() ),
				)
			);
			echo '</div>';
		}
		wp_reset_postdata();
		echo '</div>';

		echo '<button class="a-dj-carousel__prev" aria-label="Anterior">&lsaquo;</button>';
		echo '<button class="a-dj-carousel__next" aria-label="Próximo">&rsaquo;</button>';

		echo '</div>';
		return ob_get_clean();
	}

	/**
	 * Carrega template com fallback
	 */
	private function load_template( string $template, array $data = array() ): void {
		$style = APOLLO_DJ_DEFAULT_STYLE;

		$paths = array(
			get_stylesheet_directory() . '/apollo-djs/' . $style . '/' . $template . '.php',
			get_template_directory() . '/apollo-djs/' . $style . '/' . $template . '.php',
			APOLLO_DJ_DIR . 'styles/' . $style . '/' . $template . '.php',
			APOLLO_DJ_DIR . 'styles/base/' . $template . '.php',
		);

		foreach ( $paths as $path ) {
			if ( file_exists( $path ) ) {
				extract( $data, EXTR_SKIP );
				include $path;
				return;
			}
		}
	}

	/**
	 * Registra no apollo-shortcodes
	 */
	public function register_in_apollo_shortcodes( array $shortcodes ): array {
		$shortcodes['apollo_djs'] = array(
			'tag'         => 'apollo_djs',
			'description' => 'DJ listing',
			'plugin'      => 'apollo-djs',
			'attrs'       => array( 'limit', 'sound', 'featured' ),
		);

		$shortcodes['apollo_dj'] = array(
			'tag'         => 'apollo_dj',
			'description' => 'Single DJ',
			'plugin'      => 'apollo-djs',
			'attrs'       => array( 'id' ),
		);

		$shortcodes['apollo_dj_carousel'] = array(
			'tag'         => 'apollo_dj_carousel',
			'description' => 'DJ carousel slider',
			'plugin'      => 'apollo-djs',
			'attrs'       => array(),
		);

		return $shortcodes;
	}
}
