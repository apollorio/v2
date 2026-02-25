<?php
/**
 * Profile Private Template
 *
 * Shown when a user's profile is set to private
 *
 * @package Apollo\Users
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Minimal header to avoid theme issues
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo 'Perfil Privado - ' . get_bloginfo( 'name' ); ?></title>
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
		<span class="dashicons dashicons-lock"></span>
		<h1><?php esc_html_e( 'Perfil Privado' ); ?></h1>
		<p><?php esc_html_e( 'Este usuário configurou seu perfil como privado.' ); ?></p>

		<div class="apollo-restricted-actions">
			<a href="<?php echo esc_url( home_url( '/radar' ) ); ?>" class="apollo-btn apollo-btn-primary">
				<?php esc_html_e( 'Explorar outros perfis' ); ?>
			</a>
			<a href="<?php echo esc_url( home_url() ); ?>" class="apollo-btn apollo-btn-secondary">
				<?php esc_html_e( 'Voltar ao início' ); ?>
			</a>
		</div>
	</div>
</div>

	</div><!-- #content -->
</div><!-- #page -->

<?php wp_footer(); ?>
</body>
</html>
