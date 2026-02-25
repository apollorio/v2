<?php
/**
 * CPT, Taxonomy, Meta Registration (Fallback)
 *
 * Apollo Core's CPTRegistry should register CPT `classified` from master-registry.
 * This file provides fallback registration + taxonomy + meta registration + post statuses.
 *
 * Adapted from WPAdverts init/module_cpt_register patterns.
 *
 * @package Apollo\Adverts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Fallback CPT registration (if apollo-core didn't register it)
 * Adapted from WPAdverts adverts_register_post_type
 */
function apollo_adverts_register_cpt(): void {

	// Only register if core hasn't already
	if ( post_type_exists( APOLLO_CPT_CLASSIFIED ) ) {
		return;
	}

	$labels = array(
		'name'               => __( 'Anúncios', 'apollo-adverts' ),
		'singular_name'      => __( 'Anúncio', 'apollo-adverts' ),
		'add_new'            => __( 'Novo Anúncio', 'apollo-adverts' ),
		'add_new_item'       => __( 'Adicionar Anúncio', 'apollo-adverts' ),
		'edit_item'          => __( 'Editar Anúncio', 'apollo-adverts' ),
		'new_item'           => __( 'Novo Anúncio', 'apollo-adverts' ),
		'view_item'          => __( 'Ver Anúncio', 'apollo-adverts' ),
		'search_items'       => __( 'Buscar Anúncios', 'apollo-adverts' ),
		'not_found'          => __( 'Nenhum anúncio encontrado', 'apollo-adverts' ),
		'not_found_in_trash' => __( 'Nenhum anúncio na lixeira', 'apollo-adverts' ),
		'menu_name'          => __( 'Anúncios', 'apollo-adverts' ),
	);

	register_post_type(
		APOLLO_CPT_CLASSIFIED,
		array(
			'labels'          => $labels,
			'public'          => true,
			'has_archive'     => true,
			'show_in_rest'    => true,
			'rest_base'       => 'classifieds',
			'rewrite'         => array(
				'slug'       => 'anuncio',
				'with_front' => false,
			),
			'supports'        => array( 'title', 'editor', 'thumbnail', 'author', 'excerpt' ),
			'menu_icon'       => 'dashicons-megaphone',
			'capability_type' => 'post',
			'map_meta_cap'    => true,
		)
	);
}
add_action( 'init', 'apollo_adverts_register_cpt', 5 );

/**
 * Register taxonomies (fallback if core didn't)
 * Adapted from WPAdverts taxonomy registration
 */
function apollo_adverts_register_taxonomies(): void {

	// classified_domain (Tipo de Anúncio)
	if ( ! taxonomy_exists( APOLLO_TAX_CLASSIFIED_DOMAIN ) ) {
		register_taxonomy(
			APOLLO_TAX_CLASSIFIED_DOMAIN,
			APOLLO_CPT_CLASSIFIED,
			array(
				'labels'            => array(
					'name'          => __( 'Categorias', 'apollo-adverts' ),
					'singular_name' => __( 'Categoria', 'apollo-adverts' ),
					'search_items'  => __( 'Buscar Categorias', 'apollo-adverts' ),
					'all_items'     => __( 'Todas as Categorias', 'apollo-adverts' ),
					'edit_item'     => __( 'Editar Categoria', 'apollo-adverts' ),
					'add_new_item'  => __( 'Nova Categoria', 'apollo-adverts' ),
					'menu_name'     => __( 'Categorias', 'apollo-adverts' ),
				),
				'hierarchical'      => false,
				'public'            => true,
				'show_in_rest'      => true,
				'show_admin_column' => true,
				'rewrite'           => array(
					'slug'       => 'tipo-anuncio',
					'with_front' => false,
				),
			)
		);
	}

	// classified_intent (Intenção)
	if ( ! taxonomy_exists( APOLLO_TAX_CLASSIFIED_INTENT ) ) {
		register_taxonomy(
			APOLLO_TAX_CLASSIFIED_INTENT,
			APOLLO_CPT_CLASSIFIED,
			array(
				'labels'            => array(
					'name'          => __( 'Intenções', 'apollo-adverts' ),
					'singular_name' => __( 'Intenção', 'apollo-adverts' ),
					'search_items'  => __( 'Buscar Intenções', 'apollo-adverts' ),
					'all_items'     => __( 'Todas as Intenções', 'apollo-adverts' ),
					'edit_item'     => __( 'Editar Intenção', 'apollo-adverts' ),
					'add_new_item'  => __( 'Nova Intenção', 'apollo-adverts' ),
					'menu_name'     => __( 'Intenções', 'apollo-adverts' ),
				),
				'hierarchical'      => false,
				'public'            => true,
				'show_in_rest'      => true,
				'show_admin_column' => true,
				'rewrite'           => array(
					'slug'       => 'intencao',
					'with_front' => false,
				),
			)
		);
	}
}
add_action( 'init', 'apollo_adverts_register_taxonomies', 5 );

