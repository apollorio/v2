<?php
/**
 * Apollo Login - Blank Canvas Template
 *
 * Zero theme conflict: ONLY Apollo assets loaded.
 *
 * @package Apollo\Login
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$auth_config = \Apollo\Login\Templates\BlankCanvas::get_js_config();

// Get sounds from apollo-core GLOBAL BRIDGE taxonomy
$available_sounds = \Apollo\Login\apollo_login_get_sounds();
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<meta name="theme-color" content="#000000">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
	<title><?php echo esc_html__( 'Apollo::Rio — Terminal de Acesso', 'apollo-login' ); ?></title>

	<?php do_action( 'apollo_login_head' ); ?>
</head>
<body data-state="normal">

	<!-- Background Layers -->
	<div class="bg-layer"></div>
	<div class="grid-overlay"></div>
	<div class="noise-overlay"></div>

	<!-- Terminal Container -->
	<div class="terminal-wrapper">

		<!-- Scan Line Effect -->
		<div class="scan-line"></div>

		<!-- Notification Area -->
		<div class="notification-area"></div>

		<!-- Header -->
		<?php include APOLLO_LOGIN_DIR . 'templates/parts/header.php'; ?>

		<!-- Main Content Area -->
		<div class="scroll-area">

			<!-- Login Section -->
			<section id="login-section">
				<?php include APOLLO_LOGIN_DIR . 'templates/parts/login-form.php'; ?>
			</section>

			<!-- Register Section (Hidden by default) -->
			<section id="register-section" class="hidden">
				<?php include APOLLO_LOGIN_DIR . 'templates/parts/register-form.php'; ?>
			</section>

		</div>

		<!-- Footer -->
		<?php include APOLLO_LOGIN_DIR . 'templates/parts/footer.php'; ?>

		<!-- Lockout Overlay -->
		<?php include APOLLO_LOGIN_DIR . 'templates/parts/lockout-overlay.php'; ?>

		<!-- Aptitude Quiz Overlay -->
		<?php include APOLLO_LOGIN_DIR . 'templates/parts/aptitude-quiz.php'; ?>

	</div>

	<?php do_action( 'apollo_login_footer' ); ?>
</body>
</html>
