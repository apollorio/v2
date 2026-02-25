<?php

/**
 * Explore Page — /explore (aliases: /mural, /feed)
 *
 * Blank Canvas + Apollo CDN + 2-column luxury layout.
 * Preloader, fixed tab bar, compose card, feed + sidebar.
 *
 * Modular: all sections split into templates/parts/*.php
 *
 * @package Apollo\Social
 * @since   3.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! is_user_logged_in() ) {
	wp_redirect( home_url( '/acesso' ) );
	exit;
}

/* ─── Template Variables ─── */
$user_id      = get_current_user_id();
$current_user = wp_get_current_user();
$avatar       = function_exists( 'apollo_get_user_avatar_url' )
	? apollo_get_user_avatar_url( $user_id )
	: get_avatar_url( $user_id, array( 'size' => 96 ) );
$display_name = $current_user->display_name;
$username     = $current_user->user_login;
$rest_url     = rest_url( 'apollo/v1' );
$nonce        = wp_create_nonce( 'wp_rest' );
$char_limit   = class_exists( '\Apollo\Social\ContentParser' )
	? \Apollo\Social\ContentParser::CHAR_LIMIT
	: 280;

$badge = array();
if ( function_exists( 'apollo_get_user_badge_data' ) ) {
	$badge = apollo_get_user_badge_data( $user_id );
}

$parts_dir = __DIR__ . '/parts/';

/* ─── Part: Head (Blank Canvas) ─── */
require $parts_dir . 'head.php';
?>

<body data-page="explore">

	<?php /* ─── Preloader ─── */ ?>
	<?php require $parts_dir . 'preloader.php'; ?>

	<?php /* ─── Navbar (from apollo-templates) ─── */ ?>
	<?php
	if ( function_exists( 'apollo_render_navbar' ) ) {
		apollo_render_navbar();
	}
	?>

	<?php /* ─── Tab Bar (fixed) ─── */ ?>
	<?php require $parts_dir . 'tab-bar.php'; ?>

	<!-- ─── APP MAIN ─── -->
	<main class="app-main" id="app-main">

		<!-- Feed Column -->
		<div class="feed-column" id="feed-column">

			<!-- Tab: Feed (active by default) -->
			<div id="tab-feed" class="content-panel active">
				<?php require $parts_dir . 'compose-box.php'; ?>
				<?php require $parts_dir . 'feed-container.php'; ?>
			</div>

			<!-- Tab: Events -->
			<div id="tab-events" class="content-panel">
				<div class="placeholder-panel">
					<i class="ri-map-pin-line"></i>
					<h3>Eventos</h3>
					<p>Descubra os próximos eventos da cena carioca.</p>
				</div>
			</div>

			<!-- Tab: Comunas -->
			<div id="tab-comunas" class="content-panel">
				<div class="placeholder-panel">
					<i class="ri-user-community-fill"></i>
					<h3>Comunas</h3>
					<p>Encontre e participe de comunidades.</p>
				</div>
			</div>

			<!-- Tab: Market -->
			<div id="tab-market" class="content-panel">
				<div class="placeholder-panel">
					<i class="ri-ticket-2-line"></i>
					<h3>Market</h3>
					<p>Anúncios e ofertas do marketplace.</p>
				</div>
			</div>

			<!-- Tab: Favs -->
			<div id="tab-favs" class="content-panel">
				<div class="placeholder-panel">
					<i class="ri-shining-2-fill"></i>
					<h3>Favs</h3>
					<p>Seus itens salvos e favoritos.</p>
				</div>
			</div>

			<!-- Tab: Settings -->
			<?php require $parts_dir . 'settings-panel.php'; ?>

		</div>

		<!-- Sidebar Column (desktop sticky) -->
		<?php require $parts_dir . 'sidebar.php'; ?>

	</main>

	<?php /* ─── Modals ─── */ ?>
	<?php require $parts_dir . 'modal-delete.php'; ?>
	<?php require $parts_dir . 'modal-safety.php'; ?>

	<?php /* ─── Toast Container ─── */ ?>
	<div class="toast-container" id="toast-container" aria-live="polite"></div>

	<?php /* ─── Scripts ─── */ ?>
	<?php require $parts_dir . 'scripts.php'; ?>

</body>

</html>
