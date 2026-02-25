<?php
/**
 * Apollo Users - Registry Compliance Setup
 *
 * INSTRUÇÕES:
 * 1. Acesse: http://apollo.local/wp-content/plugins/apollo-users/setup-registry-compliance.php
 * 2. Execute a configuração
 * 3. DELETE este arquivo após uso por segurança
 *
 * @package Apollo\Users
 */

// Load WordPress
require_once dirname( __DIR__, 3 ) . '/wp-load.php';

// Security check
if ( ! current_user_can( 'manage_options' ) ) {
	wp_die( 'Acesso negado. Você precisa ser administrador.' );
}

// Get action
$action = $_GET['action'] ?? 'view';

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Apollo Registry Compliance Setup</title>
	<style>
		body {
			font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
			max-width: 900px;
			margin: 50px auto;
			padding: 20px;
			background: #f5f5f5;
		}
		.container {
			background: white;
			padding: 40px;
			border-radius: 8px;
			box-shadow: 0 2px 8px rgba(0,0,0,0.1);
		}
		h1 { color: #667eea; margin-top: 0; }
		h2 { color: #764ba2; margin-top: 30px; }
		.success { background: #d4edda; color: #155724; padding: 15px; border-radius: 4px; margin: 20px 0; }
		.info { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 4px; margin: 20px 0; }
		.warning { background: #fff3cd; color: #856404; padding: 15px; border-radius: 4px; margin: 20px 0; }
		.btn {
			background: #667eea;
			color: white;
			padding: 12px 24px;
			border: none;
			border-radius: 4px;
			cursor: pointer;
			font-size: 16px;
			text-decoration: none;
			display: inline-block;
			margin: 10px 10px 10px 0;
		}
		.btn:hover { background: #764ba2; }
		.btn-danger { background: #dc3545; }
		.btn-danger:hover { background: #c82333; }
		pre {
			background: #f8f9fa;
			padding: 15px;
			border-radius: 4px;
			overflow-x: auto;
		}
		.stat {
			display: inline-block;
			background: #667eea;
			color: white;
			padding: 10px 20px;
			border-radius: 4px;
			margin: 5px;
		}
	</style>
</head>
<body>
	<div class="container">
		<h1>🎯 Apollo Registry Compliance Setup</h1>

		<?php if ( $action === 'setup_roles' ) : ?>
			<div class="success">
				<strong>✅ Executando setup de roles...</strong>
			</div>
			<?php
			// Setup roles
			require_once APOLLO_USERS_DIR . 'src/Activation.php';

			// Use reflection to call private method
			$reflection = new ReflectionClass( 'Apollo\Users\Activation' );
			$method     = $reflection->getMethod( 'setup_roles' );
			$method->setAccessible( true );
			$method->invoke( null );

			echo '<div class="success"><strong>✅ Roles criados com sucesso!</strong></div>';
			echo '<h2>Roles Apollo:</h2>';
			echo '<ul>';
			echo '<li>✅ apollo_member</li>';
			echo '<li>✅ apollo_producer</li>';
			echo '<li>✅ apollo_dj</li>';
			echo '<li>✅ apollo_venue</li>';
			echo '<li>✅ apollo_moderator</li>';
			echo '</ul>';

			// List all roles
			global $wp_roles;
			$apollo_roles = array();
			foreach ( $wp_roles->roles as $slug => $role ) {
				if ( strpos( $slug, 'apollo_' ) === 0 ) {
					$apollo_roles[] = $slug;
				}
			}

			echo '<h2>Roles Encontrados:</h2>';
			echo '<pre>' . print_r( $apollo_roles, true ) . '</pre>';

			echo '<a href="?action=view" class="btn">← Voltar</a>';
			?>

		<?php elseif ( $action === 'migrate_users' ) : ?>
			<div class="success">
				<strong>✅ Migrando usuários...</strong>
			</div>
			<?php
			$users   = get_users( array( 'fields' => 'ID' ) );
			$total   = count( $users );
			$updated = 0;

			$plugin = Apollo\Users\Plugin::get_instance();

			echo '<p>Processando ' . $total . ' usuários...</p>';
			echo '<ul>';

			foreach ( $users as $user_id ) {
				$user = get_userdata( $user_id );

				// Initialize missing meta fields
				if ( ! metadata_exists( 'user', $user_id, '_apollo_user_verified' ) ) {
					update_user_meta( $user_id, '_apollo_user_verified', false );
				}

				if ( ! metadata_exists( 'user', $user_id, '_apollo_profile_completed' ) ) {
					update_user_meta( $user_id, '_apollo_profile_completed', 0 );
				}

				if ( ! metadata_exists( 'user', $user_id, '_apollo_privacy_profile' ) ) {
					update_user_meta( $user_id, '_apollo_privacy_profile', 'public' );
				}

				if ( ! metadata_exists( 'user', $user_id, '_apollo_privacy_email' ) ) {
					update_user_meta( $user_id, '_apollo_privacy_email', true );
				}

				if ( ! metadata_exists( 'user', $user_id, '_apollo_disable_author_url' ) ) {
					update_user_meta( $user_id, '_apollo_disable_author_url', true );
				}

				if ( ! metadata_exists( 'user', $user_id, '_apollo_profile_views' ) ) {
					update_user_meta( $user_id, '_apollo_profile_views', 0 );
				}

				// Recalculate profile completion
				$percentage = $plugin->update_profile_completion( $user_id );

				echo '<li>✓ User #' . $user_id . ' (' . esc_html( $user->user_login ) . '): ' . $percentage . '% completo</li>';
				++$updated;
			}

			echo '</ul>';
			echo '<div class="success"><strong>✅ CONCLUÍDO!</strong><br>';
			echo 'Total de usuários processados: ' . $updated . '/' . $total . '</div>';

			echo '<a href="?action=view" class="btn">← Voltar</a>';
			?>

		<?php else : // view ?>
			<div class="info">
				<strong>ℹ️ Apollo Users - 100% Registry Compliant</strong><br>
				Este setup instala as melhorias de conformidade com apollo-registry.json.
			</div>

			<h2>📋 O que será configurado:</h2>
			<ul>
				<li>✅ 15 meta fields registrados (register_meta)</li>
				<li>✅ 5 Apollo roles criados (member, producer, dj, venue, moderator)</li>
				<li>✅ Profile completion automático (0-100%)</li>
				<li>✅ User verification system</li>
				<li>✅ Capabilities granulares</li>
			</ul>

			<h2>🎯 Status Atual:</h2>
			<?php
			// Check roles
			global $wp_roles;
			$has_roles = false;
			foreach ( $wp_roles->roles as $slug => $role ) {
				if ( strpos( $slug, 'apollo_' ) === 0 ) {
					$has_roles = true;
					break;
				}
			}

			// Check users with meta
			$sample_user = get_users(
				array(
					'number' => 1,
					'fields' => 'ID',
				)
			);
			$has_meta    = false;
			if ( $sample_user ) {
				$has_meta = metadata_exists( 'user', $sample_user[0], '_apollo_profile_completed' );
			}

			echo '<div class="stat">Roles Apollo: ' . ( $has_roles ? '✅ Instalados' : '❌ Pendente' ) . '</div>';
			echo '<div class="stat">Meta Fields: ' . ( $has_meta ? '✅ Instalados' : '❌ Pendente' ) . '</div>';

			$users_count = count_users();
			echo '<div class="stat">Total Usuários: ' . $users_count['total_users'] . '</div>';
			?>

			<h2>⚡ Ações:</h2>
			<a href="?action=setup_roles" class="btn">1️⃣ Criar Apollo Roles</a>
			<a href="?action=migrate_users" class="btn">2️⃣ Migrar Usuários Existentes</a>

			<div class="warning">
				<strong>⚠️ IMPORTANTE:</strong><br>
				Execute as ações na ordem acima. Após concluir, DELETE este arquivo por segurança.
			</div>

			<h2>📖 Documentação:</h2>
			<p>Veja os detalhes completos em:</p>
			<ul>
				<li><code>_inventory/REGISTRY_COMPLIANCE_IMPLEMENTATION.md</code></li>
				<li><code>_inventory/COMPLIANCE_AUDIT_apollo-users_apollo-templates.md</code></li>
			</ul>

		<?php endif; ?>

		<hr>
		<p style="text-align: center; color: #999; margin-top: 40px;">
			Apollo Ecosystem v6.0.0 - Registry Compliant
		</p>
	</div>
</body>
</html>
