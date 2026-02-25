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
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo 'Acesso Necessário - ' . get_bloginfo( 'name' ); ?></title>
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<div id="page" class="site">
	<header id="masthead" class="site-header">
		<div class="site-branding">
			<h1 class="site-title"><a href="<?php echo home_url(); ?>"><?php bloginfo( 'name' ); ?></a></h1>
		</div>
	</header>

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

<?php wp_footer(); ?>
</body>
</html>
