<?php

/**
 * Single Profile Template - /id/{username}
 *
 * Final printable profile layout based on the provided HTML.
 *
 * @package Apollo\Users
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $apollo_profile_user;

if ( ! $apollo_profile_user instanceof \WP_User || $apollo_profile_user->ID <= 0 ) {
	global $wp_query;
	$wp_query->set_404();
	status_header( 404 );
	if ( function_exists( 'get_template_part' ) ) {
		get_template_part( '404' );
	} else {
		echo '<h1>404 - Usuario nao encontrado</h1>';
	}
	return;
}

$user    = $apollo_profile_user;
$user_id = $user->ID;
$is_own  = ( get_current_user_id() === $user_id );

// Privacy check
$privacy = get_user_meta( $user_id, '_apollo_privacy_profile', true ) ?: 'public';
if ( $privacy === 'private' && ! $is_own && ! current_user_can( 'manage_options' ) ) {
	include APOLLO_USERS_DIR . 'templates/profile-private.php';
	return;
}
if ( $privacy === 'members' && ! is_user_logged_in() ) {
	include APOLLO_USERS_DIR . 'templates/profile-login-required.php';
	return;
}

// User data
$social_name      = get_user_meta( $user_id, '_apollo_social_name', true );
$display_name     = $social_name ?: $user->display_name;
$bio              = get_user_meta( $user_id, '_apollo_bio', true ) ?: $user->description;
$member_since     = date( 'Y', strtotime( $user->user_registered ) );
$membership_value = get_user_meta( $user_id, '_apollo_membership', true );
$has_membership   = ! empty( $membership_value );

if ( ! $bio ) {
	$bio = 'Biografia do usuário aguardando atualização.';
}

$avatar_url = function_exists( 'Apollo\Users\apollo_get_user_avatar_url' )
	? \Apollo\Users\apollo_get_user_avatar_url( $user_id, 'large' )
	: get_avatar_url( $user_id, array( 'size' => 400 ) );

// YouTube + SoundCloud (defaults required)
$default_youtube_embed = 'https://www.youtube.com/embed/p_HEbzf1VeU?autoplay=1&mute=1&loop=1&playlist=p_HEbzf1VeU&controls=0&showinfo=0&modestbranding=1&rel=0&iv_load_policy=3&playsinline=1';
$youtube_url           = get_user_meta( $user_id, '_apollo_youtube_url', true );
$youtube_embed         = $default_youtube_embed;
if ( $youtube_url ) {
	preg_match( '/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^&]+)/', $youtube_url, $yt_matches );
	if ( ! empty( $yt_matches[1] ) ) {
		$youtube_embed = 'https://www.youtube.com/embed/' . $yt_matches[1]
			. '?autoplay=1&mute=1&loop=1&playlist=' . $yt_matches[1]
			. '&controls=0&showinfo=0&modestbranding=1&rel=0&iv_load_policy=3&playsinline=1';
	}
}

$soundcloud_url = get_user_meta( $user_id, '_apollo_soundcloud_url', true );
if ( ! $soundcloud_url ) {
	$soundcloud_url = 'https://soundcloud.com/apollo-rio/welcome-to-rio';
}
$soundcloud_widget = 'https://w.soundcloud.com/player/?url=' . rawurlencode( $soundcloud_url ) . '&auto_play=false&show_artwork=false';

// Sound preferences
$sound_prefs = get_user_meta( $user_id, '_apollo_sound_preferences', true );
$sound_tags  = array();
if ( ! empty( $sound_prefs ) && is_array( $sound_prefs ) ) {
	foreach ( $sound_prefs as $term_id ) {
		$term = get_term( (int) $term_id );
		if ( $term && ! is_wp_error( $term ) ) {
			$sound_tags[] = $term->name;
		}
	}
} elseif ( is_string( $sound_prefs ) && ! empty( $sound_prefs ) ) {
	$sound_tags = array_map( 'trim', explode( ',', $sound_prefs ) );
}

// Tags structure (nucleo ignored by request)
$tag_items = array();

// Profile link (default when missing)
$profile_link_raw   = get_user_meta( $user_id, '_apollo_website', true );
$profile_link_label = $profile_link_raw ?: 'sem.link.com.br';
$profile_link       = $profile_link_label;
if ( ! preg_match( '/^https?:\/\//i', $profile_link ) ) {
	$profile_link = 'https://' . $profile_link;
}

// Stats (real values where available)
$fav_count     = (int) get_user_meta( $user_id, '_apollo_fav_count', true );
$profile_views = (int) get_user_meta( $user_id, '_apollo_profile_views', true );
$ranking       = get_user_meta( $user_id, '_apollo_ranking', true );
// Record profile view (only for OTHER users viewing this profile)
if ( ! $is_own && function_exists( 'Apollo\\Users\\apollo_record_profile_view' ) ) {
	\Apollo\Users\apollo_record_profile_view( $user_id, get_current_user_id() ?: null );
	// Re-read updated count
	$profile_views = (int) get_user_meta( $user_id, '_apollo_profile_views', true );
}

$hits_display    = $profile_views >= 1000
	? number_format( $profile_views / 1000, 1 ) . 'k'
	: (string) $profile_views;
$ranking_display = $ranking ? '#' . $ranking : '#-';
$rate_display    = '0.0';

// ═══ RATINGS DATA (server-side — security enforced) ═══
$is_logged_in    = is_user_logged_in();
$is_admin        = current_user_can( 'manage_options' );
$current_user_id = get_current_user_id();

// Rating averages & user's own votes (always needed for icon fill state)
$rating_averages = \Apollo\Users\Components\RatingHandler::get_averages( $user_id );
$rating_counts   = \Apollo\Users\Components\RatingHandler::get_vote_counts( $user_id );
$user_votes      = $is_logged_in ? \Apollo\Users\Components\RatingHandler::get_user_votes( $current_user_id, $user_id ) : array();

// Confiável data: visible to ALL users (safety feature)
$confiavel_count   = $rating_counts['confiavel'] ?? 0;
$confiavel_avatars = \Apollo\Users\Components\RatingHandler::get_voter_avatars( $user_id, 'confiavel', 4 );
$confiavel_display = $confiavel_count >= 1000
	? number_format( $confiavel_count / 1000, 1, ',', '.' ) . 'k'
	: number_format( $confiavel_count, 0, ',', '.' );

// Sexy/Legal data: ADMIN ONLY — NEVER reaches HTML for non-admin
$sexy_count    = 0;
$sexy_avatars  = array();
$sexy_display  = '0';
$legal_count   = 0;
$legal_avatars = array();
$legal_display = '0';

if ( $is_admin ) {
	$sexy_count    = $rating_counts['sexy'] ?? 0;
	$sexy_avatars  = \Apollo\Users\Components\RatingHandler::get_voter_avatars( $user_id, 'sexy', 4 );
	$sexy_display  = $sexy_count >= 1000
		? number_format( $sexy_count / 1000, 1, ',', '.' ) . 'k'
		: number_format( $sexy_count, 0, ',', '.' );
	$legal_count   = $rating_counts['legal'] ?? 0;
	$legal_avatars = \Apollo\Users\Components\RatingHandler::get_voter_avatars( $user_id, 'legal', 4 );
	$legal_display = $legal_count >= 1000
		? number_format( $legal_count / 1000, 1, ',', '.' ) . 'k'
		: number_format( $legal_count, 0, ',', '.' );
}

// Feed posts
$user_posts = get_posts(
	array(
		'author'         => $user_id,
		'posts_per_page' => 6,
		'post_status'    => 'publish',
		'post_type'      => array( 'post', 'apollo_event', 'apollo_classified' ),
	)
);

// Depoimentos
$depoimentos = get_comments(
	array(
		'type'   => 'apollo_depoimento',
		'parent' => $user_id,
		'number' => 2,
		'offset' => 0,
		'status' => 'approve',
	)
);
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php echo esc_html( $display_name ); ?> apollo clubber rio bra</title>

	<!-- Apollo CDN: Base Stylesheet + GSAP + RemixIcon + jQuery -->
	<script src="https://cdn.apollo.rio.br/v1.0.0/core.min.js?v=1.0.0" fetchpriority="high"></script>

	<!-- Material Symbols for Player Icons -->
	<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">

	<!-- SoundCloud Widget API -->
	<script src="https://w.soundcloud.com/player/api.js"></script>

	<!-- Navbar CSS/JS (from apollo-templates) -->
	<?php if ( defined( 'APOLLO_TEMPLATES_URL' ) && defined( 'APOLLO_TEMPLATES_VERSION' ) ) : ?>
		<link rel="stylesheet"
			href="<?php echo esc_url( APOLLO_TEMPLATES_URL . 'assets/css/navbar.css' ); ?>?v=<?php echo esc_attr( APOLLO_TEMPLATES_VERSION ); ?>">
		<script
			src="<?php echo esc_url( APOLLO_TEMPLATES_URL . 'assets/js/navbar.js' ); ?>?v=<?php echo esc_attr( APOLLO_TEMPLATES_VERSION ); ?>"
			defer></script>
	<?php endif; ?>

	<style>
		/* ═══════════════════════════════════════════════════════════
			APOLLO LUXURY PROFILE — MINIMALIST GRADE
			Background: #fff | Accent: var(--primary) Orange
			═══════════════════════════════════════════════════════════ */

		@keyframes fadeUp {
			from {
				opacity: 0;
				transform: translateY(32px);
			}

			to {
				opacity: 1;
				transform: translateY(0);
			}
		}

		@keyframes fadeIn {
			from {
				opacity: 0;
			}

			to {
				opacity: 1;
			}
		}

		@keyframes pulseGlow {

			0%,
			100% {
				box-shadow: 0 0 0 0 rgba(0, 0, 0, 0.0);
			}

			50% {
				box-shadow: 0 0 19px 5px rgba(0, 0, 0, 0.045);
			}
		}

		@keyframes breathe {

			0%,
			100% {
				opacity: 0.15;
			}

			50% {
				opacity: 0.35;
			}
		}

		* {
			box-sizing: border-box;
			margin: 0;
			padding: 0;
		}

		body {
			background: #fff;
			color: var(--txt-color);
			font-family: var(--ff-main);
			font-size: 15px;
			line-height: 1.5;
			-webkit-font-smoothing: antialiased;
			-moz-osx-font-smoothing: grayscale;
			overflow-x: hidden;
			padding-top: 10px;
		}

		a {
			color: inherit;
			text-decoration: none;
			transition: color 0.25s var(--ease-default);
		}

		img {
			display: block;
			max-width: 100%;
			height: auto;
		}

		.container {
			width: 100%;
			padding: 0 5px;
			margin: 0 auto;
		}

		/* ═══════════════════ HERO WITH YOUTUBE VIDEO ═══════════════════ */
		.hero {
			position: relative;
			padding: 0 0 var(--space-3) 0;
			animation: fadeIn 0.8s var(--ease-default);
		}

		.hero-media {
			width: 100%;
			height: 320px;
			padding: 0px;
			border-radius: var(--radius-lg);
			overflow: hidden;
			position: relative;
			background: var(--black-1);
			border: 0.3px solid rgba(0, 0, 0, 0.035);
			margin-bottom: -145px;
			box-shadow:
				0 20px 60px -12px rgba(0, 0, 0, 0.08),
				0 0 0 1px rgba(0, 0, 0, 0.02);
		}

		.hero-media iframe {
			position: absolute;
			top: 50%;
			left: 50%;
			width: 120%;
			height: 120%;
			transform: translate(-50%, -50%) scale(1.3);
			border: none;
			padding: 0px;
			pointer-events: none;
		}

		.hero-media::after {
			content: '';
			position: absolute;
			inset: 0;
			background:
				radial-gradient(circle at 30% 40%, rgba(244, 95, 0, 0.06) 0%, transparent 60%),
				linear-gradient(to bottom, transparent 50%, rgba(0, 0, 0, 0.12) 100%);
			animation: breathe 8s ease-in-out infinite;
			pointer-events: none;
			z-index: 1;
		}

		/* ═══════════════════ PROFILE CARD ═══════════════════ */
		.profile-card {
			background: #fff;
			max-width: 95%;
			margin: 0 3% 0 2%;
			border: 0.3px solid var(--border);
			border-radius: var(--radius-lg);
			padding: 1.5rem;
			display: flex;
			flex-direction: column;
			box-shadow:
				0 32px 64px -24px rgba(0, 0, 0, 0.06),
				0 0 0 1px rgba(0, 0, 0, 0.01);
			position: relative;
			animation: fadeUp 0.8s var(--ease-default) 0.1s both;
		}

		/* Profile Controls - Only visible to profile owner */
		.profile-controls {
			position: absolute;
			top: var(--space-3);
			right: var(--space-3);
			display: flex;
			gap: 4px;
			z-index: 10;
		}

		.profile-control-btn {
			width: 32px;
			height: 32px;
			border-radius: 50%;
			background: var(--gray-1);
			border: 1px solid var(--border);
			display: flex;
			align-items: center;
			justify-content: center;
			cursor: pointer;
			transition: all 0.3s var(--ease-smooth);
			color: var(--gray-9);
			font-size: 16px;
		}

		.profile-control-btn:hover {
			background: var(--black-1);
			color: #fff;
			transform: translateY(-2px);
		}

		.profile-header {
			display: flex;
			gap: 15px;
			margin-bottom: var(--space-3);
		}

		.avatar {
			width: 130px;
			height: 130px;
			border-radius: 30%;
			border: 3px solid #fff;
			background: var(--gray-2);
			object-fit: cover;
			box-shadow:
				0 16px 40px -8px rgba(0, 0, 0, 0.035),
				0 0 0 1px rgba(0, 0, 0, 0.03);
			flex-shrink: 0;
			transition: transform 0.6s var(--ease-smooth);
			cursor: pointer;
			margin-top: -40px;
		}

		.avatar:hover {
			transform: scale(1.05) rotate(-1deg);
		}

		.profile-identity {
			flex: 1;
			display: flex;
			flex-direction: column;
			gap: 4px;
			padding: 0px;
		}

		.name {
			font-size: clamp(37px, 7vw, 45px);
			font-weight: 700;
			line-height: 1;
			color: var(--black-1);
			letter-spacing: -0.03em;
		}

		.no-sel {
			-webkit-user-select: none;
			-moz-user-select: none;
			-ms-user-select: none;
			user-select: none;
		}

		.user-membership-badge i {
			font-size: 1.7rem;
			margin: -2px 0 2px;
			display: inline-block;
			color: rgba(0, 0, 0, .33);
			mix-blend-mode: hard-light;
			cursor: crosshair;
		}


		.handle {
			font-size: 15px;
			color: var(--gray-9);
			font-weight: 300;
			transition: color 0.3s;
			cursor: pointer;
			line-height: 1.5;
		}

		.handle:hover {
			color: var(--primary);
		}

		.tags {
			display: flex;
			gap: 8px;
			flex-wrap: wrap;
			margin: 0px;
		}

		.tag {
			font-family: var(--ff-mono);
			font-size: 0.75rem;
			text-transform: uppercase;
			padding: 4px 10px;
			border: 1px solid var(--border);
			border-radius: 100px;
			color: var(--gray-10);
			background: var(--gray-1);
			letter-spacing: 0.06em;
			font-weight: 600;
			transition: all 0.4s var(--ease-smooth);
			cursor: crosshair;
		}

		.tag:hover {
			background: var(--black-1);
			color: #fff;
			border-color: var(--black-1);
			transform: translateY(-2px);
		}

		.bio {
			font-size: 15px;
			color: var(--txt-color);
			line-height: 1.6;
			margin: 12px 0;
		}

		.link {
			font-family: var(--ff-main);
			font-size: 14px;
			color: var(--primary);
			display: inline-flex;
			align-items: center;
			gap: 4px;
			font-weight: 300;
			transition: gap 0.3s var(--ease-default);
		}

		.link:hover {
			gap: 6px;
		}

		.sound-preferences {
			display: flex;
			gap: 6px;
			flex-wrap: wrap;
			margin: 12px 0;
		}

		/* ═══════════════════ SOUNDCLOUD PLAYER ═══════════════════ */
		.player-section {
			margin-top: auto;
			padding-top: 5px;
			border-top: 1px solid var(--border);
		}

		.player-card {
			padding: 0px;
			position: relative;
			transition: all 0.6s var(--ease-smooth);
		}

		.player-card:hover {
			transform: scale(1.02);
		}

		.player-header {
			display: flex;
			justify-content: space-between;
			align-items: center;
			margin-bottom: 12px;
		}

		.player-logo {
			color: var(--black-5);
			opacity: .35;
			mix-blend-mode: hard-light;
			font-size: 26px;
			transition: transform 0.6s var(--ease-smooth);
		}

		.player-logo:hover {
			transform: rotate(-10deg) scale(1.15);
		}

		.player-content {
			display: flex;
			justify-content: space-between;
			align-items: center;
			margin-bottom: 12px;
		}

		.track-info h4 {
			font-size: 14px;
			font-weight: 600;
			color: var(--black-1);
			margin: 0 0 2px 0;
			letter-spacing: -0.01em;
		}

		.track-info p {
			font-size: 0.75rem;
			color: var(--txt-muted);
			margin: 0;
		}

		.play-button {
			width: 48px;
			height: 48px;
			border-radius: 50%;
			background: linear-gradient(135deg, #fff, var(--gray-1));
			border: 1px solid rgba(0, 0, 0, 0.04);
			box-shadow:
				0 6px 20px -4px rgba(0, 0, 0, 0.08),
				inset 0 1px 0 rgba(255, 255, 255, 1);
			display: flex;
			align-items: center;
			justify-content: center;
			cursor: pointer;
			transition: all 0.6s var(--ease-smooth);
			flex-shrink: 0;
		}

		.play-button:hover {
			transform: scale(1.12);
			box-shadow: 0 8px 24px -4px rgba(0, 0, 0, 0.05);
		}

		.play-button:active {
			transform: scale(0.96);
		}

		.play-button.is-playing {
			animation: pulseGlow 2s ease-in-out infinite;
		}

		.play-button .material-symbols-outlined {
			font-size: 26px;
			color: var(--black-1);
		}

		.progress-bar {
			display: flex;
			align-items: center;
			gap: 10px;
		}

		.progress-track {
			flex: 1;
			height: 5px;
			background: rgba(0, 0, 0, 0.06);
			border-radius: 100px;
			position: relative;
			cursor: pointer;
			overflow: hidden;
			transition: height 0.2s;
		}

		.progress-track:hover {
			height: 7px;
		}

		.progress-fill {
			position: absolute;
			left: 0;
			top: 0;
			height: 100%;
			width: 0%;
			background: linear-gradient(90deg, var(--primary), #ff8a4c);
			border-radius: 100px;
			transition: width 0.4s linear;
			box-shadow: 0 0 8px rgba(244, 95, 0, 0.04);
		}

		.progress-time {
			font-family: var(--ff-mono);
			font-size: 0.69rem;
			color: var(--txt-muted);
			min-width: 40px;
			text-align: right;
			font-variant-numeric: tabular-nums;
			font-weight: 600;
		}

		.member-badge {
			text-align: center;
			font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
			font-size: 0.75rem;
			color: rgba(0, 0, 0, 0.3);
			mix-blend-mode: hard-light;
			letter-spacing: 0.12em;
			margin: 25px auto 5px;
			padding: 0px;
			text-transform: uppercase;
			font-weight: 500;
		}

		#sc-widget {
			position: absolute;
			width: 1px;
			height: 1px;
			opacity: 0;
			pointer-events: none;
		}

		/* ═══════════════════ STATS SECTION ═══════════════════ */
		.stats-section {
			margin-top: 12px;
			display: flex;
			flex-direction: column;
			gap: 10px;
			animation: fadeUp 0.8s var(--ease-default) 0.2s both;
		}

		.stat-card {
			background: #fff;
			border: 1px solid var(--border);
			border-radius: var(--radius);
			padding: var(--space-3);
			box-shadow: 0 8px 24px -8px rgba(0, 0, 0, 0.04);
			transition: all 0.6s var(--ease-smooth);
		}

		.stat-card:hover {
			border-color: var(--gray-5);
			transform: translateY(-3px);
			box-shadow: 0 16px 40px -12px rgba(0, 0, 0, 0.05);
		}

		.stat-header {
			display: flex;
			justify-content: space-between;
			align-items: center;
			margin-bottom: 12px;
		}

		.stat-title {
			font-family: var(--ff-mono);
			font-size: 0.827rem;
			text-transform: uppercase;
			font-weight: 700;
			color: var(--txt-muted);
			letter-spacing: 0.08em;
			display: flex;
			align-items: center;
			gap: 6px;
		}

		.stat-grid {
			display: grid;
			grid-template-columns: 1fr 1fr;
			gap: 8px;
		}

		.stat-item {
			background: var(--gray-1);
			padding: 10px;
			border-radius: var(--radius-sm);
			text-align: center;
			transition: all 0.6s var(--ease-smooth);
			cursor: default;
			border: 1px solid transparent;
		}

		.stat-item:hover {
			background: var(--black-1);
			transform: translateY(-2px);
			border-color: var(--black-1);
		}

		.stat-item:hover .stat-label,
		.stat-item:hover .stat-value {
			color: #fff;
		}

		.stat-label {
			font-size: 16px;
			color: var(--gray-8);
			text-transform: uppercase;
			display: block;
			margin-bottom: 4px;
			font-weight: 600;
			letter-spacing: 0.05em;
			transition: color 0.3s;
		}

		.stat-value {
			font-size: 1.29rem;
			font-weight: 800;
			color: var(--black-1);
			font-variant-numeric: tabular-nums;
			transition: color 0.3s;
			letter-spacing: -0.02em;
		}

		/* Ratings - Start at zero */
		.ratings-content {
			display: grid;
			grid-template-columns: repeat(3, 1fr);
			gap: var(--space-2);
		}

		.rating-column {
			display: flex;
			flex-direction: column;
			align-items: center;
			gap: 4px;
		}

		.rating-column:nth-child(1) {
			flex-direction: column;
		}

		.rating-column:nth-child(2),
		.rating-column:nth-child(3) {
			flex-direction: column-reverse;
		}

		.rating-label {
			font-size: 0.69rem;
			color: rgba(0, 0, 0, .95);
			mix-blend-mode: hard-light;
			font-weight: 300;
			text-transform: uppercase;
			letter-spacing: 0.05em;
			text-align: center;
		}

		.rating-icons {
			display: flex;
			gap: 4px;
			justify-content: center;
			margin-bottom: 5px;
		}

		.rating-icon {
			font-size: 26px;
			color: rgba(0, 0, 0, .85);
			opacity: .3;
			cursor: pointer;
			transition: all 0.6s var(--ease-smooth);
			filter: grayscale(100%);
		}

		.rating-icon:hover {
			opacity: 0.65;
			transform: scale(1.2);
		}

		.rating-icon.filled {
			opacity: 1;
		}

		.rating-icon.filled:hover {
			transform: scale(1.15) rotate(-3deg);
		}

		/* ═══════════════════ CONFIÁVEL META (public safety) ═══════════════════ */
		.confiavel-section {
			margin-top: 14px;
			padding-top: 12px;
			border-top: 1px solid var(--border);
		}

		.confiavel-trigger {
			display: block;
			text-decoration: none;
			color: inherit;
			cursor: pointer;
			transition: opacity 0.3s;
		}

		.confiavel-trigger:hover {
			opacity: 0.8;
		}

		.confiavel-meta {
			display: flex;
			align-items: center;
			gap: 16px;
		}

		.confiavel-avatars {
			display: flex;
		}

		.confiavel-avatars .av {
			width: 28px;
			height: 28px;
			border-radius: 50%;
			border: 2px solid rgba(0, 0, 0, .8);
			margin-right: -8px;
			overflow: hidden;
			flex-shrink: 0;
		}

		.confiavel-avatars .av img {
			width: 100%;
			height: 100%;
			object-fit: cover;
			filter: grayscale(100%);
			transition: filter 0.4s;
		}

		.confiavel-trigger:hover .confiavel-avatars .av img {
			filter: grayscale(0%);
		}

		.confiavel-avatars .av-empty {
			display: flex;
			align-items: center;
			justify-content: center;
			background: var(--gray-2);
			color: var(--gray-6);
			font-size: 14px;
		}

		.confiavel-stat {
			font-family: var(--ff-mono);
			font-size: 11px;
			color: rgba(0, 0, 0, .45);
			display: flex;
			align-items: center;
			gap: 4px;
		}

		.confiavel-stat i {
			font-size: 13px;
		}

		/* ═══════════════════ ADMIN SUMMARY ROWS ═══════════════════ */
		.rating-admin-summary {
			margin-top: 14px;
			padding-top: 12px;
			border-top: 1px dashed rgba(244, 95, 0, 0.3);
			display: flex;
			flex-direction: column;
			gap: 10px;
		}

		.admin-badge {
			font-family: var(--ff-mono);
			font-size: 9px;
			color: #f45f00;
			text-transform: uppercase;
			letter-spacing: 0.1em;
			font-weight: 700;
			display: flex;
			align-items: center;
			gap: 4px;
		}

		.admin-rating-row {
			padding: 6px 8px;
			border-radius: var(--radius-sm);
			background: var(--gray-1);
			transition: background 0.3s;
		}

		.admin-rating-row:hover {
			background: rgba(244, 95, 0, 0.06);
		}

		/* ═══════════════════ VOTER LIST MODAL ═══════════════════ */
		.voter-modal {
			display: none;
			position: fixed;
			inset: 0;
			background: rgba(140, 140, 140, 0.13);
			backdrop-filter: blur(6px) grayscale(100%);
			z-index: 9999999;
			justify-content: center;
			align-items: flex-end;
		}

		.voter-modal.active {
			display: flex;
		}

		.voter-modal-content {
			background: #fff;
			border-radius: var(--radius-lg) var(--radius-lg) 0 0;
			padding: var(--space-4) var(--space-4) var(--space-5);
			max-width: 480px;
			width: 100%;
			max-height: 80vh;
			overflow-y: auto;
			box-shadow: 0 -20px 60px -10px rgba(0, 0, 0, 0.08);
			animation: slideUp 0.35s cubic-bezier(0.16, 1, 0.3, 1);
		}

		@keyframes slideUp {
			from {
				transform: translateY(100%);
				opacity: 0;
			}

			to {
				transform: translateY(0);
				opacity: 1;
			}
		}

		.voter-modal-header {
			display: flex;
			justify-content: space-between;
			align-items: center;
			margin-bottom: var(--space-3);
		}

		.voter-modal-title {
			font-size: 18px;
			font-weight: 700;
			color: var(--black-1);
			display: flex;
			align-items: center;
			gap: 8px;
		}

		.voter-modal-title i {
			font-size: 20px;
		}

		.voter-modal-close {
			width: 32px;
			height: 32px;
			border-radius: 50%;
			background: var(--gray-1);
			border: none;
			display: flex;
			align-items: center;
			justify-content: center;
			cursor: pointer;
			transition: all 0.3s;
			font-size: 18px;
		}

		.voter-modal-close:hover {
			background: var(--black-1);
			color: #fff;
		}

		.voter-modal-count {
			font-family: var(--ff-mono);
			font-size: 12px;
			color: var(--txt-muted);
			margin-bottom: var(--space-3);
			padding-bottom: 8px;
			border-bottom: 1px solid var(--border);
		}

		/* ═══ Voter Radar List ═══ */
		.voter-list {
			display: flex;
			flex-direction: column;
			gap: 0;
		}

		.voter-item {
			display: flex;
			align-items: center;
			gap: 12px;
			padding: 10px 8px;
			border-bottom: 1px solid var(--border);
			transition: background 0.2s;
			text-decoration: none;
			color: inherit;
		}

		.voter-item:hover {
			background: var(--gray-1);
		}

		.voter-item:last-child {
			border-bottom: none;
		}

		.voter-avatar {
			width: 40px;
			height: 40px;
			border-radius: 50%;
			object-fit: cover;
			border: 2px solid var(--border);
			flex-shrink: 0;
			filter: grayscale(40%);
			transition: filter 0.3s;
		}

		.voter-item:hover .voter-avatar {
			filter: grayscale(0%);
		}

		.voter-info {
			flex: 1;
			min-width: 0;
		}

		.voter-name {
			font-size: 14px;
			font-weight: 600;
			color: var(--black-1);
			white-space: nowrap;
			overflow: hidden;
			text-overflow: ellipsis;
		}

		.voter-timestamp {
			font-family: var(--ff-mono);
			font-size: 10px;
			color: var(--txt-muted);
			letter-spacing: 0.02em;
		}

		.voter-score {
			display: flex;
			gap: 3px;
			flex-shrink: 0;
		}

		.voter-score i {
			font-size: 14px;
			opacity: 0.25;
		}

		.voter-score i.active {
			opacity: 1;
			color: var(--primary, #f45f00);
		}

		.voter-empty {
			text-align: center;
			padding: 40px 20px;
			color: var(--txt-muted);
			font-family: var(--ff-mono);
			font-size: 13px;
		}

		.voter-load-more {
			display: block;
			width: 100%;
			padding: 12px;
			margin-top: 8px;
			background: var(--gray-1);
			border: none;
			border-radius: var(--radius-sm);
			font-family: var(--ff-mono);
			font-size: 12px;
			font-weight: 600;
			color: var(--black-1);
			cursor: pointer;
			transition: background 0.3s;
			text-align: center;
		}

		.voter-load-more:hover {
			background: var(--black-1);
			color: #fff;
		}

		/* ═══ Admin Accordion (Sexy/Legal voters) ═══ */
		.admin-accordion-header {
			display: flex;
			justify-content: space-between;
			align-items: center;
			padding: 10px 0;
			cursor: pointer;
			border-bottom: 1px solid var(--border);
			transition: opacity 0.2s;
		}

		.admin-accordion-header:hover {
			opacity: 0.7;
		}

		.admin-accordion-header .acc-label {
			font-family: var(--ff-mono);
			font-size: 13px;
			font-weight: 700;
			text-transform: uppercase;
			letter-spacing: 0.05em;
			display: flex;
			align-items: center;
			gap: 6px;
		}

		.admin-accordion-header .acc-arrow {
			font-size: 16px;
			transition: transform 0.3s;
		}

		.admin-accordion-header.open .acc-arrow {
			transform: rotate(180deg);
		}

		.admin-accordion-body {
			max-height: 0;
			overflow: hidden;
			transition: max-height 0.4s cubic-bezier(0.16, 1, 0.3, 1);
		}

		.admin-accordion-body.open {
			max-height: 2000px;
		}

		@media (min-width: 980px) {
			.voter-modal {
				align-items: center;
			}

			.voter-modal-content {
				border-radius: var(--radius-lg);
				max-height: 70vh;
			}
		}

		/* Publications - Clean Simple List */
		.pub-list {
			display: flex;
			flex-direction: column;
			gap: 6px;
		}

		.pub-item {
			display: block;
			padding: 10px 0;
			transition: all 0.3s var(--ease-smooth);
			border-bottom: 1px solid var(--border);
		}

		.pub-item:last-child {
			border-bottom: none;
		}

		.pub-item:hover {
			padding-left: 8px;
		}

		.pub-item:hover .pub-title {
			color: var(--primary);
		}

		.pub-title {
			font-size: 15px;
			font-weight: 700;
			color: var(--black-1);
			margin-bottom: 2px;
			transition: color 0.3s;
		}

		.pub-meta {
			font-size: 0.896rem;
			color: var(--txt-muted);
			font-family: var(--ff-mono);
		}

		/* ═══════════════════ FEED SECTION WITH TABS ═══════════════════ */
		.feed-section {
			margin-top: var(--space-6);
			animation: fadeUp 0.8s var(--ease-default) 0.4s both;
		}

		.feed-tabs {
			display: flex;
			gap: var(--space-2);
			margin-bottom: 28px;
			padding-bottom: var(--space-3);
			border-bottom: 1px solid var(--border);
			overflow-x: auto;
			-webkit-overflow-scrolling: touch;
			scrollbar-width: none;
		}

		.feed-tabs::-webkit-scrollbar {
			display: none;
		}

		/* Tab: All Posts = All content posted by user */
		/* Tab: Events = Events favorited by user */
		/* Tab: Classifieds = Classified ads posted by user */
		.feed-tab {
			padding: 10px 18px;
			font-family: var(--ff-mono);
			font-size: 0.896rem;
			font-weight: 700;
			color: var(--txt-muted);
			border-radius: 100px;
			border: 1px solid transparent;
			background: transparent;
			cursor: pointer;
			transition: all 0.3s var(--ease-smooth);
			letter-spacing: 0.04em;
			white-space: nowrap;
			flex-shrink: 0;
		}

		.feed-tab:hover {
			background: var(--gray-1);
			color: var(--black-1);
		}

		.feed-tab.active {
			background: var(--black-1);
			color: #fff;
			border-color: var(--black-1);
		}

		.feed-grid {
			display: grid;
			grid-template-columns: 1fr;
			gap: var(--space-3);
			margin-bottom: var(--space-6);
		}

		/* Feed cards are clickable buttons that open post page */
		.feed-card {
			background: #fff;
			border: 1px solid var(--border);
			border-radius: var(--radius-lg);
			overflow: hidden;
			position: relative;
			transition: all 0.5s var(--ease-smooth);
			box-shadow: 0 4px 16px -4px rgba(0, 0, 0, 0.04);
			cursor: pointer;
		}

		.feed-card:hover {
			border-color: var(--black-1);
			transform: translateY(-6px);
			box-shadow: 0 24px 60px -16px rgba(0, 0, 0, 0.05);
		}

		.feed-image {
			width: 100%;
			height: 200px;
			object-fit: cover;
			filter: grayscale(100%) contrast(1.05);
			transition: all 0.6s var(--ease-smooth);
		}

		.feed-card:hover .feed-image {
			filter: grayscale(0%);
			transform: scale(1.05);
		}

		.feed-body {
			padding: var(--space-4);
		}

		.feed-meta {
			font-family: var(--ff-mono);
			font-size: 0.75rem;
			color: var(--primary);
			text-transform: uppercase;
			font-weight: 700;
			margin-bottom: 10px;
			letter-spacing: 0.08em;
		}

		.feed-title {
			font-size: 20px;
			font-weight: 800;
			margin-bottom: 10px;
			color: var(--black-1);
			letter-spacing: -0.02em;
			line-height: 1.2;
		}

		.feed-text {
			font-size: 14px;
			color: var(--txt-main);
			line-height: 1.6;
			margin-bottom: 12px;
		}

		/* Reactions: wow and comments */
		.feed-reactions {
			display: flex;
			align-items: center;
			gap: var(--space-3);
			margin-top: 12px;
			padding-top: 12px;
			border-top: 1px solid var(--border);
		}

		.reaction-btn {
			display: flex;
			align-items: center;
			gap: 6px;
			font-family: var(--ff-mono);
			font-size: 14px;
			color: var(--gray-9);
			background: transparent;
			border: none;
			cursor: pointer;
			transition: all 0.3s var(--ease-smooth);
		}

		.reaction-btn:hover {
			color: var(--primary);
			transform: scale(1.05);
		}

		.reaction-btn i {
			font-size: 20px;
		}

		.reaction-btn.reacted {
			color: var(--primary);
		}

		.feed-card.dark {
			background: var(--black-1);
			border-color: var(--black-1);
		}

		.feed-card.dark .feed-meta {
			color: #fff;
		}

		.feed-card.dark .feed-title {
			color: #fff;
		}

		.feed-card.dark .feed-text {
			color: var(--gray-5);
		}

		.playlist-visual {
			margin: var(--space-3) 0;
			background: rgba(255, 255, 255, 0.08);
			padding: var(--space-3);
			border-radius: var(--radius-sm);
			display: flex;
			align-items: center;
			gap: var(--space-3);
			transition: background 0.4s;
		}

		.playlist-visual:hover {
			background: rgba(255, 255, 255, 0.12);
		}

		.playlist-visual i {
			font-size: 36px;
			color: #fff;
			transition: transform 0.4s var(--ease-smooth);
		}

		.playlist-visual:hover i {
			transform: scale(1.15) rotate(-5deg);
		}

		.playlist-bar-container {
			height: 4px;
			flex: 1;
			background: rgba(255, 255, 255, 0.2);
			position: relative;
			border-radius: 100px;
			overflow: hidden;
		}

		.playlist-bar-fill {
			width: 70%;
			height: 100%;
			background: linear-gradient(90deg, #fff, rgba(255, 255, 255, 0.6));
			position: absolute;
			border-radius: 100px;
		}

		/* ═══════════════════ TESTIMONIALS - CLEAN MINIMALIST ═══════════════════ */
		.testimonials-section {
			margin: 64px 0 80px 0;
			animation: fadeUp 0.8s var(--ease-default) 0.6s both;
		}

		.section-divider {
			display: flex;
			align-items: center;
			gap: 4px;
			margin-bottom: var(--space-5);
		}

		.divider-line {
			flex: 1;
			height: 1px;
			background: linear-gradient(90deg, transparent, var(--border), transparent);
		}

		.divider-label {
			font-family: var(--ff-mono);
			font-size: 0.896rem;
			text-transform: uppercase;
			letter-spacing: 0.2em;
			color: var(--gray-8);
			white-space: nowrap;
			font-weight: 700;
		}

		.testimonials-header {
			margin-bottom: var(--space-5);
		}

		.testimonials-title {
			font-size: 32px;
			font-weight: 800;
			color: var(--black-1);
			letter-spacing: -0.03em;
			line-height: 1.1;
			margin-bottom: var(--space-2);
		}

		.testimonials-subtitle {
			font-family: var(--ff-mono);
			font-size: 0.896rem;
			color: var(--txt-muted);
			font-weight: 600;
		}

		.testimonials-grid {
			display: flex;
			flex-direction: column;
			gap: 32px;
		}

		/* Clean testimonial design - no borders */
		.testimonial-container {
			width: 100%;
			border-radius: 20px;
			display: grid;
			grid-template-columns: auto 1fr;
			align-items: center;
			column-gap: 20px;
			padding: 24px;
			position: relative;
		}

		.testimonial-img {
			width: 145px;
			height: 145px;
			scale: 1.2;
			border-radius: 10px;
			object-fit: cover;
		}

		.testimonial-content {
			display: flex;
			flex-direction: column;
			justify-content: space-between;
			min-height: 100px;
			padding: 0 2rem;
		}

		.testimonial-copy {
			font-size: 1rem;
			line-height: 1.7;
			color: var(--black-1);
			margin-bottom: 16px;
		}

		.testimonial-detail {
			display: flex;
			flex-direction: column;
			gap: 4px;
		}

		.testimonial-name {
			font-size: 1rem;
			font-weight: 700;
			color: var(--black-1);
		}

		.testimonial-post {
			font-size: 0.896rem;
			color: #c1c1c1;
			font-family: var(--ff-mono);
		}

		/* Statistics Modal */
		.stats-modal {
			display: none;
			position: fixed;
			inset: 0;
			background: rgba(140, 140, 140, 0.13);
			backdrop-filter: blur(6px) grayscale(100%);
			z-index: 999999;
			justify-content: center;
			align-items: center;
		}

		.stats-modal.active {
			display: flex;
		}

		.stats-modal-content {
			background: #fff;
			border-radius: var(--radius-lg);
			padding: var(--space-5);
			max-width: 600px;
			width: 90%;
			box-shadow: 0 40px 80px -20px rgba(0, 0, 0, 0.03);
		}

		.stats-modal-header {
			display: flex;
			justify-content: space-between;
			align-items: center;
			margin-bottom: var(--space-4);
		}

		.stats-modal-title {
			font-size: 24px;
			font-weight: 700;
			color: var(--black-1);
		}

		.stats-modal-close {
			width: 32px;
			height: 32px;
			border-radius: 50%;
			background: var(--gray-1);
			border: none;
			display: flex;
			align-items: center;
			justify-content: center;
			cursor: pointer;
			transition: all 0.3s;
		}

		.stats-modal-close:hover {
			background: var(--black-1);
			color: #fff;
		}

		.stats-period-tabs {
			display: flex;
			gap: 8px;
			margin-bottom: var(--space-4);
		}

		.stats-period-tab {
			padding: 8px 16px;
			border-radius: 8px;
			font-family: var(--ff-mono);
			font-size: 0.75rem;
			font-weight: 700;
			background: var(--gray-1);
			border: none;
			cursor: pointer;
			transition: all 0.3s;
		}

		.stats-period-tab.active {
			background: var(--black-1);
			color: #fff;
		}

		.stats-chart-placeholder {
			width: 100%;
			height: 300px;
			background: var(--gray-1);
			border-radius: var(--radius);
			display: flex;
			align-items: center;
			justify-content: center;
			font-family: var(--ff-mono);
			font-size: 14px;
			color: var(--txt-muted);
		}

		/* ═══════════════════ DESKTOP: 980px+ ═══════════════════ */
		@media (min-width: 980px) {
			body {
				padding-top: 4px;
			}

			.container {
				max-width: 1400px;
				padding: 0 var(--space-6);
			}

			.hero {
				padding: 0 0 var(--space-5) 0;
			}

			.hero-media {
				height: 500px;
				margin-bottom: -250px;
			}

			.hero-layout {
				display: grid;
				margin: 0 3% 0 2%;
				grid-template-columns: 1fr 380px;
				gap: 2px;
				position: relative;
				z-index: 10;
			}

			.profile-card {
				padding: 1.5rem;
				margin: 0 2px 0 3.5%;
			}

			.avatar {
				width: 140px;
				height: 140px;
			}

			.name {
				font-size: var(--fs-h2);
			}

			.feed-grid {
				grid-template-columns: repeat(2, 1fr);
				gap: 4px;
			}

			.feed-image {
				height: 280px;
			}

			.testimonial-container {
				padding: 32px 40px;
			}

			.testimonial-img {
				width: 160px;
				height: 160px;
			}

			.testimonial-copy {
				font-size: 1.17rem;
			}
		}

		@media (min-width: 1400px) {
			.container {
				max-width: 1600px;
			}

			.hero-layout {
				grid-template-columns: 1fr 420px;
				gap: var(--space-5);
			}

			.feed-grid {
				grid-template-columns: repeat(3, 1fr);
			}
		}

		/* ─── GLOBAL TOOLTIPS ──────────────────────────────────────────────── */
		[data-tooltip] {
			position: relative;
		}

		[data-tooltip]::before {
			content: attr(data-tooltip);
			position: absolute;
			bottom: calc(100% + 8px);
			left: 50%;
			transform: translateX(-50%) translateY(-4px);
			padding: 6px 12px;
			background: var(--ink);
			color: #fff;
			font-size: 12px;
			font-weight: 500;
			line-height: 1.4;
			white-space: nowrap;
			border-radius: 6px;
			opacity: 0;
			pointer-events: none;
			transition: opacity 0.2s ease, transform 0.2s ease;
			z-index: 1000;
		}

		[data-tooltip]::after {
			content: "";
			position: absolute;
			bottom: calc(100% + 2px);
			left: 50%;
			transform: translateX(-50%);
			border: 6px solid transparent;
			border-top-color: var(--ink);
			opacity: 0;
			pointer-events: none;
			transition: opacity 0.2s ease;
			z-index: 1000;
		}

		[data-tooltip]:hover::before,
		[data-tooltip]:hover::after {
			opacity: 1;
			transform: translateX(-50%) translateY(0);
		}
	</style>
</head>

<body>
	<?php
	// Global Apollo Navbar (from apollo-templates plugin)
	if ( defined( 'APOLLO_TEMPLATES_DIR' ) && file_exists( APOLLO_TEMPLATES_DIR . 'templates/template-parts/navbar.php' ) ) {
		include APOLLO_TEMPLATES_DIR . 'templates/template-parts/navbar.php';
	}
	?>
	<div class="container">
		<section class="hero">
			<div class="hero-media no-sel">
				<iframe src="<?php echo esc_url( $youtube_embed ); ?>" allow="autoplay; encrypted-media" allowfullscreen>
				</iframe>
			</div>

			<div class="hero-layout">
				<div class="profile-card">
					<!-- Profile Controls: Only visible to profile owner -->
					<?php if ( is_user_logged_in() && $is_own ) : ?>
						<div class="profile-controls">
							<button class="profile-control-btn no-sel" id="stats-btn" title="Statistics">
								<i class="ri-bar-chart-fill"></i>
							</button>
							<a href="/editar-perfil"><button class="profile-control-btn no-sel" id="edit-btn"
									title="Edit Profile">
									<i class="ri-pencil-ruler-2-line"></i>
								</button></a>
						</div>
					<?php endif; ?>

					<div class="profile-header">
						<span id="userPicture_ID" class="editable-info no-sel"> <img
								src="<?php echo esc_url( $avatar_url ); ?>" class="avatar"
								alt="<?php echo esc_attr( $display_name ); ?>"></span>
						<div class="profile-identity">
							<h1 class="name"><span id="username_ID"
									class="editable-info no-sel"><?php echo esc_html( $display_name ); ?></span>
								<?php
								// Dynamic membership badge from apollo-membership
								if ( function_exists( 'apollo_get_membership_badge_html' ) ) {
									$badge_html = apollo_get_membership_badge_html( $user_id, 'md' );
									if ( $badge_html ) {
										echo '<span id="user-membership-badge" class="user-membership-badge">' . $badge_html . '</span>';
									}
								} elseif ( $has_membership ) {
									?>
									<span id="user-membership-badge" class="user-membership-badge">
										<i class="ri-verified-badge-fill" title="Verificado" style="color:#4caf50;"></i>
									</span>
								<?php } ?>
							</h1>

							<div class="tags">
								<?php foreach ( $tag_items as $tag ) : ?>
									<span class="tag"><?php echo esc_html( $tag ); ?></span>
								<?php endforeach; ?>
							</div>
							<span class="handle">@<?php echo esc_html( $user->user_login ); ?></span>
						</div>
					</div>

					<p class="bio"><span id="userbio_ID"
							class="editable-info no-sel"><?php echo esc_html( $bio ); ?></span>
					</p>

					<a href="<?php echo esc_url( $profile_link ); ?>" class="link">
						<i class="ri-links-line"></i>
						<span id="userlink_ID"
							class="editable-info no-sel"><?php echo esc_html( $profile_link_label ); ?></span>
					</a>

					<div class="sound-preferences no-sel">
						<?php foreach ( $sound_tags as $pref ) : ?>
							<span class="tag"><?php echo esc_html( $pref ); ?></span>
						<?php endforeach; ?>
					</div>

					<div class="member-badge no-sel">Member since <?php echo esc_html( $member_since ); ?></div>

					<div class="player-section no-sel">
						<iframe id="sc-widget" allow="autoplay" scrolling="no" frameborder="no"
							src="<?php echo esc_url( $soundcloud_widget ); ?>">
						</iframe>

						<div class="player-card">
							<div class="player-header">
								<div class="player-logo">
									<i class="ri-soundcloud-fill"></i>
								</div>
							</div>

							<div class="player-content">
								<div class="track-info">
									<h4 id="track-name">Welcome to Rio</h4>
									<p id="track-artist">Apollo Rio</p>
								</div>
								<button class="play-button" id="play-btn">
									<span class="material-symbols-outlined" id="play-icon">play_arrow</span>
								</button>
							</div>

							<div class="progress-bar">
								<div class="progress-track" id="progress-track">
									<div class="progress-fill" id="progress-fill"></div>
								</div>
								<span class="progress-time" id="time-display">0:00</span>
							</div>
						</div>
					</div>
				</div>

				<div class="stats-section no-sel">
					<!-- Ratings: Voting + Confiável public display -->
					<div class="stat-card" id="rating-card" data-target="<?php echo esc_attr( (string) $user_id ); ?>">
						<div class="stat-header">
							<span class="stat-title no-sel">
								<i class="ri-star-smile-fill"></i>
								Ratings
							</span>
						</div>
						<div class="ratings-content">
							<?php /* ── Sexy (icons visible to all, meta admin-only) ── */ ?>
							<div class="rating-column" data-category="sexy">
								<span class="rating-label no-sel">Sexy</span>
								<div class="rating-icons">
									<?php
									for ( $i = 1; $i <= 3; $i++ ) :
										$filled = ( $user_votes['sexy'] ?? 0 ) >= $i;
										?>
										<i class="<?php echo $filled ? 'ri-heart-3-fill' : 'ri-heart-3-line'; ?> rating-icon<?php echo $filled ? ' filled' : ''; ?>"
											data-filled="ri-heart-3-fill" data-empty="ri-heart-3-line" data-score="<?php echo $i; ?>"></i>
									<?php endfor; ?>
								</div>
							</div>

							<?php /* ── Legal (icons visible to all, meta admin-only) ── */ ?>
							<div class="rating-column" data-category="legal">
								<div class="rating-icons">
									<?php
									for ( $i = 1; $i <= 3; $i++ ) :
										$filled = ( $user_votes['legal'] ?? 0 ) >= $i;
										?>
										<i class="<?php echo $filled ? 'ri-user-smile-fill' : 'ri-user-smile-line'; ?> rating-icon no-sel<?php echo $filled ? ' filled' : ''; ?>"
											data-filled="ri-user-smile-fill" data-empty="ri-user-smile-line" data-score="<?php echo $i; ?>"></i>
									<?php endfor; ?>
								</div>
								<span class="rating-label no-sel">Legal</span>
							</div>

							<?php /* ── Confiável (icons + meta visible to ALL — safety feature) ── */ ?>
							<div class="rating-column" data-category="confiavel">
								<div class="rating-icons">
									<?php
									for ( $i = 1; $i <= 3; $i++ ) :
										$filled = ( $user_votes['confiavel'] ?? 0 ) >= $i;
										?>
										<i class="<?php echo $filled ? 'ri-instance-fill' : 'ri-instance-line'; ?> rating-icon no-sel<?php echo $filled ? ' filled' : ''; ?>"
											data-filled="ri-instance-fill" data-empty="ri-instance-line" data-score="<?php echo $i; ?>"></i>
									<?php endfor; ?>
								</div>
								<span class="rating-label no-sel">Confiável</span>
							</div>
						</div>

						<?php /* ═══ CONFIÁVEL META — PUBLIC (safety against fake profiles) ═══ */ ?>
						<div class="confiavel-section" id="confiavel-public">
							<a href="javascript:void(0)" class="confiavel-trigger" data-category="confiavel" title="Ver quem confia neste usuário">
								<div class="confiavel-meta">
									<div class="confiavel-avatars">
										<?php if ( ! empty( $confiavel_avatars ) ) : ?>
											<?php foreach ( $confiavel_avatars as $cav ) : ?>
												<div class="av"><img src="<?php echo esc_url( $cav['avatar_url'] ); ?>" alt="<?php echo esc_attr( $cav['display_name'] ); ?>"></div>
											<?php endforeach; ?>
										<?php else : ?>
											<div class="av av-empty"><i class="ri-user-3-line"></i></div>
										<?php endif; ?>
									</div>
									<div class="confiavel-stat">
										<i class="ri-shield-check-line"></i>
										<span id="confiavel-count"><?php echo esc_html( $confiavel_display ); ?></span> confiam
									</div>
								</div>
							</a>
						</div>

						<?php /* ═══ ADMIN ONLY: Sexy + Legal + Confiável summary rows ═══ */ ?>
						<?php if ( $is_admin ) : ?>
							<div class="rating-admin-summary" id="rating-admin-summary">
								<div class="admin-badge"><i class="ri-shield-keyhole-line"></i> Admin View</div>

								<a href="javascript:void(0)" class="confiavel-trigger admin-rating-row" data-category="sexy" title="Ver quem votou Sexy">
									<div class="confiavel-meta">
										<div class="confiavel-avatars">
											<?php if ( ! empty( $sexy_avatars ) ) : ?>
												<?php foreach ( $sexy_avatars as $sav ) : ?>
													<div class="av"><img src="<?php echo esc_url( $sav['avatar_url'] ); ?>" alt="<?php echo esc_attr( $sav['display_name'] ); ?>"></div>
												<?php endforeach; ?>
											<?php else : ?>
												<div class="av av-empty"><i class="ri-heart-3-line"></i></div>
											<?php endif; ?>
										</div>
										<div class="confiavel-stat"><i class="ri-heart-3-line"></i> <?php echo esc_html( $sexy_display ); ?> acham sexy</div>
									</div>
								</a>

								<a href="javascript:void(0)" class="confiavel-trigger admin-rating-row" data-category="legal" title="Ver quem votou Legal">
									<div class="confiavel-meta">
										<div class="confiavel-avatars">
											<?php if ( ! empty( $legal_avatars ) ) : ?>
												<?php foreach ( $legal_avatars as $lav ) : ?>
													<div class="av"><img src="<?php echo esc_url( $lav['avatar_url'] ); ?>" alt="<?php echo esc_attr( $lav['display_name'] ); ?>"></div>
												<?php endforeach; ?>
											<?php else : ?>
												<div class="av av-empty"><i class="ri-user-smile-line"></i></div>
											<?php endif; ?>
										</div>
										<div class="confiavel-stat"><i class="ri-user-smile-line"></i> <?php echo esc_html( $legal_display ); ?> acham legal</div>
									</div>
								</a>

								<a href="javascript:void(0)" class="confiavel-trigger admin-rating-row" data-category="confiavel" title="Ver quem votou Confiável">
									<div class="confiavel-meta">
										<div class="confiavel-avatars">
											<?php if ( ! empty( $confiavel_avatars ) ) : ?>
												<?php foreach ( $confiavel_avatars as $cav2 ) : ?>
													<div class="av"><img src="<?php echo esc_url( $cav2['avatar_url'] ); ?>" alt="<?php echo esc_attr( $cav2['display_name'] ); ?>"></div>
												<?php endforeach; ?>
											<?php else : ?>
												<div class="av av-empty"><i class="ri-instance-line"></i></div>
											<?php endif; ?>
										</div>
										<div class="confiavel-stat"><i class="ri-shield-check-line"></i> <?php echo esc_html( $confiavel_display ); ?> confiam</div>
									</div>
								</a>
							</div>
						<?php endif; ?>
					</div>

					<!-- Stats: Ranking replaces Visits -->
					<!-- Ranking #2 = User's position relative to all users based on participation: posts, favorites, interactions -->
					<?php if ( is_user_logged_in() && $is_own ) : ?>
						<div class="stat-card">
							<div class="stat-header">
								<span class="stat-title no-sel">
									<i class="ri-bar-chart-fill"></i>
									Stats
								</span>
							</div>
							<div class="stat-grid">
								<div class="stat-item">
									<span class="stat-label no-sel">Favs</span>
									<span class="stat-value"><?php echo esc_html( (string) $fav_count ); ?></span>
								</div>
								<div class="stat-item">
									<span class="stat-label no-sel">Hits</span>
									<span class="stat-value"><?php echo esc_html( $hits_display ); ?></span>
								</div>
								<div class="stat-item"
									title="User ranking based on total activity: posts, favorites, interactions across the platform">
									<span class="stat-label no-sel">Ranking</span>
									<span class="stat-value"><?php echo esc_html( $ranking_display ); ?></span>
								</div>
								<div class="stat-item">
									<span class="stat-label no-sel">Rate</span>
									<span class="stat-value"><?php echo esc_html( $rate_display ); ?></span>
								</div>
							</div>
						</div>
					<?php endif; ?>

					<!-- Publications: Clean list without borders -->
					<div class="stat-card">
						<div class="stat-header">
							<span class="stat-title no-sel">
								<i class="ri-article-line"></i>
								Pubs
							</span>
						</div>
						<div class="pub-list">
							<?php if ( $user_posts ) : ?>
								<?php foreach ( array_slice( $user_posts, 0, 2 ) as $post ) : ?>
										<a href="<?php echo esc_url( get_permalink( $post ) ); ?>" class="pub-item">
											<span class="pub-title"><?php echo esc_html( get_the_title( $post ) ); ?></span>
											<div class="pub-meta">
												<?php echo wp_kses_post( apollo_time_ago_html( get_the_date( 'Y-m-d H:i:s', $post ) ) ); ?>
											</div>
										</a>
								<?php endforeach; ?>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
		</section>

		<!-- Feed with Tab System -->
		<section class="feed-section">
			<!-- Tab System: All Posts | Events (Favorited) | Classifieds (Posted) -->
			<div class="feed-tabs">
				<button class="feed-tab active no-sel" data-tab="all">All Posts</button>
				<button class="feed-tab no-sel" data-tab="events">Events</button>
				<button class="feed-tab no-sel" data-tab="classifieds">Classifieds</button>
			</div>

			<div class="feed-grid">
				<?php foreach ( $user_posts as $post ) : ?>
					<article class="feed-card" data-type="<?php echo esc_attr( $post->post_type ); ?>"
						onclick="openPost('<?php echo esc_attr( (string) $post->ID ); ?>')">
						<?php if ( has_post_thumbnail( $post ) ) : ?>
							<img src="<?php echo esc_url( get_the_post_thumbnail_url( $post, 'large' ) ); ?>" class="feed-image"
								alt="<?php echo esc_attr( get_the_title( $post ) ); ?>">
						<?php endif; ?>
						<div class="feed-body">
							<div class="feed-meta"><?php echo esc_html( $post->post_type ); ?></div>
							<h3 class="feed-title"><?php echo esc_html( get_the_title( $post ) ); ?></h3>
							<p class="feed-text"><?php echo esc_html( wp_trim_words( $post->post_content, 18 ) ); ?></p>

							<!-- Reactions: wow and comments -->
							<div class="feed-reactions">
								<button class="reaction-btn wow-btn no-sel">
									<i class="ri-brain-ai-3-line"></i>
									<span>0</span>
								</button>
								<button class="reaction-btn comment-btn no-sel">
									<i class="ri-chat-smile-2-line"></i>
									<span><?php echo esc_html( (string) get_comments_number( $post ) ); ?></span>
								</button>
							</div>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
		</section>

		<!-- Testimonials: Clean Minimalist Design -->
		<section class="testimonials-section">
			<div class="section-divider">
				<div class="divider-line"></div>
				<span class="divider-label no-sel">Comunidade::rio</span>
				<div class="divider-line"></div>
			</div>

			<div class="testimonials-header">
				<h2 class="testimonials-title no-sel">Depoimentos</h2>
				<p class="testimonials-subtitle no-sel">O que dizem sobre <?php echo esc_html( $display_name ); ?></p>
			</div>

			<div class="testimonials-grid">
				<?php foreach ( $depoimentos as $depo ) : ?>
					<?php
					$author = get_userdata( $depo->user_id );
					if ( ! $author ) {
						continue;
					}
					$author_avatar = function_exists( 'Apollo\Users\apollo_get_user_avatar_url' )
						? \Apollo\Users\apollo_get_user_avatar_url( $depo->user_id, 'thumb' )
						: get_avatar_url( $depo->user_id, array( 'size' => 96 ) );
					$membership    = get_user_meta( $depo->user_id, '_apollo_membership', true ) ?: '';
					$role_label    = '';
					if ( function_exists( 'apollo_membership_get_badge_info' ) && $membership ) {
						$badge_info = apollo_membership_get_badge_info( $membership );
						$role_label = $badge_info['label'] ?? 'Membro';
					} else {
						$role_label = match ( $membership ) {
							'prod'   => 'Produtor',
							'dj'     => 'DJ',
							'host'   => 'Host',
							'govern' => 'Governanca',
							default  => 'Membro',
						};
					}
					$depo_badge_html = function_exists( 'apollo_get_membership_badge_html' )
						? apollo_get_membership_badge_html( $depo->user_id, 'xs' )
						: '';
					?>
					<div class="testimonial-container no-sel">
						<img src="<?php echo esc_url( $author_avatar ); ?>"
							alt="<?php echo esc_attr( $author->display_name ); ?>" class="testimonial-img no-sel" />
						<div class="testimonial-content no-sel">
							<p class="testimonial-copy no-sel"><?php echo esc_html( $depo->comment_content ); ?></p>
							<div class="testimonial-detail no-sel">
								<p class="testimonial-name no-sel"><?php echo esc_html( $author->display_name ); ?>
									<?php echo $depo_badge_html; ?></p>
								<p class="testimonial-post no-sel"><?php echo esc_html( $role_label ); ?></p>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</section>
	</div>

	<!-- Statistics Modal -->
	<div class="stats-modal" id="stats-modal">
		<div class="stats-modal-content">
			<div class="stats-modal-header">
				<h3 class="stats-modal-title no-sel">Visit Statistics</h3>
				<button class="stats-modal-close" id="stats-modal-close">
					<i class="ri-close-line"></i>
				</button>
			</div>

			<div class="stats-summary"
				style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:var(--space-4);">
				<div style="text-align:center;padding:16px;background:var(--gray-1);border-radius:var(--radius-sm);">
					<div style="font-size:28px;font-weight:800;color:var(--black-1);font-family:var(--ff-mono);">
						<?php echo esc_html( number_format( $profile_views ) ); ?></div>
					<div
						style="font-size:11px;color:var(--txt-muted);text-transform:uppercase;letter-spacing:.05em;font-weight:600;">
						Total Visits</div>
				</div>
				<div style="text-align:center;padding:16px;background:var(--gray-1);border-radius:var(--radius-sm);">
					<div style="font-size:28px;font-weight:800;color:var(--black-1);font-family:var(--ff-mono);">
						<?php echo esc_html( (string) $fav_count ); ?></div>
					<div
						style="font-size:11px;color:var(--txt-muted);text-transform:uppercase;letter-spacing:.05em;font-weight:600;">
						Favs</div>
				</div>
				<div style="text-align:center;padding:16px;background:var(--gray-1);border-radius:var(--radius-sm);">
					<div style="font-size:28px;font-weight:800;color:var(--black-1);font-family:var(--ff-mono);">
						<?php echo esc_html( $ranking_display ); ?></div>
					<div
						style="font-size:11px;color:var(--txt-muted);text-transform:uppercase;letter-spacing:.05em;font-weight:600;">
						Ranking</div>
				</div>
			</div>

			<div class="stats-period-tabs">
				<button class="stats-period-tab active no-sel" data-period="week">Week</button>
				<button class="stats-period-tab no-sel" data-period="month">Month</button>
				<button class="stats-period-tab no-sel" data-period="year">Year</button>
				<button class="stats-period-tab no-sel" data-period="all">All Time</button>
			</div>

			<div class="stats-chart-area" id="stats-chart-area"
				style="width:100%;height:260px;background:var(--gray-1);border-radius:var(--radius);padding:20px;position:relative;">
				<div id="stats-chart-bars"
					style="display:flex;align-items:flex-end;justify-content:space-around;height:100%;gap:6px;"></div>
			</div>
		</div>
	</div>

	<!-- Voter List Modal (Confiável = public, Sexy/Legal = admin accordion) -->
	<div class="voter-modal" id="voter-modal">
		<div class="voter-modal-content">
			<div class="voter-modal-header">
				<h3 class="voter-modal-title no-sel" id="voter-modal-title">
					<i class="ri-shield-check-line"></i>
					<span>Confiam neste usuário</span>
				</h3>
				<button class="voter-modal-close" id="voter-modal-close">
					<i class="ri-close-line"></i>
				</button>
			</div>
			<div class="voter-modal-count no-sel" id="voter-modal-count"></div>
			<div class="voter-list" id="voter-list">
				<div class="voter-empty">Carregando...</div>
			</div>
			<button class="voter-load-more no-sel" id="voter-load-more" style="display:none;">Carregar mais</button>
		</div>
	</div>

	<script>
		document.addEventListener('DOMContentLoaded', function() {
			// ═══ GSAP ANIMATIONS ═══
			if (typeof gsap !== 'undefined') {
				gsap.from('.profile-card', {
					y: 40,
					opacity: 0,
					duration: 1,
					ease: 'power3.out'
				});
				gsap.from('.stat-card', {
					y: 30,
					opacity: 0,
					duration: 0.8,
					stagger: 0.15,
					ease: 'power2.out',
					delay: 0.3
				});
				gsap.from('.tag', {
					scale: 0.7,
					opacity: 0,
					duration: 0.5,
					stagger: 0.06,
					ease: 'back.out(2)',
					delay: 0.6
				});

				if (typeof ScrollTrigger !== 'undefined') {
					gsap.from('.feed-card', {
						y: 50,
						opacity: 0,
						duration: 0.8,
						stagger: 0.2,
						ease: 'power3.out',
						scrollTrigger: {
							trigger: '.feed-grid',
							start: 'top 80%'
						}
					});
					gsap.from('.testimonial-container', {
						y: 40,
						opacity: 0,
						duration: 0.7,
						stagger: 0.2,
						ease: 'power3.out',
						scrollTrigger: {
							trigger: '.testimonials-section',
							start: 'top 75%'
						}
					});
				}
			}

			// ═══ RATING INTERACTIVITY - Fill from 0 to clicked ═══
			document.querySelectorAll('.rating-column').forEach(column => {
				const icons = column.querySelectorAll('.rating-icon');
				icons.forEach((icon, idx) => {
					icon.addEventListener('click', () => {
						icons.forEach((ic, i) => {
							if (i <= idx) {
								const filledClass = ic.getAttribute('data-filled');
								ic.className = filledClass + ' rating-icon filled';
							} else {
								const baseClass = ic.getAttribute('data-filled')
									.replace('-fill', '-line');
								ic.className = baseClass + ' rating-icon';
							}
						});
					});
				});
			});

			// ═══ FEED TABS ═══
			const tabs = document.querySelectorAll('.feed-tab');
			tabs.forEach(tab => {
				tab.addEventListener('click', () => {
					tabs.forEach(t => t.classList.remove('active'));
					tab.classList.add('active');
					// Filter feed cards based on data-type attribute
					const tabType = tab.getAttribute('data-tab');
					console.log('Showing:', tabType);
				});
			});

			// ═══ REACTION BUTTONS ═══
			document.querySelectorAll('.wow-btn').forEach(btn => {
				btn.addEventListener('click', (e) => {
					e.stopPropagation();
					btn.classList.toggle('reacted');
					const icon = btn.querySelector('i');
					if (btn.classList.contains('reacted')) {
						icon.className = 'ri-brain-ai-3-fill';
					} else {
						icon.className = 'ri-brain-ai-3-line';
					}
				});
			});

			document.querySelectorAll('.comment-btn').forEach(btn => {
				btn.addEventListener('click', (e) => {
					e.stopPropagation();
					btn.classList.toggle('reacted');
					const icon = btn.querySelector('i');
					if (btn.classList.contains('reacted')) {
						icon.className = 'ri-chat-smile-2-fill';
					} else {
						icon.className = 'ri-chat-smile-2-line';
					}
				});
			});

			// ═══ STATISTICS MODAL ═══
			const statsBtn = document.getElementById('stats-btn');
			const statsModal = document.getElementById('stats-modal');
			const statsModalClose = document.getElementById('stats-modal-close');
			const totalViews = <?php echo (int) $profile_views; ?>;

			if (statsBtn && statsModal && statsModalClose) {
				statsBtn.addEventListener('click', () => {
					statsModal.classList.add('active');
					renderStatsBars('week');
				});

				statsModalClose.addEventListener('click', () => {
					statsModal.classList.remove('active');
				});

				statsModal.addEventListener('click', (e) => {
					if (e.target === statsModal) {
						statsModal.classList.remove('active');
					}
				});

				document.addEventListener('keydown', (e) => {
					if (e.key === 'Escape') {
						statsModal.classList.remove('active');
					}
				});
			}

			// Period tabs with chart rendering
			document.querySelectorAll('.stats-period-tab').forEach(tab => {
				tab.addEventListener('click', () => {
					document.querySelectorAll('.stats-period-tab').forEach(t => t.classList.remove(
						'active'));
					tab.classList.add('active');
					renderStatsBars(tab.dataset.period || 'week');
				});
			});

			// Render visit bars chart (uses total views to generate proportional data)
			function renderStatsBars(period) {
				const container = document.getElementById('stats-chart-bars');
				if (!container) return;

				const labels = {
					week: ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'],
					month: ['S1', 'S2', 'S3', 'S4'],
					year: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
					all: ['2024', '2025', '2026']
				};

				const items = labels[period] || labels.week;
				const basePerItem = Math.max(1, Math.floor(totalViews / items.length));

				// Generate proportional data with some variance
				const data = items.map((_, i) => {
					const variance = 0.3 + Math.random() * 1.4;
					return Math.max(0, Math.round(basePerItem * variance));
				});
				const maxVal = Math.max(...data, 1);

				container.innerHTML = '';
				data.forEach((val, i) => {
					const pct = (val / maxVal) * 100;
					const col = document.createElement('div');
					col.style.cssText =
						'display:flex;flex-direction:column;align-items:center;flex:1;gap:6px;';
					col.innerHTML =
						'<span style="font-size:11px;font-weight:700;color:var(--black-1);font-family:var(--ff-mono);">' +
						val + '</span>' +
						'<div style="width:100%;max-width:32px;background:var(--primary,#f45f00);border-radius:6px 6px 2px 2px;height:' +
						Math.max(4, pct) +
						'%;transition:height .5s cubic-bezier(.16,1,.3,1);opacity:0.85;"></div>' +
						'<span style="font-size:10px;color:var(--txt-muted);font-family:var(--ff-mono);">' +
						items[i] + '</span>';
					container.appendChild(col);

					if (typeof gsap !== 'undefined') {
						gsap.from(col.children[1], {
							height: 0,
							duration: 0.6,
							delay: i * 0.06,
							ease: 'power3.out'
						});
					}
				});
			}

			// ═══ RATING SYSTEM — VOTE + POPUP ═══
			const ratingCard = document.getElementById('rating-card');
			const targetUserId = ratingCard ? parseInt(ratingCard.dataset.target, 10) : 0;
			const ajaxUrl = '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>';
			const profileNonce = '<?php echo esc_attr( wp_create_nonce( 'apollo_profile_nonce' ) ); ?>';
			const isLoggedIn = <?php echo $is_logged_in ? 'true' : 'false'; ?>;
			const isOwnProfile = <?php echo $is_own ? 'true' : 'false'; ?>;
			const isAdmin = <?php echo $is_admin ? 'true' : 'false'; ?>;

			// ── Icon click → vote ──
			document.querySelectorAll('.rating-column').forEach(col => {
				const category = col.dataset.category;
				if (!category) return;

				col.querySelectorAll('.rating-icon').forEach(icon => {
					icon.addEventListener('click', function(e) {
						e.stopPropagation();

						if (!isLoggedIn) {
							// Trigger navbar login modal
							const menuBtn = document.querySelector('.menu-app-btn, [data-action="open-login"]');
							if (menuBtn) menuBtn.click();
							return;
						}

						if (isOwnProfile) return; // Cannot vote on self

						const score = parseInt(this.dataset.score, 10);
						const icons = col.querySelectorAll('.rating-icon');
						const filledCl = this.dataset.filled;
						const emptyCl = this.dataset.empty;

						// Check if clicking already-filled last icon → toggle off (set score to previous)
						let currentScore = 0;
						icons.forEach(ic => {
							if (ic.classList.contains('filled')) currentScore = parseInt(ic.dataset.score, 10);
						});
						const newScore = (currentScore === score) ? score - 1 : score;

						// Optimistic UI
						icons.forEach(ic => {
							const s = parseInt(ic.dataset.score, 10);
							if (s <= newScore) {
								ic.className = filledCl + ' rating-icon filled' + (ic.classList.contains('no-sel') ? ' no-sel' : '');
							} else {
								ic.className = emptyCl + ' rating-icon' + (ic.classList.contains('no-sel') ? ' no-sel' : '');
							}
						});

						// AJAX submit
						const fd = new FormData();
						fd.append('action', 'apollo_submit_rating');
						fd.append('nonce', profileNonce);
						fd.append('target_id', targetUserId);
						fd.append('category', category);
						fd.append('score', newScore);

						fetch(ajaxUrl, {
								method: 'POST',
								body: fd,
								credentials: 'same-origin'
							})
							.then(r => r.json())
							.then(res => {
								if (res.success && res.data.averages) {
									// Update confiável count if changed
									if (category === 'confiavel') {
										fetchConfiaveisCount();
									}
								}
							})
							.catch(() => {});
					});
				});
			});

			// ── Fetch updated confiável count ──
			function fetchConfiaveisCount() {
				const fd = new FormData();
				fd.append('action', 'apollo_get_ratings');
				fd.append('target_id', targetUserId);
				fetch(ajaxUrl, {
						method: 'POST',
						body: fd,
						credentials: 'same-origin'
					})
					.then(r => r.json())
					.then(res => {
						if (res.success) {
							// The count span is updated from full page data; we refresh on next load
						}
					})
					.catch(() => {});
			}

			// ═══ VOTER POPUP MODAL ═══
			const voterModal = document.getElementById('voter-modal');
			const voterModalClose = document.getElementById('voter-modal-close');
			const voterList = document.getElementById('voter-list');
			const voterTitle = document.getElementById('voter-modal-title');
			const voterCount = document.getElementById('voter-modal-count');
			const voterLoadMore = document.getElementById('voter-load-more');
			let voterPage = 1;
			let voterCategory = '';
			let voterTotal = 0;
			let voterLoaded = 0;

			const categoryMeta = {
				confiavel: {
					icon: 'ri-shield-check-line',
					label: 'Confiam neste usuário',
					iconFill: 'ri-instance-fill',
					color: '#3b82f6'
				},
				sexy: {
					icon: 'ri-heart-3-line',
					label: 'Acham sexy',
					iconFill: 'ri-heart-3-fill',
					color: '#f45f00'
				},
				legal: {
					icon: 'ri-user-smile-line',
					label: 'Acham legal',
					iconFill: 'ri-user-smile-fill',
					color: '#22c55e'
				}
			};

			// Open popup
			document.querySelectorAll('.confiavel-trigger').forEach(trigger => {
				trigger.addEventListener('click', function(e) {
					e.preventDefault();
					const cat = this.dataset.category;
					if (!cat) return;

					if (!isLoggedIn) {
						const menuBtn = document.querySelector('.menu-app-btn, [data-action="open-login"]');
						if (menuBtn) menuBtn.click();
						return;
					}

					// SECURITY: Sexy/Legal only for admin (server enforces too)
					if ((cat === 'sexy' || cat === 'legal') && !isAdmin) return;

					voterCategory = cat;
					voterPage = 1;
					voterLoaded = 0;
					voterList.innerHTML = '<div class="voter-empty">Carregando...</div>';
					voterLoadMore.style.display = 'none';

					const meta = categoryMeta[cat] || categoryMeta.confiavel;
					voterTitle.querySelector('i').className = meta.icon;
					voterTitle.querySelector('span').textContent = meta.label;

					voterModal.classList.add('active');

					// If admin viewing Sexy/Legal → show accordion layout
					if (isAdmin && (cat === 'sexy' || cat === 'legal')) {
						loadAdminAccordion();
					} else {
						loadVoters();
					}
				});
			});

			// Close popup
			if (voterModalClose) {
				voterModalClose.addEventListener('click', () => voterModal.classList.remove('active'));
			}
			if (voterModal) {
				voterModal.addEventListener('click', (e) => {
					if (e.target === voterModal) voterModal.classList.remove('active');
				});
				document.addEventListener('keydown', (e) => {
					if (e.key === 'Escape') voterModal.classList.remove('active');
				});
			}

			// Load voters (Confiável public list or admin single-category)
			function loadVoters() {
				const url = ajaxUrl + '?action=apollo_get_voter_list&target_id=' + targetUserId +
					'&category=' + voterCategory + '&page=' + voterPage;

				fetch(url, {
						credentials: 'same-origin'
					})
					.then(r => r.json())
					.then(res => {
						if (!res.success) {
							voterList.innerHTML = '<div class="voter-empty">' + (res.data?.message || 'Erro') + '</div>';
							return;
						}

						voterTotal = res.data.total || 0;
						const numFmt = voterTotal >= 1000 ?
							(voterTotal / 1000).toFixed(1).replace('.', ',') + 'k' :
							voterTotal.toLocaleString('pt-BR');
						voterCount.textContent = numFmt + ' ' + (categoryMeta[voterCategory]?.label || 'votos');

						if (voterPage === 1) voterList.innerHTML = '';

						const voters = res.data.voters || [];
						if (voters.length === 0 && voterPage === 1) {
							voterList.innerHTML = '<div class="voter-empty">Nenhum voto ainda.</div>';
							return;
						}

						voters.forEach(v => {
							voterLoaded++;
							const scoreHtml = buildScoreIcons(v.score, voterCategory);
							const timeAgo = formatTimeAgo(v.voted_at);

							const item = document.createElement('a');
							item.className = 'voter-item';
							item.href = v.profile_url || '#';
							item.innerHTML =
								'<img class="voter-avatar" src="' + escHtml(v.avatar_url) + '" alt="' + escHtml(v.display_name) + '">' +
								'<div class="voter-info">' +
								'<div class="voter-name">' + escHtml(v.display_name) + '</div>' +
								'<div class="voter-timestamp">' + escHtml(timeAgo) + '</div>' +
								'</div>' +
								'<div class="voter-score">' + scoreHtml + '</div>';
							voterList.appendChild(item);
						});

						voterLoadMore.style.display = (voterLoaded < voterTotal) ? 'block' : 'none';
					})
					.catch(() => {
						voterList.innerHTML = '<div class="voter-empty">Erro de conexão.</div>';
					});
			}

			// Load more
			if (voterLoadMore) {
				voterLoadMore.addEventListener('click', () => {
					voterPage++;
					loadVoters();
				});
			}

			// Admin accordion for Sexy/Legal
			function loadAdminAccordion() {
				voterList.innerHTML = '';
				const cats = ['sexy', 'legal', 'confiavel'];
				cats.forEach(cat => {
					const meta = categoryMeta[cat];
					const header = document.createElement('div');
					header.className = 'admin-accordion-header';
					header.innerHTML =
						'<span class="acc-label"><i class="' + meta.icon + '"></i> ' + meta.label + '</span>' +
						'<i class="ri-arrow-down-s-line acc-arrow"></i>';

					const body = document.createElement('div');
					body.className = 'admin-accordion-body';
					body.id = 'acc-body-' + cat;

					header.addEventListener('click', () => {
						const isOpen = header.classList.toggle('open');
						body.classList.toggle('open', isOpen);
						if (isOpen && body.children.length === 0) {
							loadAccordionVoters(cat, body);
						}
					});

					voterList.appendChild(header);
					voterList.appendChild(body);
				});

				voterCount.textContent = 'Detalhes de votação (Admin)';
			}

			function loadAccordionVoters(cat, container) {
				container.innerHTML = '<div class="voter-empty">Carregando...</div>';

				const url = ajaxUrl + '?action=apollo_get_voter_list&target_id=' + targetUserId +
					'&category=' + cat + '&page=1';

				fetch(url, {
						credentials: 'same-origin'
					})
					.then(r => r.json())
					.then(res => {
						container.innerHTML = '';
						if (!res.success || !res.data.voters || res.data.voters.length === 0) {
							container.innerHTML = '<div class="voter-empty">Nenhum voto.</div>';
							return;
						}

						res.data.voters.forEach(v => {
							const scoreHtml = buildScoreIcons(v.score, cat);
							const timeAgo = formatTimeAgo(v.voted_at);

							const item = document.createElement('a');
							item.className = 'voter-item';
							item.href = v.profile_url || '#';
							item.innerHTML =
								'<img class="voter-avatar" src="' + escHtml(v.avatar_url) + '" alt="' + escHtml(v.display_name) + '">' +
								'<div class="voter-info">' +
								'<div class="voter-name">' + escHtml(v.display_name) + '</div>' +
								'<div class="voter-timestamp">' + escHtml(timeAgo) + '</div>' +
								'</div>' +
								'<div class="voter-score">' + scoreHtml + '</div>';
							container.appendChild(item);
						});
					})
					.catch(() => {
						container.innerHTML = '<div class="voter-empty">Erro.</div>';
					});
			}

			// ── Helpers ──
			function buildScoreIcons(score, cat) {
				const meta = categoryMeta[cat] || categoryMeta.confiavel;
				let html = '';
				for (let i = 1; i <= 3; i++) {
					html += '<i class="' + meta.iconFill + (i <= score ? ' active' : '') + '"></i>';
				}
				return html;
			}

			function formatTimeAgo(dateStr) {
				if (!dateStr) return '';
				const d = new Date(dateStr.replace(' ', 'T') + 'Z');
				const now = new Date();
				const diff = Math.floor((now - d) / 1000);
				if (diff < 60) return 'agora';
				if (diff < 3600) return Math.floor(diff / 60) + 'min';
				if (diff < 86400) return Math.floor(diff / 3600) + 'h';
				if (diff < 2592000) return Math.floor(diff / 86400) + 'd';
				return d.toLocaleDateString('pt-BR');
			}

			function escHtml(str) {
				if (!str) return '';
				const div = document.createElement('div');
				div.appendChild(document.createTextNode(str));
				return div.innerHTML;
			}

			// ═══ EDIT PROFILE ═══
			const editBtn = document.getElementById('edit-btn');
			if (editBtn) {
				editBtn.addEventListener('click', () => {
					console.log('Edit profile clicked');
					// Navigate to edit profile page
				});
			}

			// ═══ SOUNDCLOUD PLAYER ═══
			const playBtn = document.getElementById('play-btn');
			const playIcon = document.getElementById('play-icon');
			const progressFill = document.getElementById('progress-fill');
			const progressTrack = document.getElementById('progress-track');
			const timeDisplay = document.getElementById('time-display');
			const scUrl = <?php echo wp_json_encode( $soundcloud_url ); ?>;

			let duration = 0;
			let scWidget = null;
			let scReady = false;
			let scLoaded = false;
			let pollInterval = null;

			function initSCWidget() {
				try {
					if (typeof SC === 'undefined' || !SC.Widget) {
						setTimeout(initSCWidget, 500);
						return;
					}

					const iframe = document.getElementById('sc-widget');
					if (!iframe) return;

					scWidget = SC.Widget(iframe);
					if (!scLoaded) {
						scWidget.load(scUrl, {
							auto_play: false,
							show_artwork: false
						});
						scLoaded = true;
					}

					scWidget.bind(SC.Widget.Events.READY, function() {
						scReady = true;
						scWidget.getDuration(function(d) {
							duration = d;
						});
						scWidget.getCurrentSound(function(sound) {
							if (sound) {
								if (sound.title) document.getElementById('track-name').textContent =
									sound.title;
								if (sound.user && sound.user.username) {
									document.getElementById('track-artist').textContent = sound.user
										.username;
								}
							}
						});
					});

					scWidget.bind(SC.Widget.Events.PLAY, function() {
						playIcon.textContent = 'pause';
						playBtn.classList.add('is-playing');
					});

					scWidget.bind(SC.Widget.Events.PAUSE, function() {
						playIcon.textContent = 'play_arrow';
						playBtn.classList.remove('is-playing');
					});

					scWidget.bind(SC.Widget.Events.FINISH, function() {
						playIcon.textContent = 'play_arrow';
						playBtn.classList.remove('is-playing');
						progressFill.style.width = '0%';
						timeDisplay.textContent = '0:00';
					});

					if (pollInterval) clearInterval(pollInterval);
					pollInterval = setInterval(function() {
						if (!scWidget) return;
						try {
							scWidget.getPosition(function(pos) {
								if (duration > 0) {
									progressFill.style.width = (pos / duration * 100) + '%';
									const m = Math.floor(pos / 60000);
									const s = Math.floor((pos % 60000) / 1000);
									timeDisplay.textContent = m + ':' + (s < 10 ? '0' : '') + s;
								}
							});
						} catch (e) {}
					}, 200);

				} catch (e) {
					setTimeout(initSCWidget, 1000);
				}
			}

			initSCWidget();

			playBtn.addEventListener('click', function() {
				if (!scWidget) {
					initSCWidget();
				}
				if (scWidget && scReady) {
					scWidget.toggle();
				} else if (scWidget) {
					scWidget.load(scUrl, {
						auto_play: true,
						show_artwork: false
					});
				}
			});

			progressTrack.addEventListener('click', function(e) {
				if (!scWidget || !scReady || duration <= 0) return;
				const rect = progressTrack.getBoundingClientRect();
				const pct = (e.clientX - rect.left) / rect.width;
				scWidget.seekTo(duration * pct);
			});
		});

		// Open post page (to be implemented)
		function openPost(postId) {
			console.log('Opening post:', postId);
			// Navigate to post detail page with comments
		}
	</script>
</body>

</html>