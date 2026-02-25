<?php
/**
 * Apollo Password Reset - Blank Canvas Template
 *
 * @package Apollo\Login
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$auth_config = \Apollo\Login\Templates\BlankCanvas::get_js_config();
$token       = sanitize_text_field( $_GET['token'] ?? '' );
$user_id     = (int) ( $_GET['user_id'] ?? 0 );
$has_token   = ! empty( $token ) && $user_id > 0;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<title><?php echo esc_html__( 'Apollo::Rio — Resetar Senha', 'apollo-login' ); ?></title>
	<?php do_action( 'apollo_login_head' ); ?>
</head>
<body data-state="normal">
	<div class="bg-layer"></div>
	<div class="grid-overlay"></div>
	<div class="noise-overlay"></div>

	<div class="terminal-wrapper">
		<div class="scan-line"></div>
		<div class="notification-area"></div>
		<?php include APOLLO_LOGIN_DIR . 'templates/parts/header.php'; ?>

		<div class="scroll-area">
			<div class="flavor-text">
				<span>> <?php esc_html_e( 'Recuperação de acesso', 'apollo-login' ); ?></span>
			</div>

			<?php if ( $has_token ) : ?>
			<!-- New Password Form -->
			<form id="reset-confirm-form" method="post">
				<input type="hidden" name="token" value="<?php echo esc_attr( $token ); ?>">
				<input type="hidden" name="user_id" value="<?php echo esc_attr( (string) $user_id ); ?>">

				<div class="form-group">
					<label for="new_password"><?php esc_html_e( 'Nova chave de acesso', 'apollo-login' ); ?> <span style="color: var(--color-accent);">*</span></label>
					<div class="input-wrapper">
						<span class="input-prefix">></span>
						<input type="password" id="new_password" name="new_password" placeholder="<?php esc_attr_e( 'mínimo 8 caracteres', 'apollo-login' ); ?>" minlength="8" required autocomplete="new-password">
					</div>
				</div>

				<div class="form-group">
					<label for="confirm_password"><?php esc_html_e( 'Confirme a nova senha', 'apollo-login' ); ?> <span style="color: var(--color-accent);">*</span></label>
					<div class="input-wrapper">
						<span class="input-prefix">></span>
						<input type="password" id="confirm_password" name="confirm_password" placeholder="<?php esc_attr_e( 'repita a senha', 'apollo-login' ); ?>" minlength="8" required autocomplete="new-password">
					</div>
				</div>

				<button type="submit" class="btn-primary">
					<span><?php esc_html_e( 'RESETAR SENHA', 'apollo-login' ); ?></span>
					<i class="ri-lock-unlock-line"></i>
				</button>
			</form>

			<?php else : ?>
			<!-- Request Reset Form -->
			<form id="reset-request-form" method="post">
				<div class="form-group" style="margin-top: 16px;">
					<label for="reset_email"><?php esc_html_e( 'E-mail da conta', 'apollo-login' ); ?> <span style="color: var(--color-accent);">*</span></label>
					<div class="input-wrapper">
						<span class="input-prefix">></span>
						<input type="email" id="reset_email" name="email" placeholder="<?php esc_attr_e( 'seu@email.com', 'apollo-login' ); ?>" required autocomplete="email">
					</div>
				</div>

				<button type="submit" class="btn-primary">
					<span><?php esc_html_e( 'ENVIAR LINK DE RECUPERAÇÃO', 'apollo-login' ); ?></span>
					<i class="ri-mail-send-line"></i>
				</button>
			</form>
			<?php endif; ?>

			<div style="text-align: center; margin-top: 20px;">
				<p style="font-size: 12px; color: rgba(148,163,184,0.9);">
					<a href="<?php echo esc_url( home_url( '/' . APOLLO_LOGIN_PAGE_ACESSO . '/' ) ); ?>" class="btn-text">
						<?php esc_html_e( 'Voltar para login', 'apollo-login' ); ?>
					</a>
				</p>
			</div>
		</div>

		<?php include APOLLO_LOGIN_DIR . 'templates/parts/footer.php'; ?>
	</div>

	<?php do_action( 'apollo_login_footer' ); ?>
</body>
</html>