/**
 * Register custom post statuses
 * Adapted from WPAdverts adverts_register_status
 */
function apollo_adverts_register_post_statuses(): void {

	register_post_status(
		'expired',
		array(
			'label'                     => __( 'Expirado', 'apollo-adverts' ),
			'public'                    => false,
			'exclude_from_search'       => true,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Expirado <span class="count">(%s)</span>', 'Expirados <span class="count">(%s)</span>', 'apollo-adverts' ),
		)
	);

	register_post_status(
		'classified_tmp',
		array(
			'label'                     => __( 'Temporário', 'apollo-adverts' ),
			'public'                    => false,
			'exclude_from_search'       => true,
			'show_in_admin_all_list'    => false,
			'show_in_admin_status_list' => false,
		)
	);
}
add_action( 'init', 'apollo_adverts_register_post_statuses', 6 );

/**
 * Register post meta THROUGH apollo-core MetaRegistry.
 *
 * This plugin must not call register_post_meta() directly.
 * Adapted config is merged into core map via filter.
 *
 * @param array $meta_config Existing post meta map from apollo-core.
 * @return array
 */
function apollo_adverts_register_meta( array $meta_config ): array {
	if ( ! isset( $meta_config[ APOLLO_CPT_CLASSIFIED ] ) || ! is_array( $meta_config[ APOLLO_CPT_CLASSIFIED ] ) ) {
		$meta_config[ APOLLO_CPT_CLASSIFIED ] = array();
	}

	$meta_keys = APOLLO_ADVERTS_META_KEYS;

	foreach ( $meta_keys as $key => $config ) {
		$type = 'string';
		if ( $config['type'] === 'float' ) {
			$type = 'number';
		} elseif ( $config['type'] === 'bool' ) {
			$type = 'boolean';
		} elseif ( $config['type'] === 'int' ) {
			$type = 'integer';
		}

		$meta_config[ APOLLO_CPT_CLASSIFIED ][ $key ] = array(
			'type'          => $type,
			'single'        => true,
			'show_in_rest'  => true,
			'sanitize'      => 'apollo_adverts_sanitize_meta_' . $config['type'],
			'auth_callback' => function () {
				return current_user_can( 'edit_posts' );
			},
		);
	}

	return $meta_config;
}
add_filter( 'apollo_core_register_meta', 'apollo_adverts_register_meta', 10 );

/**
 * Meta sanitization callbacks
 */
function apollo_adverts_sanitize_meta_float( $value ): float {
	return (float) $value;
}

function apollo_adverts_sanitize_meta_string( $value ): string {
	return sanitize_text_field( (string) $value );
}

function apollo_adverts_sanitize_meta_bool( $value ): string {
	return $value ? '1' : '';
}

function apollo_adverts_sanitize_meta_int( $value ): int {
	return (int) $value;
}

function apollo_adverts_sanitize_meta_date( $value ): string {
	if ( preg_match( '/^\d{4}-\d{2}-\d{2}$/', (string) $value ) ) {
		return (string) $value;
	}
	return '';
}

/**
 * Add "Expired" status to admin dropdown
 * Adapted from WPAdverts admin status display
 */
function apollo_adverts_admin_status_display(): void {
	global $post;
	if ( ! $post || $post->post_type !== APOLLO_CPT_CLASSIFIED ) {
		return;
	}

	if ( $post->post_status === 'expired' ) {
		echo '<script>
			jQuery(document).ready(function($){
				$("select#post_status").append(\'<option value="expired" selected="selected">' . esc_js( __( 'Expirado', 'apollo-adverts' ) ) . '</option>\');
				$(".misc-pub-section #post-status-display").text("' . esc_js( __( 'Expirado', 'apollo-adverts' ) ) . '");
			});
		</script>';
	}
}
add_action( 'admin_footer-post.php', 'apollo_adverts_admin_status_display' );
add_action( 'admin_footer-post-new.php', 'apollo_adverts_admin_status_display' );
