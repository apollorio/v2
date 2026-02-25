<?php
/**
 * Single Profile Template - /id/{username}
 *
 * Based on the Apollo Profile HTML mockup.
 * 2/3 + 1/3 grid layout with hero cover.
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
		echo '<h1>404 - Usuário não encontrado</h1>';
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
$social_name  = get_user_meta( $user_id, '_apollo_social_name', true );
$display_name = $social_name ?: $user->display_name;
$bio          = get_user_meta( $user_id, '_apollo_bio', true ) ?: $user->description;
$location     = get_user_meta( $user_id, 'user_location', true ) ?: 'Rio de Janeiro, RJ';
$instagram    = $user->user_login; // Always = username
$website      = get_user_meta( $user_id, '_apollo_website', true );
$avatar_url   = \Apollo\Users\apollo_get_user_avatar_url( $user_id, 'large' );
$cover_url    = \Apollo\Users\apollo_get_user_cover_url( $user_id );
$membership   = \Apollo\Users\apollo_get_user_membership( $user_id );
$member_since = date_i18n( 'Y', strtotime( $user->user_registered ) );

// Sound preferences (from apollo-core taxonomy)
$sound_prefs = get_user_meta( $user_id, '_apollo_sound_preferences', true );
$sound_tags  = [];
if ( is_array( $sound_prefs ) && taxonomy_exists( 'sound' ) ) {
	foreach ( $sound_prefs as $term_id ) {
		$term = get_term( $term_id, 'sound' );
		if ( $term && ! is_wp_error( $term ) ) {
			$sound_tags[] = $term->name;
		}
	}
}

// Ratings
$rating_averages = \Apollo\Users\Components\RatingHandler::get_user_rating_averages( $user_id );
$total_votes     = \Apollo\Users\Components\RatingHandler::get_total_votes( $user_id );
$my_votes        = [];
if ( is_user_logged_in() && ! $is_own ) {
	foreach ( array_keys( APOLLO_USERS_RATING_CATEGORIES ) as $cat ) {
		$vote = \Apollo\Users\Components\RatingHandler::get_user_vote( get_current_user_id(), $user_id, $cat );
		$my_votes[ $cat ] = $vote ? (int) $vote->score : 0;
	}
}

// Stats (own user only)
$profile_views = (int) get_user_meta( $user_id, '_apollo_profile_views', true );
$total_posts   = count_user_posts( $user_id );
$fav_count     = (int) get_user_meta( $user_id, '_apollo_fav_count', true );

// Depoimentos
$depoimentos = \Apollo\Users\Components\DepoimentoHandler::get_depoimentos( $user_id, 1, 10 );
$depo_count  = \Apollo\Users\Components\DepoimentoHandler::count_depoimentos( $user_id );

// User posts for feed
$user_posts = get_posts( [
	'author'         => $user_id,
	'posts_per_page' => 6,
	'post_status'    => 'publish',
	'post_type'      => [ 'post', 'apollo_event', 'apollo_classified' ],
] );

// Hide email
$hide_email = get_user_meta( $user_id, '_apollo_privacy_email', true );
$show_email = ! $hide_email || $is_own;

// Nonce for AJAX
$nonce = wp_create_nonce( 'apollo_profile_nonce' );
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php echo esc_html( $display_name ); ?> | Apollo Profile</title>

	<script src="https://cdn.apollo.rio.br/v1.0.0/core.js" fetchpriority="high"></script>
	<link href="https://fonts.googleapis.com/css2?family=Shrikhand&family=Space+Grotesk:wght@300..700&family=Space+Mono:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
	<link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">

	<?php
	wp_enqueue_style( 'apollo-users-profile' );
	wp_enqueue_script( 'apollo-users-profile' );
	wp_localize_script( 'apollo-users-profile', 'apolloProfile', [
		'ajaxUrl'       => admin_url( 'admin-ajax.php' ),
		'restUrl'       => rest_url( 'apollo/v1/' ),
		'nonce'         => $nonce,
		'restNonce'     => wp_create_nonce( 'wp_rest' ),
		'userId'        => $user_id,
		'isOwn'         => $is_own,
		'isLoggedIn'    => is_user_logged_in(),
		'currentUserId' => get_current_user_id(),
		'myVotes'       => $my_votes,
		'categories'    => APOLLO_USERS_RATING_CATEGORIES,
	] );
	wp_head();
	?>
</head>
<body class="apollo-profile-page">

<div class="app-container" data-user-id="<?php echo esc_attr( $user_id ); ?>">

	<!-- ═══════════ HERO SECTION ═══════════ -->
	<section class="hero-section">
		<div class="hero-media">
			<?php if ( $cover_url ) : ?>
				<img src="<?php echo esc_url( $cover_url ); ?>" alt="" class="hero-cover-img">
			<?php else : ?>
				<iframe src="https://assets.apollo.rio.br/bg/1.html" allowfullscreen></iframe>
			<?php endif; ?>
			<div class="hero-overlay"></div>
		</div>

		<!-- ═══════════ 2/3 + 1/3 GRID ═══════════ -->
		<div class="hero-content">

			<!-- LEFT: Profile Card -->
			<div class="profile-card">
				<div class="pc-header">
					<?php if ( $is_own ) : ?>
						<a href="<?php echo esc_url( home_url( '/editar-perfil/' ) ); ?>" class="pc-edit-btn" title="Editar Perfil">
							<i class="ri-pencil-line"></i>
						</a>
					<?php endif; ?>

					<img src="<?php echo esc_url( $avatar_url ); ?>" class="pc-avatar" alt="<?php echo esc_attr( $display_name ); ?>">

					<div class="pc-identity-wrap">
						<h1 class="pc-name"><?php echo esc_html( $display_name ); ?></h1>

						<div class="pc-tags">
							<span class="pc-tag pc-tag--membership" style="border-color:<?php echo esc_attr( $membership['color'] ); ?>; color:<?php echo esc_attr( $membership['color'] ); ?>">
								<i class="<?php echo esc_attr( $membership['icon'] ); ?>"></i>
								<?php echo esc_html( $membership['label'] ); ?>
							</span>
							<?php if ( get_user_meta( $user_id, '_apollo_user_verified', true ) ) : ?>
								<span class="pc-tag pc-tag--verified"><i class="ri-verified-badge-fill"></i> Verificado</span>
							<?php endif; ?>
						</div>

						<span class="pc-handle">@<?php echo esc_html( $instagram ); ?></span>
					</div>
				</div>

				<?php if ( $bio ) : ?>
					<p class="pc-bio"><?php echo wp_kses_post( nl2br( esc_html( $bio ) ) ); ?></p>
				<?php endif; ?>

				<?php if ( $location ) : ?>
					<span class="pc-location"><i class="ri-map-pin-2-line"></i> <?php echo esc_html( $location ); ?></span>
				<?php endif; ?>

				<a href="<?php echo esc_url( home_url( '/id/' . $user->user_login ) ); ?>" class="pc-link">
					<i class="ri-link-m"></i> apollo.rio.br/id/<?php echo esc_html( $user->user_login ); ?>
				</a>

				<?php if ( ! empty( $sound_tags ) ) : ?>
					<div class="sound-tags">
						<?php foreach ( $sound_tags as $tag ) : ?>
							<span class="pc-tag"><?php echo esc_html( $tag ); ?></span>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>

				<?php if ( $website ) : ?>
					<a href="<?php echo esc_url( $website ); ?>" target="_blank" class="pc-link pc-link--website">
						<i class="ri-global-line"></i> <?php echo esc_html( wp_parse_url( $website, PHP_URL_HOST ) ); ?>
					</a>
				<?php endif; ?>

				<div class="member-since">[ MEMBRO DESDE <?php echo esc_html( $member_since ); ?> ]</div>

				<!-- Social Actions -->
				<?php if ( ! $is_own && is_user_logged_in() ) : ?>
					<div class="pc-actions">
						<a href="https://instagram.com/<?php echo esc_attr( $instagram ); ?>" target="_blank" class="pc-action-btn">
							<i class="ri-instagram-line"></i>
						</a>
						<?php if ( $show_email ) : ?>
							<a href="mailto:<?php echo esc_attr( $user->user_email ); ?>" class="pc-action-btn">
								<i class="ri-mail-line"></i>
							</a>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<?php if ( $is_own ) : ?>
					<div class="pc-own-links">
						<a href="<?php echo esc_url( home_url( '/editar-perfil/' ) ); ?>" class="pc-btn-edit">
							<i class="ri-pencil-line"></i> Editar Perfil
						</a>
						<a href="<?php echo esc_url( home_url( '/minha-conta/' ) ); ?>" class="pc-btn-account">
							<i class="ri-settings-3-line"></i> Minha Conta
						</a>
					</div>
				<?php endif; ?>
			</div>

			<!-- RIGHT: Summary Sidebar -->
			<div class="summary-sidebar">

				<!-- RATINGS CARD -->
				<div class="summary-card" id="ratings-card">
					<div class="sc-header"><i class="ri-heart-fill"></i> Avaliações</div>
					<?php if ( $total_votes > 0 ) : ?>
						<div class="rating-note"><?php echo esc_html( $total_votes ); ?> voto<?php echo $total_votes > 1 ? 's' : ''; ?></div>
					<?php endif; ?>

					<div class="ratings-list">
						<?php foreach ( APOLLO_USERS_RATING_CATEGORIES as $cat_key => $cat_info ) :
							$avg   = $rating_averages[ $cat_key ]['avg'] ?? 0;
							$count = $rating_averages[ $cat_key ]['count'] ?? 0;
							$my_score = $my_votes[ $cat_key ] ?? 0;
						?>
							<div class="rating-row" data-category="<?php echo esc_attr( $cat_key ); ?>">
								<div class="stat-label"><?php echo esc_html( $cat_info['label'] ); ?></div>
								<div class="rating-emojis" data-avg="<?php echo esc_attr( $avg ); ?>">
									<?php for ( $i = 1; $i <= 3; $i++ ) :
										$filled = ( ! $is_own && is_user_logged_in() ) ? ( $my_score >= $i ) : ( $avg >= $i );
										$half   = ( ! $filled && $avg > ( $i - 1 ) && $avg < $i );
										$class  = $filled ? 'filled' : ( $half ? 'half-filled' : '' );
										$interactive = ( ! $is_own && is_user_logged_in() ) ? 'data-score="' . $i . '"' : '';
									?>
										<i class="<?php echo esc_attr( $cat_info['icon'] ); ?> rating-emoji <?php echo $class; ?>"
										   style="<?php echo $filled ? 'color:' . esc_attr( $cat_info['color'] ) : ''; ?>"
										   <?php echo $interactive; ?>></i>
									<?php endfor; ?>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				</div>

				<!-- STATS CARD (OWN USER ONLY) -->
				<?php if ( $is_own ) : ?>
					<div class="summary-card summary-card--private">
						<div class="sc-header"><i class="ri-bar-chart-fill"></i> Performance <span class="sc-private-badge">Privado</span></div>
						<div class="stats-grid">
							<div class="stat-item">
								<span class="stat-label">Visitas</span>
								<span class="stat-val"><?php echo esc_html( number_format_i18n( $profile_views ) ); ?></span>
							</div>
							<div class="stat-item">
								<span class="stat-label">Posts</span>
								<span class="stat-val"><?php echo esc_html( $total_posts ); ?></span>
							</div>
							<div class="stat-item">
								<span class="stat-label">Favs</span>
								<span class="stat-val"><?php echo esc_html( $fav_count ); ?></span>
							</div>
							<div class="stat-item">
								<span class="stat-label">Votos</span>
								<span class="stat-val"><?php echo esc_html( $total_votes ); ?></span>
							</div>
						</div>
					</div>
				<?php endif; ?>

				<!-- PUBLICATIONS CARD (Public) -->
				<div class="summary-card">
					<div class="sc-header"><i class="ri-article-line"></i> Publicações</div>
					<div class="pub-list">
						<?php if ( $user_posts ) :
							foreach ( array_slice( $user_posts, 0, 3 ) as $post ) :
						?>
							<a href="<?php echo get_permalink( $post ); ?>" class="pub-item">
								<span class="pub-title"><?php echo esc_html( $post->post_title ); ?></span>
								<div class="pub-meta"><?php echo human_time_diff( get_the_time( 'U', $post ), current_time( 'timestamp' ) ) . ' atrás'; ?></div>
							</a>
						<?php endforeach;
						else : ?>
							<p class="pub-empty">Nenhuma publicação ainda.</p>
						<?php endif; ?>
					</div>
				</div>

			</div><!-- /summary-sidebar -->
		</div><!-- /hero-content -->
	</section>

	<!-- ═══════════ DEPOIMENTOS SECTION ═══════════ -->
	<section class="depoimentos-section" id="depoimentos">
		<div class="depo-container">
			<div class="depo-header">
				<h2 class="depo-title"><i class="ri-double-quotes-l"></i> Depoimentos</h2>
				<span class="depo-count"><?php echo esc_html( $depo_count ); ?></span>
			</div>

			<!-- Depoimento Form (logged in, not own profile) -->
			<?php if ( is_user_logged_in() && ! $is_own ) :
				$already_left = \Apollo\Users\Components\DepoimentoHandler::get_user_depoimento( get_current_user_id(), $user_id );
				if ( ! $already_left ) :
			?>
				<form class="depo-form" id="depoimento-form">
					<div class="depo-form-inner">
						<img src="<?php echo esc_url( \Apollo\Users\apollo_get_user_avatar_url( get_current_user_id(), 'thumb' ) ); ?>" class="depo-form-avatar" alt="">
						<textarea name="content" placeholder="Escreva um depoimento sobre <?php echo esc_attr( $display_name ); ?>..." maxlength="1000" rows="3" required></textarea>
					</div>
					<button type="submit" class="depo-submit-btn"><i class="ri-send-plane-fill"></i> Publicar</button>
				</form>
			<?php endif; endif; ?>

			<!-- Depoimentos List -->
			<div class="depo-list" id="depoimentos-list">
				<?php if ( $depoimentos ) :
					foreach ( $depoimentos as $depo ) :
				?>
					<div class="depo-item" data-id="<?php echo esc_attr( $depo['id'] ); ?>">
						<?php if ( $depo['can_delete'] ) : ?>
							<button class="depo-delete-btn" data-id="<?php echo esc_attr( $depo['id'] ); ?>" title="Excluir">
								<i class="ri-close-line"></i>
							</button>
						<?php endif; ?>
						<i class="ri-double-quotes-l depo-quote-icon"></i>
						<blockquote class="depo-content"><?php echo wp_kses_post( $depo['content'] ); ?></blockquote>
						<div class="depo-author">
							<a href="<?php echo esc_url( home_url( '/id/' . $depo['author_login'] ) ); ?>">
								<img src="<?php echo esc_url( $depo['avatar_url'] ); ?>" class="depo-author-avatar" alt="">
							</a>
							<div>
								<a href="<?php echo esc_url( home_url( '/id/' . $depo['author_login'] ) ); ?>" class="depo-author-name">
									<?php echo esc_html( $depo['author_name'] ); ?>
								</a>
								<div class="depo-author-role"><?php echo esc_html( $depo['membership'] ); ?></div>
							</div>
							<span class="depo-date"><?php echo esc_html( $depo['date_human'] ); ?></span>
						</div>
					</div>
				<?php endforeach;
				else : ?>
					<p class="depo-empty">Nenhum depoimento ainda. Seja o primeiro!</p>
				<?php endif; ?>
			</div>
		</div>
	</section>

	<!-- ═══════════ FEED SECTION ═══════════ -->
	<?php if ( $user_posts ) : ?>
	<section class="feed-section">
		<div class="feed-container">
			<div class="feed-toolbar">
				<div class="feed-title">Publicações</div>
				<div class="feed-tabs">
					<button class="feed-tab active">Todos</button>
					<button class="feed-tab">Eventos</button>
					<button class="feed-tab">Classificados</button>
				</div>
			</div>
			<div class="complex-grid">
				<?php foreach ( $user_posts as $post ) : ?>
					<article class="f-card">
						<?php if ( has_post_thumbnail( $post ) ) : ?>
							<img src="<?php echo esc_url( get_the_post_thumbnail_url( $post, 'medium_large' ) ); ?>" class="f-card-img" alt="">
						<?php endif; ?>
						<div class="f-card-body">
							<h3 class="f-title">
								<a href="<?php echo get_permalink( $post ); ?>"><?php echo esc_html( $post->post_title ); ?></a>
							</h3>
							<p class="f-text"><?php echo esc_html( wp_trim_words( $post->post_content, 20 ) ); ?></p>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php endif; ?>

</div><!-- /app-container -->

<?php wp_footer(); ?>
</body>
</html>
