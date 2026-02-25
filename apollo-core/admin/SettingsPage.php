<?php
/**
 * Apollo Core - Admin Settings Page
 *
 * Settings page for Apollo Core configuration including
 * the uninstall data deletion option.
 *
 * @package Apollo\Core\Admin
 * @since 6.0.0
 */

declare(strict_types=1);

namespace Apollo\Core\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Settings Page
 */
class SettingsPage {

	/**
	 * Menu slug
	 */
	const MENU_SLUG = 'apollo-settings';

	/**
	 * Option group
	 */
	const OPTION_GROUP = 'apollo_settings';

	/**
	 * Initialize
	 */
	public static function init(): void {
		add_action( 'admin_menu', array( self::class, 'add_menu' ) );
		add_action( 'admin_init', array( self::class, 'register_settings' ) );
	}

	/**
	 * Add admin menu
	 */
	public static function add_menu(): void {
		add_menu_page(
			__( 'Apollo', 'apollo-core' ),
			__( 'Apollo', 'apollo-core' ),
			'manage_options',
			self::MENU_SLUG,
			array( self::class, 'render_page' ),
			'dashicons-superhero-alt',
			3
		);

		add_submenu_page(
			self::MENU_SLUG,
			__( 'Configurações', 'apollo-core' ),
			__( 'Configurações', 'apollo-core' ),
			'manage_options',
			self::MENU_SLUG,
			array( self::class, 'render_page' )
		);

		add_submenu_page(
			self::MENU_SLUG,
			__( 'Registry', 'apollo-core' ),
			__( 'Registry', 'apollo-core' ),
			'manage_options',
			'apollo-registry',
			array( self::class, 'render_registry_page' )
		);

		add_submenu_page(
			self::MENU_SLUG,
			__( 'Database', 'apollo-core' ),
			__( 'Database', 'apollo-core' ),
			'manage_options',
			'apollo-database',
			array( self::class, 'render_database_page' )
		);
	}

	/**
	 * Register settings
	 */
	public static function register_settings(): void {
		// General section
		add_settings_section(
			'apollo_general',
			__( 'Configurações Gerais', 'apollo-core' ),
			null,
			self::MENU_SLUG
		);

		// Debug mode
		register_setting(
			self::OPTION_GROUP,
			'apollo_debug_mode',
			array(
				'type'              => 'boolean',
				'sanitize_callback' => 'rest_sanitize_boolean',
				'default'           => false,
			)
		);

		add_settings_field(
			'apollo_debug_mode',
			__( 'Modo Debug', 'apollo-core' ),
			array( self::class, 'render_checkbox_field' ),
			self::MENU_SLUG,
			'apollo_general',
			array(
				'id'          => 'apollo_debug_mode',
				'description' => __( 'Habilita logs de debug detalhados.', 'apollo-core' ),
			)
		);

		// CDN enabled
		register_setting(
			self::OPTION_GROUP,
			'apollo_cdn_enabled',
			array(
				'type'              => 'boolean',
				'sanitize_callback' => 'rest_sanitize_boolean',
				'default'           => true,
			)
		);

		add_settings_field(
			'apollo_cdn_enabled',
			__( 'CDN Ativo', 'apollo-core' ),
			array( self::class, 'render_checkbox_field' ),
			self::MENU_SLUG,
			'apollo_general',
			array(
				'id'          => 'apollo_cdn_enabled',
				'description' => __( 'Usar CDN para assets estáticos.', 'apollo-core' ),
			)
		);

		// Danger zone section
		add_settings_section(
			'apollo_danger',
			__( 'Zona de Perigo', 'apollo-core' ),
			array( self::class, 'render_danger_section' ),
			self::MENU_SLUG
		);

		// Uninstall delete data
		register_setting(
			self::OPTION_GROUP,
			\Apollo\Core\UninstallHandler::OPTION_DELETE_DATA,
			array(
				'type'              => 'boolean',
				'sanitize_callback' => 'rest_sanitize_boolean',
				'default'           => false,
			)
		);

		add_settings_field(
			'apollo_uninstall_delete_data',
			__( 'Deletar Dados ao Desinstalar', 'apollo-core' ),
			array( self::class, 'render_danger_checkbox' ),
			self::MENU_SLUG,
			'apollo_danger',
			array(
				'id'          => \Apollo\Core\UninstallHandler::OPTION_DELETE_DATA,
				'description' => __( '⚠️ CUIDADO: Se marcado, TODOS os dados do Apollo serão PERMANENTEMENTE deletados quando o plugin for removido. Isso inclui todas as tabelas, posts, meta fields, taxonomias e configurações.', 'apollo-core' ),
			)
		);
	}

