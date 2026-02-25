<?php

/**
 * Register the email_aprio CPT.
 *
 * @package Apollo\Email\Core
 * @since   1.0.0
 */

declare(strict_types=1);

namespace Apollo\Email\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CPT {

	/**
	 * Register the email_aprio custom post type.
	 */
	public function register(): void {
		// If already registered by apollo-core fallback, skip
		if ( post_type_exists( 'email_aprio' ) ) {
			$this->addMetaBoxes();
			return;
		}

		register_post_type(
			'email_aprio',
			array(
				'labels'          => array(
					'name'               => __( 'Email Templates', 'apollo-email' ),
					'singular_name'      => __( 'Email Template', 'apollo-email' ),
					'add_new'            => __( 'Novo Template', 'apollo-email' ),
					'add_new_item'       => __( 'Adicionar Template', 'apollo-email' ),
					'edit_item'          => __( 'Editar Template', 'apollo-email' ),
					'new_item'           => __( 'Novo Template', 'apollo-email' ),
					'view_item'          => __( 'Ver Template', 'apollo-email' ),
					'search_items'       => __( 'Buscar Templates', 'apollo-email' ),
					'not_found'          => __( 'Nenhum template encontrado', 'apollo-email' ),
					'not_found_in_trash' => __( 'Nenhum template na lixeira', 'apollo-email' ),
					'menu_name'          => __( 'Email Templates', 'apollo-email' ),
				),
				'public'          => false,
				'show_ui'         => true,
				'show_in_menu'    => false, // Managed via AdminPage
				'show_in_rest'    => true,
				'rest_base'       => 'email-templates',
				'rest_namespace'  => 'apollo/v1',
				'supports'        => array( 'title', 'editor' ),
				'has_archive'     => false,
				'rewrite'         => false,
				'capability_type' => 'post',
				'map_meta_cap'    => true,
			)
		);

		$this->addMetaBoxes();
	}

	/**
	 * Register meta boxes for email template editing.
	 */
	private function addMetaBoxes(): void {
		add_action(
			'add_meta_boxes',
			function () {
				add_meta_box(
					'apollo_email_template_meta',
					__( 'Configurações do Template', 'apollo-email' ),
					array( $this, 'renderMetaBox' ),
					'email_aprio',
					'side',
					'high'
				);

				add_meta_box(
					'apollo_email_template_preview',
					__( 'Preview', 'apollo-email' ),
					array( $this, 'renderPreviewBox' ),
					'email_aprio',
					'normal',
					'low'
				);
			}
		);

		add_action( 'save_post_email_aprio', array( $this, 'saveMetaBox' ), 10, 2 );
	}

	/**
	 * Render the template settings meta box.
	 */
	public function renderMetaBox( \WP_Post $post ): void {
		$subject   = get_post_meta( $post->ID, '_email_subject', true );
		$type      = get_post_meta( $post->ID, '_email_type', true ) ?: 'transactional';
		$variables = get_post_meta( $post->ID, '_email_variables', true );
		if ( ! is_array( $variables ) ) {
			$variables = array();
		}

		wp_nonce_field( 'apollo_email_meta', '_apollo_email_meta_nonce' );
		?>
		<p>
			<label for="email_subject"><strong><?php esc_html_e( 'Assunto:', 'apollo-email' ); ?></strong></label>
			<input type="text" id="email_subject" name="_email_subject" value="<?php echo esc_attr( $subject ); ?>" class="widefat" placeholder="Ex: Bem-vindo(a) ao {{site_name}}">
			<small><?php esc_html_e( 'Use {{variavel}} para merge tags', 'apollo-email' ); ?></small>
		</p>
		<p>
			<label for="email_type"><strong><?php esc_html_e( 'Tipo:', 'apollo-email' ); ?></strong></label>
			<select id="email_type" name="_email_type" class="widefat">
				<option value="transactional" <?php selected( $type, 'transactional' ); ?>><?php esc_html_e( 'Transacional', 'apollo-email' ); ?></option>
				<option value="marketing" <?php selected( $type, 'marketing' ); ?>><?php esc_html_e( 'Marketing', 'apollo-email' ); ?></option>
				<option value="digest" <?php selected( $type, 'digest' ); ?>><?php esc_html_e( 'Resumo', 'apollo-email' ); ?></option>
			</select>
		</p>
		<p>
			<label for="email_variables"><strong><?php esc_html_e( 'Variáveis disponíveis:', 'apollo-email' ); ?></strong></label>
			<textarea id="email_variables" name="_email_variables" class="widefat" rows="3" placeholder="user_name, site_name, action_url"><?php echo esc_textarea( implode( ', ', $variables ) ); ?></textarea>
			<small><?php esc_html_e( 'Separadas por vírgula', 'apollo-email' ); ?></small>
		</p>
		<?php
	}

	/**
	 * Render the preview meta box.
	 */
	public function renderPreviewBox( \WP_Post $post ): void {
		?>
		<div id="apollo-email-preview-container">
			<button type="button" class="button" id="apollo-email-preview-btn">
				<span class="dashicons dashicons-visibility"></span>
				<?php esc_html_e( 'Gerar Preview', 'apollo-email' ); ?>
			</button>
			<div id="apollo-email-preview-frame" style="margin-top: 10px; border: 1px solid #ddd; border-radius: 4px; display: none;">
				<iframe id="apollo-email-preview-iframe" style="width: 100%; height: 500px; border: 0;"></iframe>
			</div>
		</div>
		<?php
	}

	/**
	 * Save meta box data.
	 */
	public function saveMetaBox( int $post_id, \WP_Post $post ): void {
		if ( ! isset( $_POST['_apollo_email_meta_nonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_apollo_email_meta_nonce'] ) ), 'apollo_email_meta' ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Subject
		if ( isset( $_POST['_email_subject'] ) ) {
			update_post_meta( $post_id, '_email_subject', sanitize_text_field( wp_unslash( $_POST['_email_subject'] ) ) );
		}

		// Type
		if ( isset( $_POST['_email_type'] ) ) {
			$type = sanitize_text_field( wp_unslash( $_POST['_email_type'] ) );
			if ( in_array( $type, array( 'transactional', 'marketing', 'digest' ), true ) ) {
				update_post_meta( $post_id, '_email_type', $type );
			}
		}

		// Variables
		if ( isset( $_POST['_email_variables'] ) ) {
			$raw  = sanitize_text_field( wp_unslash( $_POST['_email_variables'] ) );
			$vars = array_map( 'trim', explode( ',', $raw ) );
			$vars = array_filter( $vars );
			update_post_meta( $post_id, '_email_variables', array_values( $vars ) );
		}
	}
}
