<?php

/**
 * Bulk Editor Spreadsheet Template
 *
 * Renders a full-width Handsontable spreadsheet for a single content type.
 * Data loaded via AJAX, saved via AJAX batch.
 *
 * @package Apollo\Sheets
 * @var string $content_slug   Content type slug (post_type name, 'users', or 'comments')
 * @var string $entity_type    Entity type: post_type|users|comments
 * @var string $content_label  Human-readable label
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Load Apollo CDN for icons and base styles
echo '<script src="https://cdn.apollo.rio.br/v1.0.0/core.min.js?v=1.0.0" fetchpriority="high"></script>';

$nonce = wp_create_nonce( 'apollo_bulk_nonce' );
?>
<div class="wrap apollo-bulk-editor-wrap" id="apollo-bulk-wrap"
	data-content-type="<?php echo esc_attr( $content_slug ); ?>"
	data-entity-type="<?php echo esc_attr( $entity_type ); ?>"
	data-nonce="<?php echo esc_attr( $nonce ); ?>">

	<!-- ═══ HEADER ═══ -->
	<div class="apollo-bulk-header">
		<div class="apollo-bulk-header-left">
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=apollo-bulk' ) ); ?>" class="apollo-bulk-back" title="<?php esc_attr_e( 'Voltar', 'apollo-sheets' ); ?>">
				<span class="dashicons dashicons-arrow-left-alt2"></span>
			</a>
			<h1 class="wp-heading-inline">
				<?php echo esc_html( $content_label ); ?>
				<span class="apollo-bulk-badge" id="bulk-total-badge">0</span>
			</h1>
		</div>

		<div class="apollo-bulk-header-right">
			<!-- Search -->
			<div class="apollo-bulk-search-wrap">
				<input type="text" id="bulk-search" placeholder="<?php esc_attr_e( 'Buscar…', 'apollo-sheets' ); ?>" class="apollo-bulk-search">
				<span class="dashicons dashicons-search"></span>
			</div>

			<?php if ( $entity_type === 'post_type' ) : ?>
				<!-- Status filter -->
				<select id="bulk-status-filter" class="apollo-bulk-select">
					<option value="any"><?php esc_html_e( 'Todos os Status', 'apollo-sheets' ); ?></option>
					<option value="publish"><?php esc_html_e( 'Publicado', 'apollo-sheets' ); ?></option>
					<option value="draft"><?php esc_html_e( 'Rascunho', 'apollo-sheets' ); ?></option>
					<option value="pending"><?php esc_html_e( 'Pendente', 'apollo-sheets' ); ?></option>
					<option value="private"><?php esc_html_e( 'Privado', 'apollo-sheets' ); ?></option>
					<option value="trash"><?php esc_html_e( 'Lixeira', 'apollo-sheets' ); ?></option>
				</select>
			<?php elseif ( $entity_type === 'users' ) : ?>
				<!-- Role filter -->
				<select id="bulk-role-filter" class="apollo-bulk-select">
					<option value=""><?php esc_html_e( 'Todos os Perfis', 'apollo-sheets' ); ?></option>
					<?php foreach ( wp_roles()->roles as $role_slug => $role_info ) : ?>
						<option value="<?php echo esc_attr( $role_slug ); ?>"><?php echo esc_html( $role_info['name'] ); ?></option>
					<?php endforeach; ?>
				</select>
			<?php elseif ( $entity_type === 'comments' ) : ?>
				<!-- Comment status filter -->
				<select id="bulk-comment-status-filter" class="apollo-bulk-select">
					<option value="all"><?php esc_html_e( 'Todos', 'apollo-sheets' ); ?></option>
					<option value="approve"><?php esc_html_e( 'Aprovados', 'apollo-sheets' ); ?></option>
					<option value="hold"><?php esc_html_e( 'Pendentes', 'apollo-sheets' ); ?></option>
					<option value="spam"><?php esc_html_e( 'Spam', 'apollo-sheets' ); ?></option>
					<option value="trash"><?php esc_html_e( 'Lixeira', 'apollo-sheets' ); ?></option>
				</select>
			<?php endif; ?>
		</div>
	</div>

	<!-- ═══ TOOLBAR ═══ -->
	<div class="apollo-bulk-toolbar" id="bulk-toolbar">
		<div class="apollo-bulk-toolbar-left">
			<button type="button" id="bulk-btn-save" class="button button-primary apollo-icon-btn" title="<?php esc_attr_e( 'Salvar alterações', 'apollo-sheets' ); ?>" aria-label="<?php esc_attr_e( 'Salvar alterações', 'apollo-sheets' ); ?>" disabled>
				<span class="dashicons dashicons-saved"></span>
			</button>

			<?php if ( $entity_type === 'post_type' ) : ?>
				<button type="button" id="bulk-btn-add" class="button apollo-icon-btn" title="<?php esc_attr_e( 'Adicionar linhas', 'apollo-sheets' ); ?>" aria-label="<?php esc_attr_e( 'Adicionar linhas', 'apollo-sheets' ); ?>">
					<span class="dashicons dashicons-plus-alt2"></span>
				</button>
				<button type="button" id="bulk-btn-delete" class="button apollo-icon-btn" title="<?php esc_attr_e( 'Excluir selecionados', 'apollo-sheets' ); ?>" aria-label="<?php esc_attr_e( 'Excluir selecionados', 'apollo-sheets' ); ?>" disabled>
					<span class="dashicons dashicons-trash"></span>
				</button>
			<?php endif; ?>

			<button type="button" id="bulk-btn-export" class="button apollo-icon-btn" title="<?php esc_attr_e( 'Exportar CSV', 'apollo-sheets' ); ?>" aria-label="<?php esc_attr_e( 'Exportar CSV', 'apollo-sheets' ); ?>">
				<span class="dashicons dashicons-download"></span>
			</button>
		</div>

		<div class="apollo-bulk-toolbar-right">
			<div class="apollo-bulk-per-page">
				<span><?php esc_html_e( 'Linhas:', 'apollo-sheets' ); ?></span>
				<select id="bulk-per-page" class="apollo-bulk-per-page-select">
					<option value="50">50</option>
					<option value="100" selected>100</option>
					<option value="250">250</option>
					<option value="500">500</option>
					<option value="custom"><?php esc_html_e( 'Outro...', 'apollo-sheets' ); ?></option>
				</select>
			</div>
			<div class="apollo-bulk-pagination">
				<button type="button" id="bulk-page-prev" class="button apollo-icon-btn" title="<?php esc_attr_e( 'Página anterior', 'apollo-sheets' ); ?>" aria-label="<?php esc_attr_e( 'Página anterior', 'apollo-sheets' ); ?>" disabled>
					<span class="dashicons dashicons-arrow-left-alt2"></span>
				</button>
				<span class="apollo-bulk-page-info">
					<?php esc_html_e( 'Pág.', 'apollo-sheets' ); ?>
					<input type="number" id="bulk-page-current" value="1" min="1" class="apollo-bulk-page-input">
					<span id="bulk-page-total">/ 1</span>
				</span>
				<button type="button" id="bulk-page-next" class="button apollo-icon-btn" title="<?php esc_attr_e( 'Próxima página', 'apollo-sheets' ); ?>" aria-label="<?php esc_attr_e( 'Próxima página', 'apollo-sheets' ); ?>" disabled>
					<span class="dashicons dashicons-arrow-right-alt2"></span>
				</button>
				<span class="apollo-bulk-total-info" id="bulk-total-info"></span>
			</div>
		</div>
	</div>

	<!-- ═══ STATUS CONSOLE ═══ -->
	<div id="bulk-console" class="apollo-bulk-console">
		<?php esc_html_e( 'Carregando dados…', 'apollo-sheets' ); ?>
	</div>

	<!-- ═══ SPREADSHEET CONTAINER ═══ -->
	<div id="apollo-bulk-spreadsheet" class="apollo-bulk-spreadsheet"></div>

	<!-- ═══ ADD ROWS MODAL ═══ -->
	<?php if ( $entity_type === 'post_type' ) : ?>
		<div id="bulk-add-modal" class="apollo-bulk-modal" style="display:none;">
			<div class="apollo-bulk-modal-content">
				<h3><?php esc_html_e( 'Adicionar Linhas', 'apollo-sheets' ); ?></h3>
				<p><?php esc_html_e( 'Quantas linhas (rascunhos) deseja criar?', 'apollo-sheets' ); ?></p>
				<input type="number" id="bulk-add-count" value="5" min="1" max="50" class="regular-text">
				<div class="apollo-bulk-modal-actions">
					<button type="button" id="bulk-add-confirm" class="button button-primary"><?php esc_html_e( 'Criar', 'apollo-sheets' ); ?></button>
					<button type="button" id="bulk-add-cancel" class="button"><?php esc_html_e( 'Cancelar', 'apollo-sheets' ); ?></button>
				</div>
			</div>
		</div>
	<?php endif; ?>

	<!-- ═══ SAVE MODAL ═══ -->
	<div id="bulk-save-modal" class="apollo-bulk-modal" style="display:none;">
		<div class="apollo-bulk-modal-content">
			<h3><?php esc_html_e( 'Salvando Alterações', 'apollo-sheets' ); ?></h3>
			<p><?php esc_html_e( 'As alterações estão sendo salvas. Não feche esta janela.', 'apollo-sheets' ); ?></p>
			<div class="apollo-bulk-progress">
				<div class="apollo-bulk-progress-bar" id="bulk-progress-bar"></div>
			</div>
			<div id="bulk-save-response" class="apollo-bulk-save-response"></div>
		</div>
	</div>
</div>
