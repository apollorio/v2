<?php
/**
 * Registry — CPT "dj", meta keys, metaboxes, admin columns
 *
 * CPT slug = "dj", rewrite = "dj", archive = "djs", rest_base = "djs"
 * Taxonomy: sound (GLOBAL BRIDGE via apollo-core, shared with event)
 *
 * @package Apollo\DJs
 */

declare(strict_types=1);

namespace Apollo\DJs;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Registry {

	public function __construct() {
		add_action( 'init', array( $this, 'register_cpt' ), 5 );
		add_filter( 'apollo_core_register_meta', array( $this, 'register_meta' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_metaboxes' ) );
		add_action( 'save_post_' . APOLLO_DJ_CPT, array( $this, 'save_metabox' ), 10, 2 );
		add_filter( 'manage_' . APOLLO_DJ_CPT . '_posts_columns', array( $this, 'admin_columns' ) );
		add_action( 'manage_' . APOLLO_DJ_CPT . '_posts_custom_column', array( $this, 'admin_column_content' ), 10, 2 );
	}

	/**
	 * Registra CPT "dj" — fallback se apollo-core não registrou
	 */
	public function register_cpt(): void {
		if ( post_type_exists( APOLLO_DJ_CPT ) ) {
			$this->register_taxonomy_fallback();
			return;
		}

		$labels = array(
			'name'               => __( 'DJs', 'apollo-djs' ),
			'singular_name'      => __( 'DJ', 'apollo-djs' ),
			'add_new'            => __( 'Novo DJ', 'apollo-djs' ),
			'add_new_item'       => __( 'Adicionar Novo DJ', 'apollo-djs' ),
			'edit_item'          => __( 'Editar DJ', 'apollo-djs' ),
			'new_item'           => __( 'Novo DJ', 'apollo-djs' ),
			'view_item'          => __( 'Ver DJ', 'apollo-djs' ),
			'search_items'       => __( 'Buscar DJs', 'apollo-djs' ),
			'not_found'          => __( 'Nenhum DJ encontrado', 'apollo-djs' ),
			'not_found_in_trash' => __( 'Nenhum DJ na lixeira', 'apollo-djs' ),
		);

		register_post_type(
			APOLLO_DJ_CPT,
			array(
				'labels'              => $labels,
				'public'              => true,
				'has_archive'         => 'djs',
				'rewrite'             => array(
					'slug'       => 'dj',
					'with_front' => false,
				),
				'rest_base'           => 'djs',
				'show_in_rest'        => true,
				'supports'            => array( 'title', 'editor', 'thumbnail', 'author' ),
				'menu_icon'           => 'dashicons-format-audio',
				'menu_position'       => 7,
				'taxonomies'          => array( APOLLO_DJ_TAX_SOUND ),
				'capability_type'     => 'post',
				'map_meta_cap'        => true,
				'show_in_admin_bar'   => true,
				'exclude_from_search' => false,
			)
		);

		$this->register_taxonomy_fallback();
	}

	/**
	 * Fallback: registra taxonomy sound se apollo-core/apollo-events não registraram
	 */
	private function register_taxonomy_fallback(): void {
		if ( ! taxonomy_exists( APOLLO_DJ_TAX_SOUND ) ) {
			register_taxonomy(
				APOLLO_DJ_TAX_SOUND,
				array( APOLLO_DJ_CPT, 'event' ),
				array(
					'labels'       => array(
						'name'          => 'Gêneros Musicais',
						'singular_name' => 'Gênero Musical',
					),
					'hierarchical' => true,
					'public'       => true,
					'show_in_rest' => true,
					'rewrite'      => array( 'slug' => 'som' ),
				)
			);
		}
	}

	/**
	 * Meta keys via apollo-core — conforme apollo-registry.json
	 */
	public function register_meta( array $meta_config ): array {
		$meta_config['dj'] = array(
			'_dj_image'      => array(
				'type'     => 'integer',
				'sanitize' => 'absint',
			),
			'_dj_banner'     => array(
				'type'     => 'integer',
				'sanitize' => 'absint',
			),
			'_dj_website'    => array(
				'type'     => 'string',
				'sanitize' => 'esc_url_raw',
			),
			'_dj_instagram'  => array(
				'type'     => 'string',
				'sanitize' => 'sanitize_text_field',
			),
			'_dj_soundcloud' => array(
				'type'     => 'string',
				'sanitize' => 'esc_url_raw',
			),
			'_dj_spotify'    => array(
				'type'     => 'string',
				'sanitize' => 'esc_url_raw',
			),
			'_dj_youtube'    => array(
				'type'     => 'string',
				'sanitize' => 'esc_url_raw',
			),
			'_dj_mixcloud'   => array(
				'type'     => 'string',
				'sanitize' => 'esc_url_raw',
			),
			'_dj_user_id'    => array(
				'type'     => 'integer',
				'sanitize' => 'absint',
			),
			'_dj_verified'   => array(
				'type'     => 'boolean',
				'sanitize' => 'rest_sanitize_boolean',
			),
			'_dj_bio_short'  => array(
				'type'     => 'string',
				'sanitize' => 'sanitize_textarea_field',
			),
		);

		return $meta_config;
	}

	/**
	 * Metabox no admin
	 */
	public function add_metaboxes(): void {
		add_meta_box(
			'apollo_dj_details',
			__( 'Detalhes do DJ', 'apollo-djs' ),
			array( $this, 'render_metabox' ),
			APOLLO_DJ_CPT,
			'normal',
			'high'
		);
	}

	/**
	 * Renderiza metabox
	 */
	public function render_metabox( \WP_Post $post ): void {
		wp_nonce_field( 'apollo_dj_metabox', 'apollo_dj_nonce' );

		$bio_short  = get_post_meta( $post->ID, '_dj_bio_short', true );
		$image_id   = get_post_meta( $post->ID, '_dj_image', true );
		$banner_id  = get_post_meta( $post->ID, '_dj_banner', true );
		$website    = get_post_meta( $post->ID, '_dj_website', true );
		$instagram  = get_post_meta( $post->ID, '_dj_instagram', true );
		$soundcloud = get_post_meta( $post->ID, '_dj_soundcloud', true );
		$spotify    = get_post_meta( $post->ID, '_dj_spotify', true );
		$youtube    = get_post_meta( $post->ID, '_dj_youtube', true );
		$mixcloud   = get_post_meta( $post->ID, '_dj_mixcloud', true );
		$user_id    = get_post_meta( $post->ID, '_dj_user_id', true );
		$verified   = get_post_meta( $post->ID, '_dj_verified', true );
		?>
		<style>
			.apollo-dj-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
			.apollo-dj-field { margin-bottom: 12px; }
			.apollo-dj-field label { display: block; font-weight: 600; margin-bottom: 4px; font-size: 13px; }
			.apollo-dj-field input,
			.apollo-dj-field textarea { width: 100%; padding: 6px 8px; }
			.apollo-dj-full { grid-column: 1 / -1; }
		</style>
		<div class="apollo-dj-grid">
			<div class="apollo-dj-field apollo-dj-full">
				<label for="apollo_dj_bio_short"><?php esc_html_e( 'Bio curta (max 280 chars)', 'apollo-djs' ); ?></label>
				<textarea id="apollo_dj_bio_short" name="_dj_bio_short" rows="3" maxlength="280"><?php echo esc_textarea( $bio_short ); ?></textarea>
			</div>
			<div class="apollo-dj-field">
				<label for="apollo_dj_image"><?php esc_html_e( 'Foto (attachment ID)', 'apollo-djs' ); ?></label>
				<input type="number" id="apollo_dj_image" name="_dj_image" value="<?php echo esc_attr( $image_id ); ?>" min="0">
			</div>
			<div class="apollo-dj-field">
				<label for="apollo_dj_banner"><?php esc_html_e( 'Banner (attachment ID)', 'apollo-djs' ); ?></label>
				<input type="number" id="apollo_dj_banner" name="_dj_banner" value="<?php echo esc_attr( $banner_id ); ?>" min="0">
			</div>
			<div class="apollo-dj-field">
				<label for="apollo_dj_user_id"><?php esc_html_e( 'User ID vinculado', 'apollo-djs' ); ?></label>
				<input type="number" id="apollo_dj_user_id" name="_dj_user_id" value="<?php echo esc_attr( $user_id ); ?>" min="0">
			</div>
			<div class="apollo-dj-field">
				<label>
					<input type="checkbox" name="_dj_verified" value="1" <?php checked( $verified, '1' ); ?>>
					<?php esc_html_e( 'DJ Verificado', 'apollo-djs' ); ?>
				</label>
			</div>
			<div class="apollo-dj-field">
				<label for="apollo_dj_website"><?php esc_html_e( 'Website', 'apollo-djs' ); ?></label>
				<input type="url" id="apollo_dj_website" name="_dj_website" value="<?php echo esc_url( $website ); ?>">
			</div>
			<div class="apollo-dj-field">
				<label for="apollo_dj_instagram"><?php esc_html_e( 'Instagram', 'apollo-djs' ); ?></label>
				<input type="text" id="apollo_dj_instagram" name="_dj_instagram" value="<?php echo esc_attr( $instagram ); ?>" placeholder="https://instagram.com/...">
			</div>
			<div class="apollo-dj-field">
				<label for="apollo_dj_soundcloud"><?php esc_html_e( 'SoundCloud', 'apollo-djs' ); ?></label>
				<input type="url" id="apollo_dj_soundcloud" name="_dj_soundcloud" value="<?php echo esc_url( $soundcloud ); ?>">
			</div>
			<div class="apollo-dj-field">
				<label for="apollo_dj_spotify"><?php esc_html_e( 'Spotify', 'apollo-djs' ); ?></label>
				<input type="url" id="apollo_dj_spotify" name="_dj_spotify" value="<?php echo esc_url( $spotify ); ?>">
			</div>
			<div class="apollo-dj-field">
				<label for="apollo_dj_youtube"><?php esc_html_e( 'YouTube', 'apollo-djs' ); ?></label>
				<input type="url" id="apollo_dj_youtube" name="_dj_youtube" value="<?php echo esc_url( $youtube ); ?>">
			</div>
			<div class="apollo-dj-field">
				<label for="apollo_dj_mixcloud"><?php esc_html_e( 'Mixcloud', 'apollo-djs' ); ?></label>
				<input type="url" id="apollo_dj_mixcloud" name="_dj_mixcloud" value="<?php echo esc_url( $mixcloud ); ?>">
			</div>
		</div>
		<?php
	}

	/**
	 * Salva metabox
	 */
	public function save_metabox( int $post_id, \WP_Post $post ): void {
		if ( ! isset( $_POST['apollo_dj_nonce'] ) || ! wp_verify_nonce( $_POST['apollo_dj_nonce'], 'apollo_dj_metabox' ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$text_fields = array( '_dj_bio_short', '_dj_instagram' );
		$url_fields  = array( '_dj_website', '_dj_soundcloud', '_dj_spotify', '_dj_youtube', '_dj_mixcloud' );
		$int_fields  = array( '_dj_image', '_dj_banner', '_dj_user_id' );

		foreach ( $text_fields as $key ) {
			if ( isset( $_POST[ $key ] ) ) {
				update_post_meta( $post_id, $key, sanitize_textarea_field( wp_unslash( $_POST[ $key ] ) ) );
			}
		}

		foreach ( $url_fields as $key ) {
			if ( isset( $_POST[ $key ] ) ) {
				update_post_meta( $post_id, $key, esc_url_raw( wp_unslash( $_POST[ $key ] ) ) );
			}
		}

		foreach ( $int_fields as $key ) {
			if ( isset( $_POST[ $key ] ) ) {
				update_post_meta( $post_id, $key, absint( $_POST[ $key ] ) );
			}
		}

		// Checkbox
		update_post_meta( $post_id, '_dj_verified', isset( $_POST['_dj_verified'] ) ? '1' : '' );
	}

	/**
	 * Colunas admin
	 */
	public function admin_columns( array $columns ): array {
		$new = array();
		foreach ( $columns as $key => $label ) {
			$new[ $key ] = $label;
			if ( 'title' === $key ) {
				$new['dj_verified'] = __( 'Verificado', 'apollo-djs' );
				$new['dj_sounds']   = __( 'Gêneros', 'apollo-djs' );
				$new['dj_events']   = __( 'Eventos', 'apollo-djs' );
			}
		}
		return $new;
	}

	/**
	 * Conteúdo das colunas admin
	 */
	public function admin_column_content( string $column, int $post_id ): void {
		switch ( $column ) {
			case 'dj_verified':
				echo apollo_dj_is_verified( $post_id ) ? '✅' : '—';
				break;

			case 'dj_sounds':
				$sounds = apollo_dj_get_sounds( $post_id );
				echo esc_html( implode( ', ', $sounds ) ?: '—' );
				break;

			case 'dj_events':
				$count = apollo_dj_count_upcoming_events( $post_id );
				echo esc_html( $count );
				break;
		}
	}
}
