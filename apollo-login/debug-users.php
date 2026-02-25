<?php
/**
 * Debug script to check WordPress users.
 * Access: http://localhost:10004/wp-content/plugins/apollo-login/debug-users.php
 */

// Load WordPress.
require_once dirname( dirname( dirname( __DIR__ ) ) ) . '/wp-load.php';

// Get all users.
$users = get_users(
	array(
		'fields' => array( 'ID', 'user_login', 'user_email' ),
	)
);

header( 'Content-Type: text/html; charset=utf-8' );
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Apollo Login - Debug Users</title>
	<style>
		body { font-family: system-ui, sans-serif; padding: 20px; background: #f5f5f5; }
		table { background: white; border-collapse: collapse; width: 100%; max-width: 800px; }
		th, td { padding: 12px; text-align: left; border: 1px solid #ddd; }
		th { background: #333; color: white; }
		tr:hover { background: #f9f9f9; }
		h1 { color: #333; }
		.info { background: #e3f2fd; padding: 12px; border-left: 4px solid #2196f3; margin: 20px 0; }
	</style>
</head>
<body>
	<h1>🔍 Apollo Login - Usuários WordPress</h1>

	<div class="info">
		<strong>Total de usuários:</strong> <?php echo count( $users ); ?>
	</div>

	<table>
		<thead>
			<tr>
				<th>ID</th>
				<th>Login</th>
				<th>Email</th>
				<th>Verificar Senha</th>
			</tr>
		</thead>
		<tbody>
			<?php if ( empty( $users ) ) : ?>
				<tr>
					<td colspan="4" style="text-align: center; color: #999;">
						Nenhum usuário encontrado.
					</td>
				</tr>
			<?php else : ?>
				<?php foreach ( $users as $user ) : ?>
					<tr>
						<td><?php echo esc_html( $user->ID ); ?></td>
						<td><strong><?php echo esc_html( $user->user_login ); ?></strong></td>
						<td><?php echo esc_html( $user->user_email ); ?></td>
						<td>
							<?php
							// Check if password 'root' works
							$check_user = wp_authenticate( $user->user_login, 'root' );
							if ( ! is_wp_error( $check_user ) ) {
								echo '<span style="color: green;">✓ Senha: root</span>';
							} else {
								$check_admin = wp_authenticate( $user->user_login, 'admin' );
								if ( ! is_wp_error( $check_admin ) ) {
									echo '<span style="color: green;">✓ Senha: admin</span>';
								} else {
									echo '<span style="color: #999;">⚠ Senha diferente</span>';
								}
							}
							?>
						</td>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
		</tbody>
	</table>

	<div class="info" style="margin-top: 30px;">
		<strong>Nota:</strong> Este arquivo deve ser excluído em produção por razões de segurança.
	</div>
</body>
</html>