	/**
	 * Render danger section description
	 */
	public static function render_danger_section(): void {
		echo '<p class="description" style="color: #dc3232; font-weight: bold;">';
		echo esc_html__( 'As opções abaixo podem causar perda permanente de dados. Use com extremo cuidado.', 'apollo-core' );
		echo '</p>';
	}

	/**
	 * Render checkbox field
	 */
	public static function render_checkbox_field( array $args ): void {
		$value = get_option( $args['id'], false );
		?>
		<label>
			<input type="checkbox"
					name="<?php echo esc_attr( $args['id'] ); ?>"
					id="<?php echo esc_attr( $args['id'] ); ?>"
					value="1"
					<?php checked( $value, true ); ?>>
			<?php if ( ! empty( $args['description'] ) ) : ?>
				<span class="description"><?php echo esc_html( $args['description'] ); ?></span>
			<?php endif; ?>
		</label>
		<?php
	}

	/**
	 * Render danger checkbox (with red styling)
	 */
	public static function render_danger_checkbox( array $args ): void {
		$value = get_option( $args['id'], false );
		?>
		<label style="color: #dc3232;">
			<input type="checkbox"
					name="<?php echo esc_attr( $args['id'] ); ?>"
					id="<?php echo esc_attr( $args['id'] ); ?>"
					value="1"
					<?php checked( $value, true ); ?>
					style="accent-color: #dc3232;">
			<?php if ( ! empty( $args['description'] ) ) : ?>
				<span class="description" style="color: #666;"><?php echo wp_kses_post( $args['description'] ); ?></span>
			<?php endif; ?>
		</label>
		<?php
	}

