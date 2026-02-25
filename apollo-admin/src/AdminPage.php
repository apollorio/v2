<?php

/**
 * Apollo Admin Page — registers menu + renders the tabbed settings UI.
 *
 * One tab per Apollo plugin, grouped by layer.
 *
 * @package Apollo\Admin
 */

declare(strict_types=1);

namespace Apollo\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class AdminPage {


	private static ?AdminPage $instance = null;

	public static function get_instance(): AdminPage {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {}

	public function init(): void {
		add_action( 'admin_menu', array( $this, 'register_menu' ) );
		add_action( 'admin_init', array( $this, 'handle_save' ) );

		// CPanel save handlers (AJAX + admin-post fallback)
		add_action( 'wp_ajax_apollo_cpanel_save', array( $this, 'handle_cpanel_save' ) );
		add_action( 'admin_post_apollo_cpanel_save', array( $this, 'handle_cpanel_save' ) );

		// Draft creation handler (luxury modal form)
		add_action( 'wp_ajax_apollo_admin_create_draft', array( $this, 'handle_create_draft' ) );
	}

	/* ─────────────────────────── Menu ──────────────────────────── */

	public function register_menu(): void {
		add_menu_page(
			__( 'Apollo Settings', 'apollo-admin' ),
			__( 'Apollo', 'apollo-admin' ),
			'manage_options',
			'apollo',
			array( $this, 'render_page' ),
			'dashicons-superhero-alt',
			3
		);
	}

	/* ─────────────────────────── Tab Helpers ───────────────────── */

	/**
	 * Build the ordered list of tabs.
	 * First tab = _status (overview), then _global, then each plugin grouped by layer.
	 *
	 * @return array<string, array{slug:string,name:string,icon:string,layer:string,layer_name:string,installed:bool,active:bool}>
	 */
	public function get_tabs(): array {
		$tabs = array();

		// Special "_status" tab — overview of all plugins
		$tabs['_status'] = array(
			'slug'       => '_status',
			'name'       => 'Status Overview',
			'icon'       => 'dashicons-dashboard',
			'layer'      => 'L0',
			'layer_name' => 'Visão Geral',
			'installed'  => true,
			'active'     => true,
		);

		// Special "_global" tab always second
		$tabs['_global'] = array(
			'slug'       => '_global',
			'name'       => 'Global',
			'icon'       => 'dashicons-admin-site-alt3',
			'layer'      => 'L0',
			'layer_name' => 'Geral',
			'installed'  => true,
			'active'     => true,
		);

		$registry = Registry::get_instance();
		$manifest = Registry::get_registry_manifest();

		// Sort by layer then name
		$sorted = $manifest;
		uasort(
			$sorted,
			function ( $a, $b ) {
				$cmp = strcmp( $a['layer'], $b['layer'] );
				return $cmp !== 0 ? $cmp : strcmp( $a['name'], $b['name'] );
			}
		);

		foreach ( $sorted as $slug => $meta ) {
			$info          = $registry->get( $slug );
			$tabs[ $slug ] = array(
				'slug'       => $slug,
				'name'       => $meta['name'],
				'icon'       => $meta['icon'],
				'layer'      => $meta['layer'],
				'layer_name' => $meta['layer_name'],
				'installed'  => $info['installed'] ?? false,
				'active'     => $info['active'] ?? false,
			);
		}

		return $tabs;
	}

	/* ─────────────────────────── Render ────────────────────────── */

	public function render_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Você não tem permissão para acessar esta página.', 'apollo-admin' ) );
		}

