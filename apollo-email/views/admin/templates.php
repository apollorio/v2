<?php
/**
 * Admin View: Templates.
 *
 * @var array $templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$type_labels = array(
	'transactional' => __( 'Transacional', 'apollo-email' ),
	'marketing'     => __( 'Marketing', 'apollo-email' ),
	'digest'        => __( 'Digest', 'apollo-email' ),
);
?>
<div class="wrap apollo-email-admin">
	<h1 class="wp-heading-inline"><?php esc_html_e( 'Templates de E-mail', 'apollo-email' ); ?></h1>
	<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=email_aprio' ) ); ?>" class="page-title-action">
		<?php esc_html_e( 'Novo Template', 'apollo-email' ); ?>
	</a>
	<hr class="wp-header-end" />

	<?php if ( empty( $templates ) ) : ?>
		<div class="apollo-email-empty">
			<span class="dashicons dashicons-email-alt2"></span>
			<p><?php esc_html_e( 'Nenhum template encontrado. Desative e reative o plugin para gerar os templates padrão.', 'apollo-email' ); ?></p>
			<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=email_aprio' ) ); ?>" class="button button-primary">
				<?php esc_html_e( 'Criar Template', 'apollo-email' ); ?>
			</a>
		</div>
	<?php else : ?>
		<table class="wp-list-table widefat fixed striped">
			<thead>
				<tr>
					<th class="column-title"><?php esc_html_e( 'Título', 'apollo-email' ); ?></th>
					<th class="column-slug"><?php esc_html_e( 'Slug', 'apollo-email' ); ?></th>
					<th class="column-subject"><?php esc_html_e( 'Assunto', 'apollo-email' ); ?></th>
					<th class="column-type"><?php esc_html_e( 'Tipo', 'apollo-email' ); ?></th>
					<th class="column-variables"><?php esc_html_e( 'Variáveis', 'apollo-email' ); ?></th>
					<th class="column-date"><?php esc_html_e( 'Modificado', 'apollo-email' ); ?></th>
					<th class="column-actions"><?php esc_html_e( 'Ações', 'apollo-email' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $templates as $tpl ) : ?>
					<tr>
						<td class="column-title">
							<strong>
								<a href="<?php echo esc_url( get_edit_post_link( $tpl['id'] ) ); ?>">
									<?php echo esc_html( $tpl['title'] ); ?>
								</a>
							</strong>
						</td>
						<td class="column-slug">
							<code><?php echo esc_html( $tpl['slug'] ); ?></code>
						</td>
						<td class="column-subject">
							<?php echo esc_html( $tpl['subject'] ?? '—' ); ?>
						</td>
						<td class="column-type">
							<?php
							$type       = $tpl['type'] ?? 'transactional';
							$type_class = 'badge badge-' . sanitize_html_class( $type );
							?>
							<span class="<?php echo esc_attr( $type_class ); ?>">
								<?php echo esc_html( $type_labels[ $type ] ?? ucfirst( $type ) ); ?>
							</span>
						</td>
						<td class="column-variables">
							<?php
							$vars = $tpl['variables'] ?? array();
							if ( is_array( $vars ) && ! empty( $vars ) ) {
								foreach ( $vars as $v ) {
									echo '<code class="var-tag">{{' . esc_html( $v ) . '}}</code> ';
								}
							} else {
								echo '—';
							}
							?>
						</td>
						<td class="column-date">
							<?php echo esc_html( wp_date( 'd/m/Y H:i', strtotime( $tpl['modified'] ) ) ); ?>
						</td>
						<td class="column-actions">
							<a href="<?php echo esc_url( get_edit_post_link( $tpl['id'] ) ); ?>" class="button button-small">
								<?php esc_html_e( 'Editar', 'apollo-email' ); ?>
							</a>
							<a href="
							<?php
							echo esc_url(
								add_query_arg(
									array(
										'page'    => 'apollo-email-templates',
										'preview' => $tpl['id'],
									),
									admin_url( 'admin.php' )
								)
							);
							?>
							" class="button button-small" target="_blank">
								<?php esc_html_e( 'Preview', 'apollo-email' ); ?>
							</a>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	<?php endif; ?>

	<div class="apollo-email-card apollo-email-card-full" style="margin-top: 20px;">
		<h3><?php esc_html_e( 'Merge Tags Disponíveis', 'apollo-email' ); ?></h3>
		<p class="description"><?php esc_html_e( 'Use estas tags no assunto ou corpo do template. Elas serão substituídas automaticamente.', 'apollo-email' ); ?></p>
		<table class="widefat fixed striped" style="max-width: 600px;">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Tag', 'apollo-email' ); ?></th>
					<th><?php esc_html_e( 'Descrição', 'apollo-email' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr><td><code>{{site_name}}</code></td><td><?php esc_html_e( 'Nome do site', 'apollo-email' ); ?></td></tr>
				<tr><td><code>{{username}}</code></td><td><?php esc_html_e( 'Nome do usuário', 'apollo-email' ); ?></td></tr>
				<tr><td><code>{{email}}</code></td><td><?php esc_html_e( 'E-mail do destinatário', 'apollo-email' ); ?></td></tr>
				<tr><td><code>{{action_url}}</code></td><td><?php esc_html_e( 'URL da ação principal', 'apollo-email' ); ?></td></tr>
				<tr><td><code>{{action_text}}</code></td><td><?php esc_html_e( 'Texto do botão de ação', 'apollo-email' ); ?></td></tr>
				<tr><td><code>{{title}}</code></td><td><?php esc_html_e( 'Título do e-mail', 'apollo-email' ); ?></td></tr>
				<tr><td><code>{{message}}</code></td><td><?php esc_html_e( 'Mensagem principal', 'apollo-email' ); ?></td></tr>
				<tr><td><code>{{current_year}}</code></td><td><?php esc_html_e( 'Ano atual', 'apollo-email' ); ?></td></tr>
				<tr><td><code>{{brand_color}}</code></td><td><?php esc_html_e( 'Cor da marca', 'apollo-email' ); ?></td></tr>
			</tbody>
		</table>
	</div>
</div>