	/**
	 * Render main settings page
	 */
	public static function render_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'Acesso negado.', 'apollo-core' ) );
		}
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

			<div class="apollo-admin-header" style="background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); padding: 30px; border-radius: 8px; margin: 20px 0; color: #fff;">
				<h2 style="margin: 0 0 10px; color: #fff;">🚀 Apollo Core v<?php echo esc_html( APOLLO_CORE_VERSION ); ?></h2>
				<p style="margin: 0; opacity: 0.9;">Master Registry para CPTs, Taxonomias e Meta Keys do ecossistema Apollo.</p>
			</div>

			<form method="post" action="options.php">
				<?php
				settings_fields( self::OPTION_GROUP );
				do_settings_sections( self::MENU_SLUG );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Render registry page
	 */
	public static function render_registry_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'Acesso negado.', 'apollo-core' ) );
		}

		$cpt_registry = \Apollo\Core\CPTRegistry::get_instance();
		$tax_registry = \Apollo\Core\TaxonomyRegistry::get_instance();

		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Apollo Registry', 'apollo-core' ); ?></h1>

			<h2 class="nav-tab-wrapper">
				<a href="#cpts" class="nav-tab nav-tab-active" onclick="showTab('cpts')">CPTs (<?php echo count( $cpt_registry->get_definitions() ); ?>)</a>
				<a href="#taxonomies" class="nav-tab" onclick="showTab('taxonomies')">Taxonomias (<?php echo count( $tax_registry->get_definitions() ); ?>)</a>
			</h2>

			<div id="tab-cpts" class="tab-content">
				<table class="wp-list-table widefat fixed striped">
					<thead>
						<tr>
							<th><?php esc_html_e( 'Slug', 'apollo-core' ); ?></th>
							<th><?php esc_html_e( 'Nome', 'apollo-core' ); ?></th>
							<th><?php esc_html_e( 'Owner', 'apollo-core' ); ?></th>
							<th><?php esc_html_e( 'Rewrite', 'apollo-core' ); ?></th>
							<th><?php esc_html_e( 'Status', 'apollo-core' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $cpt_registry->get_definitions() as $slug => $def ) : ?>
							<?php $status = $cpt_registry->get_registered()[ $slug ] ?? array(); ?>
							<tr>
								<td><code><?php echo esc_html( $slug ); ?></code></td>
								<td><?php echo esc_html( $def['labels']['name'] ); ?></td>
								<td><?php echo esc_html( $def['owner'] ); ?></td>
								<td><?php echo $def['rewrite'] ? '<code>/' . esc_html( $def['rewrite'] ) . '</code>' : '—'; ?></td>
								<td>
									<?php if ( ! empty( $status['fallback'] ) ) : ?>
										<span style="color: #d63638;">⚠️ Fallback</span>
									<?php else : ?>
										<span style="color: #00a32a;">✓ Owner</span>
									<?php endif; ?>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>

			<div id="tab-taxonomies" class="tab-content" style="display: none;">
				<table class="wp-list-table widefat fixed striped">
					<thead>
						<tr>
							<th><?php esc_html_e( 'Slug', 'apollo-core' ); ?></th>
							<th><?php esc_html_e( 'Nome', 'apollo-core' ); ?></th>
							<th><?php esc_html_e( 'Object Types', 'apollo-core' ); ?></th>
							<th><?php esc_html_e( 'Hierárquica', 'apollo-core' ); ?></th>
							<th><?php esc_html_e( 'Status', 'apollo-core' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $tax_registry->get_definitions() as $slug => $def ) : ?>
							<?php $status = $tax_registry->get_registered()[ $slug ] ?? array(); ?>
							<tr>
								<td><code><?php echo esc_html( $slug ); ?></code></td>
								<td><?php echo esc_html( $def['labels']['name'] ); ?></td>
								<td><?php echo esc_html( implode( ', ', $def['object_types'] ) ); ?></td>
								<td><?php echo $def['hierarchical'] ? '✓' : '—'; ?></td>
								<td>
									<?php if ( ! empty( $status['fallback'] ) ) : ?>
										<span style="color: #d63638;">⚠️ Fallback</span>
									<?php else : ?>
										<span style="color: #00a32a;">✓ Core</span>
									<?php endif; ?>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>

			<script>
			function showTab(tab) {
				document.querySelectorAll('.tab-content').forEach(el => el.style.display = 'none');
				document.querySelectorAll('.nav-tab').forEach(el => el.classList.remove('nav-tab-active'));
				document.getElementById('tab-' + tab).style.display = 'block';
				event.target.classList.add('nav-tab-active');
			}
			</script>
		</div>
		<?php
	}

	/**
	 * Render database page
	 */
	public static function render_database_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'Acesso negado.', 'apollo-core' ) );
		}

		$builder = new \Apollo\Core\DatabaseBuilder();
		$status  = $builder->get_table_status();

		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Apollo Database', 'apollo-core' ); ?></h1>

			<table class="wp-list-table widefat fixed striped">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Tabela', 'apollo-core' ); ?></th>
						<th><?php esc_html_e( 'Plugin', 'apollo-core' ); ?></th>
						<th><?php esc_html_e( 'Status', 'apollo-core' ); ?></th>
						<th><?php esc_html_e( 'Registros', 'apollo-core' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $status as $key => $info ) : ?>
						<tr>
							<td><code><?php echo esc_html( $info['table'] ); ?></code></td>
							<td><?php echo esc_html( $info['plugin'] ); ?></td>
							<td>
								<?php if ( $info['exists'] ) : ?>
									<span style="color: #00a32a;">✓ Existe</span>
								<?php else : ?>
									<span style="color: #d63638;">✗ Não existe</span>
								<?php endif; ?>
							</td>
							<td><?php echo number_format( $info['rows'] ); ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
				<tfoot>
					<tr>
						<th colspan="3"><strong><?php esc_html_e( 'Total de Registros', 'apollo-core' ); ?></strong></th>
						<th><strong><?php echo number_format( array_sum( array_column( $status, 'rows' ) ) ); ?></strong></th>
					</tr>
				</tfoot>
			</table>
		</div>
		<?php
	}
}

// Initialize
SettingsPage::init();