		// Load the CPanel dashboard template (all partials assembled)
		require APOLLO_ADMIN_DIR . 'templates/dashboard.php';
	}

	/**
	 * Render the form fields for a specific tab
	 */
	private function render_tab_content( string $slug, array $tab ): void {
		// Special render for status overview
		if ( $slug === '_status' ) {
			$this->render_status_overview();
			return;
		}

		$settings = Settings::get_instance();
		$schema   = Settings::get_schema( $slug );

		?>
		<div class="apollo-tab-header">
			<span class="dashicons <?php echo esc_attr( $tab['icon'] ); ?> apollo-tab-icon-large"></span>
			<div>
				<h2><?php echo esc_html( $tab['name'] ); ?></h2>
				<?php if ( $slug !== '_global' ) : ?>
					<p class="description">
						<?php
						$info = Registry::get_instance()->get( $slug );
						echo esc_html( $info['description'] ?? '' );
						if ( ! empty( $info['version'] ) && $info['version'] !== '—' ) {
							echo ' — <strong>v' . esc_html( $info['version'] ) . '</strong>';
						}
						?>
					</p>
				<?php endif; ?>
			</div>
		</div>

		<?php if ( $slug !== '_global' && ! ( $tab['installed'] ?? false ) ) : ?>
			<div class="apollo-notice warning">
				<span class="dashicons dashicons-warning"></span>
				<?php esc_html_e( 'Este plugin não está instalado. As configurações serão salvas mas não terão efeito até que o plugin seja instalado e ativado.', 'apollo-admin' ); ?>
			</div>
		<?php endif; ?>

		<?php if ( empty( $schema ) ) : ?>
			<div class="apollo-notice info">
				<span class="dashicons dashicons-info"></span>
				<?php esc_html_e( 'Nenhuma configuração disponível para este plugin ainda. Os campos de configuração serão adicionados conforme o plugin for desenvolvido.', 'apollo-admin' ); ?>
			</div>
			<?php return; ?>
		<?php endif; ?>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=apollo&tab=' . $slug ) ); ?>">
			<?php wp_nonce_field( 'apollo_admin_save_' . $slug, 'apollo_admin_nonce' ); ?>
			<input type="hidden" name="apollo_admin_tab" value="<?php echo esc_attr( $slug ); ?>">

			<table class="form-table apollo-settings-table" role="presentation">
				<tbody>
					<?php
					foreach ( $schema as $key => $field ) :
						$value    = $settings->get( $slug, $key, $field['default'] );
						$field_id = 'apollo_' . $slug . '_' . $key;
						?>
						<tr>
							<th scope="row">
								<label for="<?php echo esc_attr( $field_id ); ?>">
									<?php echo esc_html( $field['label'] ); ?>
								</label>
							</th>
							<td>
								<?php $this->render_field( $field_id, $key, $field, $value ); ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>

			<?php submit_button( __( 'Salvar Configurações', 'apollo-admin' ), 'primary apollo-save-btn', 'apollo_admin_submit' ); ?>
		</form>
		<?php
	}

	/**
	 * Render a single form field based on its type
	 */
	private function render_field( string $id, string $key, array $field, mixed $value ): void {
		$name = 'apollo_settings[' . esc_attr( $key ) . ']';

		switch ( $field['type'] ) {

			case 'text':
			case 'email':
				printf(
					'<input type="%s" id="%s" name="%s" value="%s" class="regular-text apollo-field" />',
					esc_attr( $field['type'] ),
					esc_attr( $id ),
					esc_attr( $name ),
					esc_attr( (string) $value )
				);
				break;

			case 'number':
				printf(
					'<input type="number" id="%s" name="%s" value="%s" class="small-text apollo-field" />',
					esc_attr( $id ),
					esc_attr( $name ),
					esc_attr( (string) $value )
				);
				break;

			case 'color':
				printf(
					'<input type="color" id="%s" name="%s" value="%s" class="apollo-color-field" />',
					esc_attr( $id ),
					esc_attr( $name ),
					esc_attr( (string) $value )
				);
				break;

			case 'toggle':
				$checked = filter_var( $value, FILTER_VALIDATE_BOOLEAN );
				printf(
					'<label class="apollo-toggle">
						<input type="hidden" name="%s" value="0" />
						<input type="checkbox" id="%s" name="%s" value="1" %s />
						<span class="apollo-toggle-slider"></span>
					</label>',
					esc_attr( $name ),
					esc_attr( $id ),
					esc_attr( $name ),
					checked( $checked, true, false )
				);
				break;

			case 'select':
				printf( '<select id="%s" name="%s" class="apollo-field">', esc_attr( $id ), esc_attr( $name ) );
				foreach ( ( $field['options'] ?? array() ) as $opt_val => $opt_label ) {
					printf(
						'<option value="%s" %s>%s</option>',
						esc_attr( $opt_val ),
						selected( $value, $opt_val, false ),
						esc_html( $opt_label )
					);
				}
				echo '</select>';
				break;

			case 'textarea':
				printf(
					'<textarea id="%s" name="%s" rows="5" class="large-text apollo-field">%s</textarea>',
					esc_attr( $id ),
					esc_attr( $name ),
					esc_textarea( (string) $value )
				);
				break;
		}
	}

	/**
	 * Render the status overview grid showing all plugins
	 */
	private function render_status_overview(): void {
		$registry = Registry::get_instance();
		$manifest = Registry::get_registry_manifest();

		// Group plugins by layer
		$layers = array();
		foreach ( $manifest as $slug => $meta ) {
			$layer = $meta['layer'];
			if ( ! isset( $layers[ $layer ] ) ) {
				$layers[ $layer ] = array(
					'name'    => $meta['layer_name'],
					'plugins' => array(),
				);
			}
			$info                                 = $registry->get( $slug );
			$layers[ $layer ]['plugins'][ $slug ] = array_merge( $meta, $info );
		}

		// Sort layers
		ksort( $layers );

		// Calculate stats
		$total     = count( $manifest );
		$installed = 0;
		$active    = 0;
		$missing   = 0;
		foreach ( $manifest as $slug => $meta ) {
			$info = $registry->get( $slug );
			if ( $info['active'] ?? false ) {
				++$active;
			} elseif ( $info['installed'] ?? false ) {
				++$installed;
			} else {
				++$missing;
			}
		}
		$compliance = $total > 0 ? round( ( $active / $total ) * 100, 1 ) : 0;

		?>
		<div class="apollo-status-overview">

			<div class="apollo-tab-header">
				<span class="dashicons dashicons-dashboard apollo-tab-icon-large"></span>
				<div>
					<h2><?php esc_html_e( 'Status Overview', 'apollo-admin' ); ?></h2>
					<p class="description">
						<?php esc_html_e( 'Visão geral de todos os plugins Apollo — instalados, ativos, e faltantes.', 'apollo-admin' ); ?>
					</p>
				</div>
			</div>

			<!-- Stats Summary -->
			<div class="apollo-stats-grid">
				<div class="apollo-stat-card">
					<div class="apollo-stat-value"><?php echo esc_html( $total ); ?></div>
					<div class="apollo-stat-label"><?php esc_html_e( 'Total Plugins', 'apollo-admin' ); ?></div>
				</div>
				<div class="apollo-stat-card active">
					<div class="apollo-stat-value"><?php echo esc_html( $active ); ?></div>
					<div class="apollo-stat-label"><?php esc_html_e( 'Ativos', 'apollo-admin' ); ?></div>
				</div>
				<div class="apollo-stat-card installed">
					<div class="apollo-stat-value"><?php echo esc_html( $installed ); ?></div>
					<div class="apollo-stat-label"><?php esc_html_e( 'Instalados', 'apollo-admin' ); ?></div>
				</div>
				<div class="apollo-stat-card missing">
					<div class="apollo-stat-value"><?php echo esc_html( $missing ); ?></div>
					<div class="apollo-stat-label"><?php esc_html_e( 'Faltando', 'apollo-admin' ); ?></div>
				</div>
				<div class="apollo-stat-card compliance">
					<div class="apollo-stat-value"><?php echo esc_html( $compliance . '%' ); ?></div>
					<div class="apollo-stat-label"><?php esc_html_e( 'Compliance', 'apollo-admin' ); ?></div>
				</div>
			</div>

			<!-- Plugins by Layer -->
			<?php foreach ( $layers as $layer_key => $layer_data ) : ?>
				<div class="apollo-layer-section">
					<h3 class="apollo-layer-title">
						<span class="apollo-layer-badge"><?php echo esc_html( $layer_key ); ?></span>
						<?php echo esc_html( $layer_data['name'] ); ?>
						<span class="apollo-layer-count"><?php echo esc_html( count( $layer_data['plugins'] ) ); ?> plugins</span>
					</h3>

					<div class="apollo-plugins-grid">
						<?php foreach ( $layer_data['plugins'] as $slug => $plugin ) : ?>
							<div class="apollo-plugin-card <?php echo ( $plugin['active'] ?? false ) ? 'active' : ( ( $plugin['installed'] ?? false ) ? 'installed' : 'missing' ); ?>">
								<div class="apollo-plugin-header">
									<span class="dashicons <?php echo esc_attr( $plugin['icon'] ); ?> apollo-plugin-icon"></span>
									<div class="apollo-plugin-info">
										<h4 class="apollo-plugin-name">
											<a href="<?php echo esc_url( admin_url( 'admin.php?page=apollo&tab=' . $slug ) ); ?>">
												<?php echo esc_html( $plugin['name'] ); ?>
											</a>
										</h4>
										<div class="apollo-plugin-meta">
											<span class="apollo-plugin-slug"><?php echo esc_html( $slug ); ?></span>
											<?php if ( ! empty( $plugin['version'] ) && $plugin['version'] !== '—' ) : ?>
												<span class="apollo-plugin-version">v<?php echo esc_html( $plugin['version'] ); ?></span>
											<?php endif; ?>
										</div>
									</div>
									<div class="apollo-plugin-status">
										<?php if ( $plugin['active'] ?? false ) : ?>
											<span class="apollo-status-badge active">
												<span class="dashicons dashicons-yes-alt"></span> Ativo
											</span>
										<?php elseif ( $plugin['installed'] ?? false ) : ?>
											<span class="apollo-status-badge installed">
												<span class="dashicons dashicons-download"></span> Instalado
											</span>
										<?php else : ?>
											<span class="apollo-status-badge missing">
												<span class="dashicons dashicons-warning"></span> Faltando
											</span>
										<?php endif; ?>
									</div>
								</div>
								<p class="apollo-plugin-description">
									<?php echo esc_html( $plugin['description'] ?? '' ); ?>
								</p>
								<?php if ( ! empty( $plugin['file'] ) ) : ?>
									<div class="apollo-plugin-file">
										<span class="dashicons dashicons-media-code"></span>
										<code><?php echo esc_html( basename( $plugin['file'] ) ); ?></code>
									</div>
								<?php endif; ?>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endforeach; ?>

		</div>
		<?php
	}

	/* ─────────────────────────── CPanel Save Handler ──────────── */

	/**
	 * Handle save from the new CPanel dashboard (AJAX + admin-post).
	 *
	 * All fields use name="apollo[key]" so $_POST['apollo'] is a flat key→value array.
	 * Values are sanitized by type inference (boolean-like, numeric, email, hex color, or text).
	 */
	public function handle_cpanel_save(): void {
		// Verify nonce
		if (
			! isset( $_POST['apollo_cpanel_nonce'] ) ||
			! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['apollo_cpanel_nonce'] ) ), 'apollo_cpanel_save' )
		) {
			if ( wp_doing_ajax() ) {
				wp_send_json_error( array( 'message' => 'Invalid nonce.' ), 403 );
			}
			wp_die( esc_html__( 'Nonce inválido.', 'apollo-admin' ) );
		}

		// Verify capabilities
		if ( ! current_user_can( 'manage_options' ) ) {
			if ( wp_doing_ajax() ) {
				wp_send_json_error( array( 'message' => 'No permission.' ), 403 );
			}
			wp_die( esc_html__( 'Sem permissão.', 'apollo-admin' ) );
		}

		$raw = isset( $_POST['apollo'] ) ? wp_unslash( $_POST['apollo'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		if ( ! is_array( $raw ) ) {
			$raw = array();
		}

		// Load existing settings so unchecked checkboxes don't lose their keys
		$existing = get_option( APOLLO_ADMIN_OPTION_KEY, array() );
		if ( ! is_array( $existing ) ) {
			$existing = array();
		}

		// Reset all existing boolean (0/1) keys to 0 — then POST data overrides to 1
		foreach ( $existing as $k => $v ) {
			if ( $v === 1 || $v === 0 ) {
				$existing[ $k ] = 0;
			}
		}

		$sanitized = array();
		foreach ( $raw as $key => $value ) {
			$safe_key = sanitize_key( $key );
			if ( empty( $safe_key ) ) {
				continue;
			}

			if ( is_array( $value ) ) {
				// Sub-arrays (e.g., custom fields) — sanitize each element
				$sanitized[ $safe_key ] = array_map( 'sanitize_text_field', $value );
			} elseif ( in_array( $value, array( '0', '1' ), true ) ) {
				// Boolean toggle
				$sanitized[ $safe_key ] = (int) $value;
			} elseif ( is_numeric( $value ) ) {
				$sanitized[ $safe_key ] = is_float( $value + 0 ) ? (float) $value : (int) $value;
			} elseif ( preg_match( '/^#[0-9a-f]{3,8}$/i', $value ) ) {
				$sanitized[ $safe_key ] = sanitize_hex_color( $value ) ?: '';
			} elseif ( is_email( $value ) ) {
				$sanitized[ $safe_key ] = sanitize_email( $value );
			} elseif ( filter_var( $value, FILTER_VALIDATE_URL ) ) {
				$sanitized[ $safe_key ] = esc_url_raw( $value );
			} else {
				$sanitized[ $safe_key ] = sanitize_text_field( (string) $value );
			}
		}

		// Merge: existing (with booleans reset to 0) + new sanitized values
		$merged = array_merge( $existing, $sanitized );

		// Save all settings as a single serialized option
		update_option( APOLLO_ADMIN_OPTION_KEY, $merged );

		/**
		 * Fires after CPanel settings are saved.
		 *
		 * @param array $sanitized The saved settings array.
		 */
		do_action( 'apollo/admin/settings_saved', $merged );

		if ( wp_doing_ajax() ) {
			wp_send_json_success( array( 'message' => 'Settings saved.' ) );
		}

		// Non-AJAX: redirect back to admin page
		wp_safe_redirect( admin_url( 'admin.php?page=apollo&settings-updated=1' ) );
		exit;
	}

	/* ─────────────────────────── Draft Creation Handler ──────────── */

	/**
	 * Handle CPT draft creation from the luxury modal form.
	 *
	 * Expects:
	 *   - action:          apollo_admin_create_draft
	 *   - _wpnonce:        nonce for 'apollo_admin_create_draft'
	 *   - apollo_form_id:  form identifier (new-event, new-dj, etc.)
	 *   - apollo_cpt:      CPT slug (event, dj, hub, local, classified, or empty for report)
	 *   - title:           post_title
	 *   - content:         post_content (optional)
	 *   - _event_*, _dj_*, _classified_*, _local_*: meta keys
	 */
	public function handle_create_draft(): void {
		// Verify nonce
		if (
			! isset( $_POST['_wpnonce'] ) ||
			! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'apollo_admin_create_draft' )
		) {
			wp_send_json_error( array( 'message' => 'Invalid nonce.' ), 403 );
		}

		// Verify capabilities
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => 'No permission.' ), 403 );
		}

		$form_id = isset( $_POST['apollo_form_id'] ) ? sanitize_key( $_POST['apollo_form_id'] ) : '';
		$cpt     = isset( $_POST['apollo_cpt'] ) ? sanitize_key( $_POST['apollo_cpt'] ) : '';

		// ── Report form (no CPT — just log / email) ──
		if ( $form_id === 'report' || empty( $cpt ) ) {
			$report_data = array(
				'name'    => isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '',
				'email'   => isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '',
				'subject' => isset( $_POST['subject'] ) ? sanitize_text_field( wp_unslash( $_POST['subject'] ) ) : '',
				'message' => isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '',
			);

			/**
			 * Fires when a report form is submitted from the CPanel.
			 *
			 * @param array $report_data Sanitized report data.
			 */
			do_action( 'apollo/admin/report_submitted', $report_data );

			wp_send_json_success( array( 'message' => 'Report submitted.' ) );
		}

		// ── Allowed CPTs ──
		$allowed_cpts = array( 'event', 'dj', 'hub', 'local', 'classified' );
		if ( ! in_array( $cpt, $allowed_cpts, true ) ) {
			wp_send_json_error( array( 'message' => 'Invalid CPT: ' . $cpt ), 400 );
		}

		// ── Build post data ──
		$post_title   = isset( $_POST['title'] ) ? sanitize_text_field( wp_unslash( $_POST['title'] ) ) : '';
		$post_content = isset( $_POST['content'] ) ? wp_kses_post( wp_unslash( $_POST['content'] ) ) : '';

		if ( empty( $post_title ) ) {
			wp_send_json_error( array( 'message' => 'Title is required.' ), 400 );
		}

		$post_id = wp_insert_post(
			array(
				'post_type'    => $cpt,
				'post_title'   => $post_title,
				'post_content' => $post_content,
				'post_status'  => 'draft',
				'post_author'  => get_current_user_id(),
			),
			true
		);

		if ( is_wp_error( $post_id ) ) {
			wp_send_json_error( array( 'message' => $post_id->get_error_message() ), 500 );
		}

		// ── Save meta keys (prefix-based sanitization) ──
		$meta_sanitizers = array(
			// Event meta
			'_event_start_date'      => 'sanitize_text_field',
			'_event_end_date'        => 'sanitize_text_field',
			'_event_start_time'      => 'sanitize_text_field',
			'_event_end_time'        => 'sanitize_text_field',
			'_event_loc_id'          => 'absint',
			'_event_ticket_url'      => 'esc_url_raw',
			'_event_ticket_price'    => 'sanitize_text_field',
			'_event_coupon_code'     => 'sanitize_text_field',
			'_event_list_url'        => 'esc_url_raw',
			'_event_video_url'       => 'esc_url_raw',
			'_event_privacy'         => 'sanitize_key',
			'_event_status'          => 'sanitize_key',
			// DJ meta
			'_dj_bio_short'          => 'sanitize_text_field',
			'_dj_instagram'          => 'sanitize_text_field',
			'_dj_soundcloud'         => 'esc_url_raw',
			'_dj_spotify'            => 'esc_url_raw',
			'_dj_youtube'            => 'esc_url_raw',
			'_dj_mixcloud'           => 'esc_url_raw',
			'_dj_website'            => 'esc_url_raw',
			'_dj_user_id'            => 'absint',
			'_dj_verified'           => 'absint',
			// Classified meta
			'_classified_price'      => 'sanitize_text_field',
			'_classified_currency'   => 'sanitize_text_field',
			'_classified_negotiable' => 'absint',
			'_classified_condition'  => 'sanitize_key',
			// Local meta
			'_local_address'         => 'sanitize_text_field',
			'_local_city'            => 'sanitize_text_field',
			'_local_lat'             => 'sanitize_text_field',
			'_local_lng'             => 'sanitize_text_field',
			'_local_capacity'        => 'absint',
			'_local_phone'           => 'sanitize_text_field',
			'_local_instagram'       => 'sanitize_text_field',
			'_local_website'         => 'esc_url_raw',
			'_local_price_range'     => 'sanitize_text_field',
		);

		foreach ( $meta_sanitizers as $meta_key => $sanitize_fn ) {
			if ( isset( $_POST[ $meta_key ] ) && $_POST[ $meta_key ] !== '' ) {
				$raw_value = wp_unslash( $_POST[ $meta_key ] );
				$clean     = call_user_func( $sanitize_fn, $raw_value );
				update_post_meta( $post_id, $meta_key, $clean );
			}
		}

		/**
		 * Fires after a draft CPT post is created from the CPanel modal.
		 *
		 * @param int    $post_id  The new post ID.
		 * @param string $cpt      The CPT slug.
		 * @param string $form_id  The form identifier.
		 */
		do_action( 'apollo/admin/draft_created', $post_id, $cpt, $form_id );

		wp_send_json_success(
			array(
				'message'  => 'Draft created.',
				'post_id'  => $post_id,
				'cpt'      => $cpt,
				'edit_url' => get_edit_post_link( $post_id, 'raw' ),
			)
		);
	}

	/* ─────────────────────────── Legacy Save Handler ──────────── */

	public function handle_save(): void {
		if ( ! isset( $_POST['apollo_admin_submit'] ) ) {
			return;
		}
		if ( ! isset( $_POST['apollo_admin_nonce'] ) ) {
			return;
		}

		$tab = isset( $_POST['apollo_admin_tab'] ) ? sanitize_key( $_POST['apollo_admin_tab'] ) : '';
		if ( empty( $tab ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_POST['apollo_admin_nonce'], 'apollo_admin_save_' . $tab ) ) {
			wp_die( esc_html__( 'Nonce inválido.', 'apollo-admin' ) );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Sem permissão.', 'apollo-admin' ) );
		}

		$raw_settings = isset( $_POST['apollo_settings'] ) ? wp_unslash( $_POST['apollo_settings'] ) : array();
		if ( ! is_array( $raw_settings ) ) {
			$raw_settings = array();
		}

		$schema    = Settings::get_schema( $tab );
		$sanitized = array();

		foreach ( $schema as $key => $field ) {
			$raw = $raw_settings[ $key ] ?? $field['default'];

			switch ( $field['type'] ) {
				case 'toggle':
					$sanitized[ $key ] = filter_var( $raw, FILTER_VALIDATE_BOOLEAN );
					break;
				case 'number':
					$sanitized[ $key ] = (int) $raw;
					break;
				case 'email':
					$sanitized[ $key ] = sanitize_email( (string) $raw );
					break;
				case 'color':
					$sanitized[ $key ] = sanitize_hex_color( (string) $raw ) ?: $field['default'];
					break;
				case 'textarea':
					$sanitized[ $key ] = sanitize_textarea_field( (string) $raw );
					break;
				default:
					$sanitized[ $key ] = sanitize_text_field( (string) $raw );
			}
		}

		Settings::get_instance()->replace_plugin( $tab, $sanitized );

		wp_safe_redirect( admin_url( 'admin.php?page=apollo&tab=' . $tab . '&settings-updated=1' ) );
		exit;
	}
}
