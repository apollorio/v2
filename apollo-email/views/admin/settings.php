<?php
/**
 * Admin View: Configurações.
 *
 * Uses WordPress Settings API — sections and fields are
 * registered in AdminPage::registerSettings().
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wrap apollo-email-admin">
	<h1><?php esc_html_e( 'Configurações — Email Apollo', 'apollo-email' ); ?></h1>

	<form method="post" action="options.php">
		<?php
		settings_fields( 'apollo_email_settings' );
		?>

		<div class="apollo-email-settings-wrap">
			<!-- General -->
			<div class="apollo-email-settings-section">
				<h2><span class="dashicons dashicons-admin-generic"></span> <?php esc_html_e( 'Geral', 'apollo-email' ); ?></h2>
				<table class="form-table" role="presentation">
					<?php do_settings_fields( 'apollo_email_settings', 'apollo_email_general' ); ?>
				</table>
			</div>

			<!-- Transport -->
			<div class="apollo-email-settings-section">
				<h2><span class="dashicons dashicons-migrate"></span> <?php esc_html_e( 'Transporte', 'apollo-email' ); ?></h2>
				<p class="description"><?php esc_html_e( 'Configure como os e-mails serão enviados. Os campos de SMTP, SES ou SendGrid serão usados conforme o método selecionado.', 'apollo-email' ); ?></p>
				<table class="form-table" role="presentation">
					<?php do_settings_fields( 'apollo_email_settings', 'apollo_email_transport' ); ?>
				</table>
			</div>

			<!-- Tracking -->
			<div class="apollo-email-settings-section">
				<h2><span class="dashicons dashicons-chart-bar"></span> <?php esc_html_e( 'Rastreamento', 'apollo-email' ); ?></h2>
				<p class="description"><?php esc_html_e( 'Ative rastreamento para monitorar aberturas e cliques nos e-mails enviados.', 'apollo-email' ); ?></p>
				<table class="form-table" role="presentation">
					<?php do_settings_fields( 'apollo_email_settings', 'apollo_email_tracking' ); ?>
				</table>
			</div>

			<!-- Branding -->
			<div class="apollo-email-settings-section">
				<h2><span class="dashicons dashicons-art"></span> <?php esc_html_e( 'Identidade Visual', 'apollo-email' ); ?></h2>
				<p class="description"><?php esc_html_e( 'Personalize a aparência dos e-mails enviados.', 'apollo-email' ); ?></p>
				<table class="form-table" role="presentation">
					<?php do_settings_fields( 'apollo_email_settings', 'apollo_email_branding' ); ?>
				</table>
			</div>
		</div>

		<?php submit_button( __( 'Salvar Configurações', 'apollo-email' ) ); ?>
	</form>

	<!-- System Info -->
	<div class="apollo-email-card apollo-email-card-full" style="margin-top: 20px;">
		<h3><?php esc_html_e( 'Informações do Sistema', 'apollo-email' ); ?></h3>
		<table class="widefat striped" style="max-width: 600px;">
			<tbody>
				<tr>
					<td><?php esc_html_e( 'Plugin Version', 'apollo-email' ); ?></td>
					<td><code><?php echo esc_html( APOLLO_EMAIL_VERSION ); ?></code></td>
				</tr>
				<tr>
					<td><?php esc_html_e( 'PHP Version', 'apollo-email' ); ?></td>
					<td><code><?php echo esc_html( PHP_VERSION ); ?></code></td>
				</tr>
				<tr>
					<td><?php esc_html_e( 'WordPress Version', 'apollo-email' ); ?></td>
					<td><code><?php echo esc_html( get_bloginfo( 'version' ) ); ?></code></td>
				</tr>
				<tr>
					<td><?php esc_html_e( 'Cron Status', 'apollo-email' ); ?></td>
					<td>
						<?php
						$next_run = wp_next_scheduled( APOLLO_EMAIL_CRON_HOOK );
						if ( $next_run ) {
							echo '<span class="badge badge-sent">' . esc_html__( 'Ativo', 'apollo-email' ) . '</span> — ';
							echo esc_html(
								sprintf(
									__( 'Próximo: %s', 'apollo-email' ),
									wp_date( 'd/m/Y H:i:s', $next_run )
								)
							);
						} else {
							echo '<span class="badge badge-failed">' . esc_html__( 'Inativo', 'apollo-email' ) . '</span>';
						}
						?>
					</td>
				</tr>
				<tr>
					<td><?php esc_html_e( 'Apollo Core', 'apollo-email' ); ?></td>
					<td>
						<?php if ( defined( 'APOLLO_CORE_BOOTSTRAPPED' ) ) : ?>
							<span class="badge badge-sent"><?php esc_html_e( 'Conectado', 'apollo-email' ); ?></span>
							<?php if ( defined( 'APOLLO_VERSION' ) ) : ?>
								<code><?php echo esc_html( APOLLO_VERSION ); ?></code>
							<?php endif; ?>
						<?php else : ?>
							<span class="badge badge-failed"><?php esc_html_e( 'Não encontrado', 'apollo-email' ); ?></span>
						<?php endif; ?>
					</td>
				</tr>
				<tr>
					<td><?php esc_html_e( 'DB Version', 'apollo-email' ); ?></td>
					<td><code><?php echo esc_html( get_option( 'apollo_email_db_version', '—' ) ); ?></code></td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
