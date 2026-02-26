<?php

/**
 * Profile Login Required Template
 *
 * Shown when a members-only profile is viewed by non-logged user
 *
 * @package Apollo\Users
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get current URL for redirect after login
$current_url = home_url( $_SERVER['REQUEST_URI'] );

// Minimal header to avoid theme issues
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo 'Acesso Necessário - ' . get_bloginfo( 'name' ); ?></title>

	<!-- Apollo CDN - Canvas Mode (NO wp_head to prevent theme interference) -->
	<script src="https://cdn.apollo.rio.br/v1.0.0/core.js" fetchpriority="high"></script>

	<!-- Navbar CSS/JS -->
	<?php if ( defined( 'APOLLO_TEMPLATES_URL' ) && defined( 'APOLLO_TEMPLATES_VERSION' ) ) : ?>
		<link rel="stylesheet" href="<?php echo esc_url( APOLLO_TEMPLATES_URL . 'assets/css/navbar.css' ); ?>?v=<?php echo esc_attr( APOLLO_TEMPLATES_VERSION ); ?>">
		<script src="<?php echo esc_url( APOLLO_TEMPLATES_URL . 'assets/js/navbar.js' ); ?>?v=<?php echo esc_attr( APOLLO_TEMPLATES_VERSION ); ?>" defer></script>
	<?php endif; ?>
</head>

<body <?php body_class(); ?>>

	<?php
	// Global Apollo Navbar (from apollo-templates plugin)
	if ( defined( 'APOLLO_TEMPLATES_DIR' ) && file_exists( APOLLO_TEMPLATES_DIR . 'templates/template-parts/navbar.php' ) ) {
		include APOLLO_TEMPLATES_DIR . 'templates/template-parts/navbar.php';
	}
	?>

	<div id="page" class="site">

		<div id="content" class="site-content">
			<div class="container">

				<div class="apollo-profile-restricted">
					<div class="apollo-restricted-content">
						<span class="dashicons dashicons-admin-users"></span>
						<h1><?php esc_html_e( 'Apenas para Membros' ); ?></h1>
						<p><?php esc_html_e( 'Faça login para ver este perfil.' ); ?></p>

						<div class="apollo-restricted-actions">
							<a href="<?php echo esc_url( home_url( '/acesso?redirect=' . urlencode( $current_url ) ) ); ?>" class="apollo-btn apollo-btn-primary">
								<?php esc_html_e( 'Fazer Login' ); ?>
							</a>
							<a href="<?php echo esc_url( home_url( '/registre' ) ); ?>" class="apollo-btn apollo-btn-secondary">
								<?php esc_html_e( 'Criar Conta' ); ?>
							</a>
						</div>
					</div>
				</div>

			</div><!-- #content -->
		</div><!-- #page -->

		<?php /* Canvas Mode - NO wp_footer() to prevent theme interference */ ?>
</body>

</html>