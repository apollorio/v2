<?php
/**
 * Apollo Core Admin
 *
 * Admin-specific functionality
 *
 * @package Apollo_Core
 * @since 6.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Apollo_Core_Admin {

	private string $plugin_name;
	private string $version;

	public function __construct( string $plugin_name, string $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	public function enqueue_styles(): void {
		wp_enqueue_style(
			$this->plugin_name,
			plugin_dir_url( __FILE__ ) . 'css/apollo-core-admin.css',
			array(),
			$this->version,
			'all'
		);
	}

	public function enqueue_scripts(): void {
		wp_enqueue_script(
			$this->plugin_name,
			plugin_dir_url( __FILE__ ) . 'js/apollo-core-admin.js',
			array( 'jquery' ),
			$this->version,
			true
		);

		wp_localize_script(
			$this->plugin_name,
			'apolloCoreAdmin',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'apollo_core_admin' ),
				'version' => $this->version,
			)
		);
	}

	public function add_admin_menu(): void {
		add_menu_page(
			__( 'Apollo Core', 'apollo-core' ),
			__( 'Apollo', 'apollo-core' ),
			'manage_options',
			'apollo-core',
			array( $this, 'render_admin_page' ),
			'dashicons-shield',
			30
		);

		add_submenu_page(
			'apollo-core',
			__( 'Health Check', 'apollo-core' ),
			__( 'Health', 'apollo-core' ),
			'manage_options',
			'apollo-core-health',
			array( $this, 'render_health_page' )
		);

		add_submenu_page(
			'apollo-core',
			__( 'Registry', 'apollo-core' ),
			__( 'Registry', 'apollo-core' ),
			'manage_options',
			'apollo-core-registry',
			array( $this, 'render_registry_page' )
		);
	}

	public function register_settings(): void {
		register_setting( 'apollo_core_settings', 'apollo_debug_mode' );
		register_setting( 'apollo_core_settings', 'apollo_cdn_enabled' );
	}

	public function render_admin_page(): void {
		?>
		<div class="wrap apollo-core-admin">
			<h1><?php esc_html_e( 'Apollo Core Dashboard', 'apollo-core' ); ?></h1>

			<div class="apollo-cards">
				<div class="apollo-card">
					<h2><?php esc_html_e( 'Version', 'apollo-core' ); ?></h2>
					<p class="apollo-version"><?php echo esc_html( APOLLO_VERSION ); ?></p>
				</div>

				<div class="apollo-card">
					<h2><?php esc_html_e( 'Plugins', 'apollo-core' ); ?></h2>
					<ul>
						<li>Registry: <?php echo ! empty( \Apollo\Core\Registry::get_registry() ) ? '✅' : '❌'; ?></li>
						<li>CDN: <?php echo get_option( 'apollo_cdn_registered' ) ? '✅' : '❌'; ?></li>
					</ul>
				</div>

				<div class="apollo-card">
					<h2><?php esc_html_e( 'API', 'apollo-core' ); ?></h2>
					<p><code>/wp-json/apollo/v1/health</code></p>
					<p><code>/wp-json/apollo/v1/registry</code></p>
				</div>
			</div>

			<h2><?php esc_html_e( 'Settings', 'apollo-core' ); ?></h2>
			<form method="post" action="options.php">
				<?php settings_fields( 'apollo_core_settings' ); ?>
				<table class="form-table">
					<tr>
						<th scope="row"><?php esc_html_e( 'Debug Mode', 'apollo-core' ); ?></th>
						<td>
							<input type="checkbox" name="apollo_debug_mode" value="1"
								<?php checked( get_option( 'apollo_debug_mode' ), 1 ); ?>>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'CDN Enabled', 'apollo-core' ); ?></th>
						<td>
							<input type="checkbox" name="apollo_cdn_enabled" value="1"
								<?php checked( get_option( 'apollo_cdn_enabled', 1 ), 1 ); ?>>
						</td>
					</tr>
				</table>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}

	public function render_health_page(): void {
		$response = wp_remote_get( rest_url( 'apollo/v1/health' ) );
		$health   = array();
		if ( ! is_wp_error( $response ) ) {
			$health = json_decode( wp_remote_retrieve_body( $response ), true );
		}
		?>
		<div class="wrap apollo-core-health">
			<h1><?php esc_html_e( 'Health Check', 'apollo-core' ); ?></h1>

			<?php if ( ! empty( $health ) ) : ?>
				<div class="apollo-health-status <?php echo esc_attr( $health['status'] ); ?>">
					<strong><?php esc_html_e( 'Status:', 'apollo-core' ); ?></strong>
					<?php echo esc_html( strtoupper( $health['status'] ) ); ?>
				</div>

				<table class="widefat striped">
					<thead>
						<tr>
							<th><?php esc_html_e( 'Check', 'apollo-core' ); ?></th>
							<th><?php esc_html_e( 'Status', 'apollo-core' ); ?></th>
							<th><?php esc_html_e( 'Message', 'apollo-core' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $health['checks'] ?? array() as $name => $check ) : ?>
							<tr>
								<td><?php echo esc_html( ucfirst( $name ) ); ?></td>
								<td><?php echo $check['status'] === 'ok' ? '✅' : '❌'; ?></td>
								<td><?php echo esc_html( $check['message'] ?? '' ); ?></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php else : ?>
				<div class="notice notice-error">
					<p><?php esc_html_e( 'Could not retrieve health status.', 'apollo-core' ); ?></p>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}

	public function render_registry_page(): void {
		$registry = \Apollo\Core\Registry::get_registry();
		?>
		<div class="wrap apollo-core-registry">
			<h1><?php esc_html_e( 'Apollo Registry', 'apollo-core' ); ?></h1>

			<div class="apollo-registry-info">
				<p><strong><?php esc_html_e( 'Version:', 'apollo-core' ); ?></strong>
					<?php echo esc_html( $registry['$version'] ?? 'N/A' ); ?></p>
				<p><strong><?php esc_html_e( 'Generated:', 'apollo-core' ); ?></strong>
					<?php echo esc_html( $registry['$generated'] ?? 'N/A' ); ?></p>
				<p><strong><?php esc_html_e( 'Total Plugins:', 'apollo-core' ); ?></strong>
					<?php echo esc_html( $registry['architecture']['total_plugins'] ?? count( $registry['plugins'] ?? array() ) ); ?></p>
			<table class="widefat striped">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Slug', 'apollo-core' ); ?></th>
						<th><?php esc_html_e( 'Priority', 'apollo-core' ); ?></th>
						<th><?php esc_html_e( 'Description', 'apollo-core' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $registry['plugins'] ?? array() as $slug => $plugin ) : ?>
						<tr>
							<td><strong><?php echo esc_html( $slug ); ?></strong></td>
							<td><?php echo esc_html( $plugin['priority'] ?? '-' ); ?></td>
							<td><?php echo esc_html( $plugin['description'] ?? '' ); ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<?php
	}
}
