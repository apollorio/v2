<?php

/**
 * Registry — Registra CPT, meta keys e hooks via apollo-core
 *
 * CPT slug = "event", rewrite = "evento"
 * Taxonomias: event_category, event_type, event_tag, sound, season (GLOBAL BRIDGE via apollo-core)
 *
 * @package Apollo\Event
 */

declare(strict_types=1);

namespace Apollo\Event;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Registry {



	public function __construct() {
		// CPT registration — fallback se apollo-core não registrou
		add_action( 'init', array( $this, 'register_cpt' ), 5 );

		// Meta registration via apollo-core hook
		add_filter( 'apollo_core_register_meta', array( $this, 'register_meta' ) );

		// Metaboxes no admin
		add_action( 'add_meta_boxes', array( $this, 'add_metaboxes' ) );
		add_action( 'save_post_' . APOLLO_EVENT_CPT, array( $this, 'save_metabox' ), 10, 2 );

		// Colunas customizadas no admin
		add_filter( 'manage_' . APOLLO_EVENT_CPT . '_posts_columns', array( $this, 'admin_columns' ) );
		add_action( 'manage_' . APOLLO_EVENT_CPT . '_posts_custom_column', array( $this, 'admin_column_content' ), 10, 2 );
		add_filter( 'manage_edit-' . APOLLO_EVENT_CPT . '_sortable_columns', array( $this, 'sortable_columns' ) );
	}

	/**
	 * Registra CPT "event" — apollo-core faz fallback, mas registramos aqui como owner
	 */
	public function register_cpt(): void {
		// Se apollo-core já registrou, não fazer nada
		if ( post_type_exists( APOLLO_EVENT_CPT ) ) {
			// Garantir que taxonomias existam mesmo se apollo-core já registrou o CPT
			$this->register_taxonomies_fallback();
			return;
		}

		$labels = array(
			'name'               => __( 'Eventos', 'apollo-events' ),
			'singular_name'      => __( 'Evento', 'apollo-events' ),
			'add_new'            => __( 'Novo Evento', 'apollo-events' ),
			'add_new_item'       => __( 'Adicionar Novo Evento', 'apollo-events' ),
			'edit_item'          => __( 'Editar Evento', 'apollo-events' ),
			'new_item'           => __( 'Novo Evento', 'apollo-events' ),
			'view_item'          => __( 'Ver Evento', 'apollo-events' ),
			'search_items'       => __( 'Buscar Eventos', 'apollo-events' ),
			'not_found'          => __( 'Nenhum evento encontrado', 'apollo-events' ),
			'not_found_in_trash' => __( 'Nenhum evento na lixeira', 'apollo-events' ),
		);

		register_post_type(
			APOLLO_EVENT_CPT,
			array(
				'labels'              => $labels,
				'public'              => true,
				'has_archive'         => 'eventos',
				'rewrite'             => array(
					'slug'       => 'evento',
					'with_front' => false,
				),
				'rest_base'           => 'events',
				'show_in_rest'        => true,
				'supports'            => array( 'title', 'editor', 'thumbnail', 'author' ),
				'menu_icon'           => 'dashicons-calendar-alt',
				'menu_position'       => 6,
				'taxonomies'          => array(
					APOLLO_EVENT_TAX_CATEGORY,
					APOLLO_EVENT_TAX_TYPE,
					APOLLO_EVENT_TAX_TAG,
					APOLLO_EVENT_TAX_SOUND,
					APOLLO_EVENT_TAX_SEASON,
				),
				'capability_type'     => 'post',
				'map_meta_cap'        => true,
				'show_in_admin_bar'   => true,
				'exclude_from_search' => false,
			)
		);

		$this->register_taxonomies_fallback();
	}

