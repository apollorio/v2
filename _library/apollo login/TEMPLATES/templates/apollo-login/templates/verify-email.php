<?php
/**
 * Apollo Verify Email - Blank Canvas Template
 *
 * @package Apollo\Login
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$auth_config = \Apollo\Login\Templates\BlankCanvas::get_js_config();
$token       = sanitize_text_field( $_GET['token'] ?? '' );
$user_id     = (int) ( $_GET['user_id'] ?? 0 );
$verified    = false;

if ( $token && $user_id ) {
	$stored = get_user_meta( $user_id, APOLLO_META_VERIFICATION_TOKEN, true );
	if ( $stored && hash_equals( $stored, $token ) ) {
		update_user_meta( $user_id, APOLLO_META_EMAIL_VERIFIED, true );
		delete_user_meta( $user_id, APOLLO_META_VERIFICATION_TOKEN );
		$verified = true;
		do_action( 'apollo_login_email_verified', $user_id );
	}
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<title><?php echo esc_html__( 'Apollo::Rio — Verificar Email', 'apollo-login' ); ?></title>
	<?php do_action( 'apollo_login_head' ); ?>
</head>
<body data-state="<?php echo $verified ? 'success' : 'normal'; ?>">
	<div class="bg-layer"></div>
	<div class="grid-overlay"></div>
	<div class="noise-overlay"></div>

	<div class="terminal-wrapper">
		<div class="scan-line"></div>
		<?php include APOLLO_LOGIN_DIR . 'templates/parts/header.php'; ?>

		<div class="scroll-area" style="display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center;">

			<?php if ( $verified ) : ?>
				<div style="font-size: 48px; margin-bottom: 20px;">✓</div>
				<h3 style="color: var(--color-success); margin-bottom: 12px !important;"><?php esc_html_e( 'E-MAIL VERIFICADO', 'apollo-login' ); ?></h3>
				<p style="color: rgba(148,163,184,0.9); margin-bottom: 24px !important;"><?php esc_html_e( 'Sua conta foi ativada com sucesso.', 'apollo-login' ); ?></p>
				<a href="<?php echo esc_url( home_url( '/' . APOLLO_LOGIN_PAGE_ACESSO . '/' ) ); ?>" class="btn-primary" style="width: auto; padding: 13px 32px !important; text-decoration: none;">
					<span><?php esc_html_e( 'ACESSAR TERMINAL', 'apollo-login' ); ?></span>
					<i class="ri-arrow-right-line"></i>
				</a>
			<?php else : ?>
				<div style="font-size: 48px; margin-bottom: 20px;">✉</div>
				<h3 style="margin-bottom: 12px !important;"><?php esc_html_e( 'VERIFICAÇÃO DE E-MAIL', 'apollo-login' ); ?></h3>
				<p style="color: rgba(148,163,184,0.9); margin-bottom: 24px !important;"><?php esc_html_e( 'Link inválido ou expirado. Solicite um novo.', 'apollo-login' ); ?></p>
				<a href="<?php echo esc_url( home_url( '/' . APOLLO_LOGIN_PAGE_ACESSO . '/' ) ); ?>" class="btn-text"><?php esc_html_e( 'Voltar para login', 'apollo-login' ); ?></a>
			<?php endif; ?>

		</div>

		<?php include APOLLO_LOGIN_DIR . 'templates/parts/footer.php'; ?>
	</div>

	<?php do_action( 'apollo_login_footer' ); ?>
</body>
</html>
