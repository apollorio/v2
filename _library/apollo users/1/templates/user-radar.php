<?php
/**
 * User Radar Template
 *
 * Template for /radar user directory page
 *
 * @package Apollo\Users
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Require login for radar
// Temporarily disabled for testing - FORCE RADAR PAGE
// if ( ! is_user_logged_in() ) {
// 	wp_redirect( home_url( '/acesso?redirect=' . urlencode( home_url( '/radar' ) ) ) );
// 	exit;
// }

// Minimal header to avoid theme issues
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo 'Radar de Usuários - ' . get_bloginfo( 'name' ); ?></title>
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

<div class="apollo-radar-page">

	<div class="apollo-radar-header">
		<h1><?php echo 'Radar de Usuários'; ?></h1>
		<p><?php echo 'Descubra pessoas incríveis na comunidade.'; ?></p>
	</div>

	<!-- Filters -->
	<div class="apollo-radar-filters">
		<form id="apollo-radar-form" class="apollo-filter-form">
			<div class="apollo-filter-group">
				<label for="filter-search"><?php echo 'Buscar'; ?></label>
				<input type="text" id="filter-search" name="search" placeholder="<?php echo 'Nome ou username...'; ?>" />
			</div>

			<div class="apollo-filter-group">
				<label for="filter-location"><?php echo 'Cidade'; ?></label>
				<input type="text" id="filter-location" name="location" placeholder="<?php echo 'São Paulo, RJ...'; ?>" />
			</div>

			<button type="submit" class="apollo-btn apollo-btn-primary">
				<span class="dashicons dashicons-search"></span>
				<?php echo 'Buscar'; ?>
			</button>

			<button type="reset" class="apollo-btn apollo-btn-secondary">
				<?php echo 'Limpar'; ?>
			</button>
		</form>
	</div>

	<!-- Results -->
	<div class="apollo-radar-results">
		<div id="apollo-radar-grid" class="apollo-user-grid">
			<?php
			// Load users server-side for immediate display
			$users_args = [
				'number' => 20,
				'offset' => 0,
				'orderby' => 'registered',
				'order' => 'DESC',
				'meta_query' => [
					'relation' => 'OR',
					[
						'key' => '_apollo_privacy_profile',
						'value' => 'private',
						'compare' => '!=',
					],
					[
						'key' => '_apollo_privacy_profile',
						'compare' => 'NOT EXISTS',
					],
				],
			];

			$users_query = new WP_User_Query($users_args);
			$users = $users_query->get_results();

			if (!empty($users)) {
				foreach ($users as $user) {
					$user_id = $user->ID;
					$display_name = get_user_meta($user_id, '_apollo_social_name', true) ?: $user->display_name;
					$avatar_url = function_exists('apollo_get_user_avatar_url')
						? apollo_get_user_avatar_url($user_id, 'medium')
						: get_avatar_url($user_id);
					$profile_url = function_exists('apollo_get_profile_url')
						? apollo_get_profile_url($user)
						: home_url('/id/' . $user->user_login);
					$location = get_user_meta($user_id, 'user_location', true);
					?>
					<div class="apollo-user-card">
						<a href="<?php echo esc_url($profile_url); ?>" class="apollo-user-link">
							<div class="apollo-user-avatar">
								<img src="<?php echo esc_url($avatar_url); ?>" alt="<?php echo esc_attr($display_name); ?>" />
							</div>
							<div class="apollo-user-info">
								<h3 class="apollo-user-name"><?php echo esc_html($display_name); ?></h3>
								<p class="apollo-user-username">@<?php echo esc_html($user->user_login); ?></p>
								<?php if ($location) : ?>
									<p class="apollo-user-location"><span class="dashicons dashicons-location"></span> <?php echo esc_html($location); ?></p>
								<?php endif; ?>
							</div>
						</a>
					</div>
					<?php
				}
			} else {
				echo '<p class="apollo-no-results">Nenhum usuário encontrado.</p>';
			}
			?>
		</div>

		<!-- Pagination -->
		<div id="apollo-radar-pagination" class="apollo-pagination" style="display: none;">
			<button id="load-more" class="apollo-btn apollo-btn-outline">
				<?php echo 'Carregar mais'; ?>
			</button>
		</div>
	</div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
	// Basic load more functionality (optional)
	const loadMoreBtn = document.getElementById('load-more');
	if (loadMoreBtn) {
		loadMoreBtn.addEventListener('click', function() {
			// Could implement AJAX loading here if needed
			console.log('Load more clicked');
		});
	}
});
</script>

	</div><!-- #content -->

	<footer id="colophon" class="site-footer">
		<div class="site-info">
			<p>&copy; <?php echo date('Y') . ' ' . get_bloginfo('name'); ?></p>
		</div>
	</footer>
</div><!-- #page -->

<?php
wp_footer();
// Restore original error reporting
if ( isset( $old_error_reporting ) ) {
	error_reporting($old_error_reporting);
}
?>
</body>
</html>
