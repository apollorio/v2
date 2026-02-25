<?php
/**
 * Apollo Register - Blank Canvas Template
 *
 * @package Apollo\Login
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Redirect if already logged in
if ( is_user_logged_in() ) {
	wp_safe_redirect( home_url( '/mural/' ) );
	exit;
}

$auth_config      = \Apollo\Login\Templates\BlankCanvas::get_js_config();
$available_sounds = \Apollo\Login\apollo_login_get_sounds();
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<meta name="theme-color" content="#000000">
	<title><?php echo esc_html__( 'Apollo::Rio — Registre-se', 'apollo-login' ); ?></title>
	<?php do_action( 'apollo_register_head' ); ?>
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
			<section id="register-section">
				<?php include APOLLO_LOGIN_DIR . 'templates/parts/register-form.php'; ?>
			</section>
		</div>
		<?php include APOLLO_LOGIN_DIR . 'templates/parts/footer.php'; ?>
		<?php include APOLLO_LOGIN_DIR . 'templates/parts/aptitude-quiz.php'; ?>
	</div>

	<?php do_action( 'apollo_register_footer' ); ?>
</body>
</html>
