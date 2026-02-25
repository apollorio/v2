<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="robots" content="noindex,nofollow">
	<title><?php echo esc_html( get_bloginfo( 'name' ) ); ?> - Registro</title>

	<!-- APOLLO BLANK CANVAS - Only plugin assets, NO theme -->
	<link rel="stylesheet" href="<?php echo esc_url( APOLLO_LOGIN_URL . 'assets/css/login.css' ); ?>">
	<link rel="stylesheet" href="<?php echo esc_url( APOLLO_LOGIN_URL . 'assets/css/quiz.css' ); ?>">
	<link rel="stylesheet" href="<?php echo esc_url( APOLLO_LOGIN_URL . 'assets/css/simon.css' ); ?>">
	<script src="https://cdn.apollo.rio.br/v1.0.0/core.js" fetchpriority="high"></script>
	<?php do_action( 'apollo_register_head' ); ?>
</head>

<body class="apollo-register-page apollo-blank-canvas">

	<div class="apollo-register-container">
		<div class="apollo-register-wrapper">

			<div class="apollo-logo">
				<h1>Apollo::Rio</h1>
			</div>

			<div class="apollo-register-intro">
				<h2><?php esc_html_e( 'Join Apollo', 'apollo-login' ); ?></h2>
				<p><?php esc_html_e( 'Complete the 4-stage aptitude quiz to create your account.', 'apollo-login' ); ?>
				</p>
			</div>

			<?php echo do_shortcode( '[apollo_register]' ); ?>

			<!-- Quiz Overlay -->
			<?php echo do_shortcode( '[apollo_quiz]' ); ?>

			<div class="apollo-register-footer">
				<p>
					<?php esc_html_e( 'Already have an account?', 'apollo-login' ); ?>
					<a href="<?php echo esc_url( home_url( '/acesso/' ) ); ?>">
						<?php esc_html_e( 'Login', 'apollo-login' ); ?>
					</a>
				</p>
			</div>

		</div>
	</div>

	<!-- APOLLO BLANK CANVAS - No wp_footer to avoid theme conflicts -->
	<?php do_action( 'apollo_register_footer' ); ?>
</body>

</html>
