<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<meta name="theme-color" content="#000000">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
	<meta name="robots" content="noindex,nofollow">
	<title><?php echo esc_html__( 'Verificar Email', 'apollo-login' ); ?></title>

	<!-- DIRECT ASSETS - NO wp_head() TO PREVENT INTERFERENCE -->
	<script src="https://cdn.apollo.rio.br/v1.0.0/core.min.js?v=1.0.0" fetchpriority="high"></script>
	<link rel="stylesheet" href="<?php echo esc_url( APOLLO_LOGIN_URL . 'assets/css/apollo-auth-complete.css?v=' . APOLLO_LOGIN_VERSION ); ?>">
</head>

<body data-state="normal">

	<div class="bg-layer"></div>
	<div class="grid-overlay"></div>
	<div class="noise-overlay"></div>

	<div class="terminal-wrapper">
		<div class="scan-line"></div>
		<div class="notification-area"></div>

		<?php require APOLLO_LOGIN_DIR . 'templates/parts/new_header.php'; ?>

		<div class="scroll-area">
			<section>
				<?php echo do_shortcode( '[apollo_verify_email]' ); ?>
			</section>
		</div>

		<?php require APOLLO_LOGIN_DIR . 'templates/parts/new_footer.php'; ?>
	</div>

	<!-- APOLLO REPORT MODAL (Manually injected - no wp_footer in Blank Canvas) -->
	<?php
	if ( function_exists( 'apollo_render_report_modal' ) ) {
		apollo_render_report_modal( 'apolloReportTrigger', 'frontend' );
	}
	?>

	<!-- SELF-CONTAINED - NO wp_footer() -->
	<script src="<?php echo esc_url( APOLLO_LOGIN_URL . 'assets/js/apollo-auth-scripts.js?v=' . APOLLO_LOGIN_VERSION ); ?>"></script>

</body>

</html>