	/**
	 * Registra taxonomias com fallback — conforme apollo-registry.json
	 * Se apollo-core já registrou, pula. Senão, registra aqui.
	 */
	private function register_taxonomies_fallback(): void {
		// event_category
		if ( ! taxonomy_exists( APOLLO_EVENT_TAX_CATEGORY ) ) {
			register_taxonomy(
				APOLLO_EVENT_TAX_CATEGORY,
				APOLLO_EVENT_CPT,
				array(
					'labels'       => array(
						'name'          => 'Categorias',
						'singular_name' => 'Categoria',
					),
					'hierarchical' => true,
					'public'       => true,
					'show_in_rest' => true,
					'rewrite'      => array( 'slug' => 'categoria-evento' ),
				)
			);
		}

		// event_type
		if ( ! taxonomy_exists( APOLLO_EVENT_TAX_TYPE ) ) {
			register_taxonomy(
				APOLLO_EVENT_TAX_TYPE,
				APOLLO_EVENT_CPT,
				array(
					'labels'       => array(
						'name'          => 'Tipos',
						'singular_name' => 'Tipo',
					),
					'hierarchical' => true,
					'public'       => true,
					'show_in_rest' => true,
					'rewrite'      => array( 'slug' => 'tipo-evento' ),
				)
			);
		}

		// event_tag
		if ( ! taxonomy_exists( APOLLO_EVENT_TAX_TAG ) ) {
			register_taxonomy(
				APOLLO_EVENT_TAX_TAG,
				APOLLO_EVENT_CPT,
				array(
					'labels'       => array(
						'name'          => 'Tags',
						'singular_name' => 'Tag',
					),
					'hierarchical' => false,
					'public'       => true,
					'show_in_rest' => true,
					'rewrite'      => array( 'slug' => 'tag-evento' ),
				)
			);
		}

		// sound — GLOBAL BRIDGE (shared with dj)
		if ( ! taxonomy_exists( APOLLO_EVENT_TAX_SOUND ) ) {
			register_taxonomy(
				APOLLO_EVENT_TAX_SOUND,
				array( APOLLO_EVENT_CPT, 'dj' ),
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

		// season — shared with classified
		if ( ! taxonomy_exists( APOLLO_EVENT_TAX_SEASON ) ) {
			register_taxonomy(
				APOLLO_EVENT_TAX_SEASON,
				array( APOLLO_EVENT_CPT, 'classified' ),
				array(
					'labels'       => array(
						'name'          => 'Temporadas',
						'singular_name' => 'Temporada',
					),
					'hierarchical' => true,
					'public'       => true,
					'show_in_rest' => true,
					'rewrite'      => array( 'slug' => 'temporada' ),
				)
			);
		}
	}

	/**
	 * Registra meta keys via apollo-core — conforme apollo-registry.json
	 *
	 * @param array $meta_config Meta config acumulado.
	 * @return array
	 */
	public function register_meta( array $meta_config ): array {
		$meta_config['event'] = array(
			'_event_start_date'   => array(
				'type'     => 'string',
				'sanitize' => 'sanitize_text_field',
			),
			'_event_end_date'     => array(
				'type'     => 'string',
				'sanitize' => 'sanitize_text_field',
			),
			'_event_start_time'   => array(
				'type'     => 'string',
				'sanitize' => 'sanitize_text_field',
			),
			'_event_end_time'     => array(
				'type'     => 'string',
				'sanitize' => 'sanitize_text_field',
			),
			'_event_dj_ids'       => array(
				'type'     => 'array',
				'sanitize' => 'array_map_intval',
			),
			'_event_dj_slots'     => array(
				'type'     => 'array',
				'sanitize' => 'wp_kses_post',
			),
			'_event_loc_id'       => array(
				'type'     => 'integer',
				'sanitize' => 'absint',
			),
			'_event_banner'       => array(
				'type'     => 'integer',
				'sanitize' => 'absint',
			),
			'_event_ticket_url'   => array(
				'type'     => 'string',
				'sanitize' => 'esc_url_raw',
			),
			'_event_ticket_price' => array(
				'type'     => 'string',
				'sanitize' => 'sanitize_text_field',
			),
			'_event_privacy'      => array(
				'type'     => 'string',
				'sanitize' => 'sanitize_text_field',
			),
			'_event_status'       => array(
				'type'     => 'string',
				'sanitize' => 'sanitize_text_field',
			),
			'_event_is_gone'      => array(
				'type'     => 'string',
				'sanitize' => 'sanitize_text_field',
			),
			'_event_video_url'    => array(
				'type'     => 'string',
				'sanitize' => 'esc_url_raw',
			),
			'_event_gallery'      => array(
				'type'     => 'array',
				'sanitize' => 'array_map_intval',
			),
			'_event_coupon_code'  => array(
				'type'     => 'string',
				'sanitize' => 'sanitize_text_field',
			),
			'_event_list_url'     => array(
				'type'     => 'string',
				'sanitize' => 'esc_url_raw',
			),
		);

		return $meta_config;
	}

	/**
	 * Metabox de detalhes do evento no admin
	 */
	public function add_metaboxes(): void {
		add_meta_box(
			'apollo_event_details',
			__( 'Detalhes do Evento', 'apollo-events' ),
			array( $this, 'render_metabox' ),
			APOLLO_EVENT_CPT,
			'normal',
			'high'
		);
	}

	/**
	 * Renderiza metabox
	 *
	 * @param \WP_Post $post Post atual.
	 */
	public function render_metabox( \WP_Post $post ): void {
		wp_nonce_field( 'apollo_event_metabox', 'apollo_event_nonce' );

		$start_date   = get_post_meta( $post->ID, '_event_start_date', true );
		$end_date     = get_post_meta( $post->ID, '_event_end_date', true );
		$start_time   = get_post_meta( $post->ID, '_event_start_time', true );
		$end_time     = get_post_meta( $post->ID, '_event_end_time', true );
		$loc_id       = get_post_meta( $post->ID, '_event_loc_id', true );
		$banner_id    = get_post_meta( $post->ID, '_event_banner', true );
		$ticket_url   = get_post_meta( $post->ID, '_event_ticket_url', true );
		$ticket_price = get_post_meta( $post->ID, '_event_ticket_price', true );
		$privacy      = get_post_meta( $post->ID, '_event_privacy', true ) ?: 'public';
		$status       = get_post_meta( $post->ID, '_event_status', true ) ?: 'scheduled';
		$dj_ids       = get_post_meta( $post->ID, '_event_dj_ids', true ) ?: array();
		$dj_slots     = get_post_meta( $post->ID, '_event_dj_slots', true ) ?: array();
		$video_url    = get_post_meta( $post->ID, '_event_video_url', true );
		$gallery      = get_post_meta( $post->ID, '_event_gallery', true ) ?: array();
		$coupon_code  = get_post_meta( $post->ID, '_event_coupon_code', true );
		$list_url     = get_post_meta( $post->ID, '_event_list_url', true );
		?>
		<style>
			.apollo-metabox-grid {
				display: grid;
				grid-template-columns: 1fr 1fr;
				gap: 16px;
			}

			.apollo-metabox-field {
				margin-bottom: 12px;
			}

			.apollo-metabox-field label {
				display: block;
				font-weight: 600;
				margin-bottom: 4px;
				font-size: 13px;
			}

			.apollo-metabox-field input,
			.apollo-metabox-field select {
				width: 100%;
				padding: 6px 8px;
			}

			.apollo-metabox-full {
				grid-column: 1 / -1;
			}
		</style>
		<div class="apollo-metabox-grid">
			<div class="apollo-metabox-field">
				<label for="apollo_start_date"><?php esc_html_e( 'Data Início', 'apollo-events' ); ?></label>
				<input type="date" id="apollo_start_date" name="_event_start_date" value="<?php echo esc_attr( $start_date ); ?>" required>
			</div>
			<div class="apollo-metabox-field">
				<label for="apollo_end_date"><?php esc_html_e( 'Data Fim', 'apollo-events' ); ?></label>
				<input type="date" id="apollo_end_date" name="_event_end_date" value="<?php echo esc_attr( $end_date ); ?>">
			</div>
			<div class="apollo-metabox-field">
				<label for="apollo_start_time"><?php esc_html_e( 'Hora Início', 'apollo-events' ); ?></label>
				<input type="time" id="apollo_start_time" name="_event_start_time" value="<?php echo esc_attr( $start_time ); ?>">
			</div>
			<div class="apollo-metabox-field">
				<label for="apollo_end_time"><?php esc_html_e( 'Hora Fim', 'apollo-events' ); ?></label>
				<input type="time" id="apollo_end_time" name="_event_end_time" value="<?php echo esc_attr( $end_time ); ?>">
			</div>
			<div class="apollo-metabox-field">
				<label for="apollo_loc_id"><?php esc_html_e( 'Local (loc ID)', 'apollo-events' ); ?></label>
				<input type="number" id="apollo_loc_id" name="_event_loc_id" value="<?php echo esc_attr( $loc_id ); ?>" min="0">
			</div>
			<div class="apollo-metabox-field">
				<label for="apollo_banner"><?php esc_html_e( 'Banner (attachment ID)', 'apollo-events' ); ?></label>
				<input type="number" id="apollo_banner" name="_event_banner" value="<?php echo esc_attr( $banner_id ); ?>" min="0">
			</div>
			<div class="apollo-metabox-field">
				<label for="apollo_ticket_url"><?php esc_html_e( 'URL dos Ingressos', 'apollo-events' ); ?></label>
				<input type="url" id="apollo_ticket_url" name="_event_ticket_url" value="<?php echo esc_url( $ticket_url ); ?>">
			</div>
			<div class="apollo-metabox-field">
				<label for="apollo_ticket_price"><?php esc_html_e( 'Preço', 'apollo-events' ); ?></label>
				<input type="text" id="apollo_ticket_price" name="_event_ticket_price" value="<?php echo esc_attr( $ticket_price ); ?>" placeholder="R$ 50,00">
			</div>
			<div class="apollo-metabox-field">
				<label for="apollo_privacy"><?php esc_html_e( 'Privacidade', 'apollo-events' ); ?></label>
				<select id="apollo_privacy" name="_event_privacy">
					<option value="public" <?php selected( $privacy, 'public' ); ?>><?php esc_html_e( 'Público', 'apollo-events' ); ?></option>
					<option value="private" <?php selected( $privacy, 'private' ); ?>><?php esc_html_e( 'Privado', 'apollo-events' ); ?></option>
					<option value="invite" <?php selected( $privacy, 'invite' ); ?>><?php esc_html_e( 'Apenas Convidados', 'apollo-events' ); ?></option>
				</select>
			</div>
			<div class="apollo-metabox-field">
				<label for="apollo_status"><?php esc_html_e( 'Status', 'apollo-events' ); ?></label>
				<select id="apollo_status" name="_event_status">
					<option value="scheduled" <?php selected( $status, 'scheduled' ); ?>><?php esc_html_e( 'Agendado', 'apollo-events' ); ?></option>
					<option value="ongoing" <?php selected( $status, 'ongoing' ); ?>><?php esc_html_e( 'Em andamento', 'apollo-events' ); ?></option>
					<option value="finished" <?php selected( $status, 'finished' ); ?>><?php esc_html_e( 'Finalizado', 'apollo-events' ); ?></option>
					<option value="cancelled" <?php selected( $status, 'cancelled' ); ?>><?php esc_html_e( 'Cancelado', 'apollo-events' ); ?></option>
					<option value="postponed" <?php selected( $status, 'postponed' ); ?>><?php esc_html_e( 'Adiado', 'apollo-events' ); ?></option>
				</select>
			</div>
			<div class="apollo-metabox-field apollo-metabox-full">
				<label for="apollo_dj_ids"><?php esc_html_e( 'DJs (IDs separados por vírgula)', 'apollo-events' ); ?></label>
				<input type="text" id="apollo_dj_ids" name="_event_dj_ids" value="<?php echo esc_attr( is_array( $dj_ids ) ? implode( ',', $dj_ids ) : '' ); ?>" placeholder="123,456,789">
			</div>
			<div class="apollo-metabox-field apollo-metabox-full">
				<label for="apollo_dj_slots"><?php esc_html_e( 'DJ Slots — JSON (horários por DJ)', 'apollo-events' ); ?></label>
				<textarea id="apollo_dj_slots" name="_event_dj_slots" rows="4" style="width:100%;font-family:monospace;font-size:12px;" placeholder='[{"dj_id":123,"start_time":"23:00","end_time":"01:00","label":"Main Stage"}]'><?php echo esc_textarea( is_array( $dj_slots ) ? wp_json_encode( $dj_slots, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE ) : '' ); ?></textarea>
				<p class="description"><?php esc_html_e( 'Array JSON de objetos: {dj_id, start_time, end_time, label}', 'apollo-events' ); ?></p>
			</div>

			<!-- V2 Fields -->
			<div class="apollo-metabox-field apollo-metabox-full">
				<label for="apollo_video_url"><?php esc_html_e( 'Vídeo Promocional (URL YouTube/MP4)', 'apollo-events' ); ?></label>
				<input type="url" id="apollo_video_url" name="_event_video_url" value="<?php echo esc_url( $video_url ); ?>" placeholder="https://youtube.com/watch?v=...">
			</div>
			<div class="apollo-metabox-field apollo-metabox-full">
				<label for="apollo_gallery"><?php esc_html_e( 'Galeria (IDs de imagem separados por vírgula, máx. 3)', 'apollo-events' ); ?></label>
				<input type="text" id="apollo_gallery" name="_event_gallery" value="<?php echo esc_attr( is_array( $gallery ) ? implode( ',', $gallery ) : '' ); ?>" placeholder="100,101,102">
			</div>
			<div class="apollo-metabox-field">
				<label for="apollo_coupon_code"><?php esc_html_e( 'Código de Cupom', 'apollo-events' ); ?></label>
				<input type="text" id="apollo_coupon_code" name="_event_coupon_code" value="<?php echo esc_attr( $coupon_code ); ?>" placeholder="APOLLO20">
			</div>
			<div class="apollo-metabox-field">
				<label for="apollo_list_url"><?php esc_html_e( 'URL Lista Amiga', 'apollo-events' ); ?></label>
				<input type="url" id="apollo_list_url" name="_event_list_url" value="<?php echo esc_url( $list_url ); ?>" placeholder="https://...">
			</div>
		</div>
		<?php
	}

	/**
	 * Salva metabox com nonce check e capability check
	 *
	 * @param int      $post_id ID do post.
	 * @param \WP_Post $post    Objeto do post.
	 */
	public function save_metabox( int $post_id, \WP_Post $post ): void {
		// Verificação de segurança
		if (
			! isset( $_POST['apollo_event_nonce'] ) ||
			! wp_verify_nonce( $_POST['apollo_event_nonce'], 'apollo_event_metabox' )
		) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Campos de texto simples
		$text_fields = array(
			'_event_start_date',
			'_event_end_date',
			'_event_start_time',
			'_event_end_time',
			'_event_ticket_price',
			'_event_privacy',
			'_event_status',
		);

		foreach ( $text_fields as $key ) {
			if ( isset( $_POST[ $key ] ) ) {
				update_post_meta( $post_id, $key, sanitize_text_field( $_POST[ $key ] ) );
			}
		}

		// URL
		if ( isset( $_POST['_event_ticket_url'] ) ) {
			update_post_meta( $post_id, '_event_ticket_url', esc_url_raw( $_POST['_event_ticket_url'] ) );
		}

		// V2 URLs
		foreach ( array( '_event_video_url', '_event_list_url' ) as $key ) {
			if ( isset( $_POST[ $key ] ) ) {
				update_post_meta( $post_id, $key, esc_url_raw( $_POST[ $key ] ) );
			}
		}

		// V2 Coupon code
		if ( isset( $_POST['_event_coupon_code'] ) ) {
			update_post_meta( $post_id, '_event_coupon_code', sanitize_text_field( $_POST['_event_coupon_code'] ) );
		}

		// V2 Gallery (comma-separated → array)
		if ( isset( $_POST['_event_gallery'] ) ) {
			$raw = sanitize_text_field( $_POST['_event_gallery'] );
			$ids = array_filter( array_map( 'absint', explode( ',', $raw ) ) );
			$ids = array_slice( $ids, 0, 3 ); // máx. 3 imagens
			update_post_meta( $post_id, '_event_gallery', $ids );
		}

		// Integers
		foreach ( array( '_event_loc_id', '_event_banner' ) as $key ) {
			if ( isset( $_POST[ $key ] ) ) {
				update_post_meta( $post_id, $key, absint( $_POST[ $key ] ) );
			}
		}

		// DJ IDs (comma-separated → array)
		if ( isset( $_POST['_event_dj_ids'] ) ) {
			$raw = sanitize_text_field( $_POST['_event_dj_ids'] );
			$ids = array_filter( array_map( 'absint', explode( ',', $raw ) ) );
			update_post_meta( $post_id, '_event_dj_ids', $ids );
		}

		// DJ Slots (JSON → array of objects)
		if ( isset( $_POST['_event_dj_slots'] ) ) {
			$raw     = wp_unslash( $_POST['_event_dj_slots'] );
			$decoded = json_decode( $raw, true );
			if ( is_array( $decoded ) ) {
				$slots = array();
				foreach ( $decoded as $slot ) {
					if ( ! is_array( $slot ) ) {
						continue;
					}
					$slots[] = array(
						'dj_id'      => absint( $slot['dj_id'] ?? 0 ),
						'start_time' => sanitize_text_field( $slot['start_time'] ?? '' ),
						'end_time'   => sanitize_text_field( $slot['end_time'] ?? '' ),
						'label'      => sanitize_text_field( $slot['label'] ?? '' ),
					);
				}
				update_post_meta( $post_id, '_event_dj_slots', $slots );
			}
		}

		// Resetar flag de gone quando editado
		delete_post_meta( $post_id, '_event_is_gone' );

		/**
		 * Hook após salvar detalhes do evento
		 *
		 * @param int      $post_id ID do evento.
		 * @param \WP_Post $post    Objeto do post.
		 */
		do_action( 'apollo_event_after_save', $post_id, $post );
	}

	/**
	 * Colunas customizadas no admin
	 */
	public function admin_columns( array $columns ): array {
		$new = array();
		foreach ( $columns as $key => $label ) {
			$new[ $key ] = $label;
			if ( 'title' === $key ) {
				$new['event_date']   = __( 'Data', 'apollo-events' );
				$new['event_loc']    = __( 'Local', 'apollo-events' );
				$new['event_status'] = __( 'Status', 'apollo-events' );
			}
		}
		return $new;
	}

	/**
	 * Conteúdo das colunas customizadas
	 */
	public function admin_column_content( string $column, int $post_id ): void {
		switch ( $column ) {
			case 'event_date':
				$date = get_post_meta( $post_id, '_event_start_date', true );
				$time = get_post_meta( $post_id, '_event_start_time', true );
				echo esc_html( $date ? $date . ( $time ? ' ' . $time : '' ) : '—' );
				break;

			case 'event_loc':
				$loc = apollo_event_get_loc( $post_id );
				echo esc_html( $loc ? $loc['title'] : '—' );
				break;

			case 'event_status':
				$status = get_post_meta( $post_id, '_event_status', true ) ?: 'scheduled';
				$gone   = apollo_event_is_gone( $post_id );
				if ( $gone ) {
					echo '<span style="color:#999;">⏰ ' . esc_html__( 'Gone', 'apollo-events' ) . '</span>';
				} else {
					echo esc_html( ucfirst( $status ) );
				}
				break;
		}
	}

	/**
	 * Colunas ordenáveis
	 */
	public function sortable_columns( array $columns ): array {
		$columns['event_date'] = '_event_start_date';
		return $columns;
	}
}
