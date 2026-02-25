<?php
/**
 * Apollo Navbar Component
 *
 * Global fixed navigation bar displayed on all Apollo pages
 *
 * @package Apollo\Templates
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get current user
$current_user = wp_get_current_user();
$is_logged_in = is_user_logged_in();

// User data
$user_name        = $is_logged_in ? ( get_user_meta( $current_user->ID, '_apollo_social_name', true ) ?: $current_user->display_name ) : '';
$user_avatar      = $is_logged_in ? get_avatar_url( $current_user->ID, array( 'size' => 40 ) ) : '';
$user_profile_url = $is_logged_in ? home_url( '/id/' . $current_user->user_login ) : '';

// Unread notifications count (placeholder - will be dynamic later)
$unread_count = $is_logged_in ? 0 : 0;
?>

<nav class="apollo-navbar" id="apollo-navbar">
	<div class="navbar-container">
		<!-- Logo -->
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="navbar-logo" aria-label="Apollo Rio Home">
			<div class="logo-icon">
				<i class="ri-slack-fill"></i>
			</div>
			<div class="logo-text">
				<span class="logo-title">Apollo::rio</span>
				<span class="logo-subtitle">plataforma</span>
			</div>
		</a>

		<!-- Spacer -->
		<div class="navbar-spacer"></div>

		<!-- Controls -->
		<div class="navbar-controls">
			<!-- Digital Clock -->
			<div class="navbar-clock" id="navbar-clock">00:00:00</div>

			<?php if ( $is_logged_in ) : ?>
				<!-- Notifications Button (GPS/Radar Icon - Apollo's signature) -->
				<button
					class="navbar-btn"
					id="navbar-notif-btn"
					aria-label="Notificações"
					type="button"
				>
					<div class="navbar-badge" data-notif="<?php echo $unread_count > 0 ? 'true' : 'false'; ?>"></div>
					<i class="ri-gps-line"></i>
				</button>

				<!-- Apps Menu Button -->
				<button
					class="navbar-btn"
					id="navbar-apps-btn"
					aria-label="Aplicativos"
					aria-expanded="false"
				>
					<i class="ri-grid-fill"></i>
				</button>

				<!-- Profile Button -->
				<a
					href="<?php echo esc_url( $user_profile_url ); ?>"
					class="navbar-btn navbar-profile"
					aria-label="Meu Perfil"
				>
					<img src="<?php echo esc_url( $user_avatar ); ?>" alt="<?php echo esc_attr( $user_name ); ?>" class="profile-avatar">
				</a>
			<?php else : ?>
				<!-- Login Button (Guest) -->
				<a
					href="<?php echo esc_url( home_url( '/acesso/' ) ); ?>"
					class="navbar-btn navbar-login"
					aria-label="Fazer Login"
				>
					<i class="ri-login-box-line"></i>
					<span class="btn-label">Entrar</span>
				</a>
			<?php endif; ?>
		</div>
	</div>
</nav>

<!-- Apps Modal (Logged-in only) -->
<?php if ( $is_logged_in ) : ?>
<div class="navbar-apps-modal" id="navbar-apps-modal" role="dialog" aria-hidden="true" aria-labelledby="apps-modal-title">
	<div class="apps-modal-backdrop" id="navbar-apps-backdrop"></div>
	<div class="apps-modal-content">
		<div class="apps-modal-header">
			<h3 id="apps-modal-title">Aplicativos</h3>
			<button class="apps-close-btn" id="navbar-apps-close" aria-label="Fechar">
				<i class="ri-close-line"></i>
			</button>
		</div>
		<div class="apps-grid">
			<!-- Eventos -->
			<a href="<?php echo esc_url( home_url( '/eventos/' ) ); ?>" class="app-item">
				<div class="app-icon" style="background: linear-gradient(135deg, #f45f00, #ff7020);">
					<i class="ri-calendar-event-fill"></i>
				</div>
				<span class="app-label">Eventos</span>
			</a>

			<!-- Classificados -->
			<a href="<?php echo esc_url( home_url( '/classificados/' ) ); ?>" class="app-item">
				<div class="app-icon" style="background: linear-gradient(135deg, #3b82f6, #2563eb);">
					<i class="ri-advertisement-fill"></i>
				</div>
				<span class="app-label">Classificados</span>
			</a>

			<!-- DJs -->
			<a href="<?php echo esc_url( home_url( '/djs/' ) ); ?>" class="app-item">
				<div class="app-icon" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
					<i class="ri-music-2-fill"></i>
				</div>
				<span class="app-label">DJs</span>
			</a>

			<!-- Locais -->
			<a href="<?php echo esc_url( home_url( '/locais/' ) ); ?>" class="app-item">
				<div class="app-icon" style="background: linear-gradient(135deg, #22c55e, #16a34a);">
					<i class="ri-map-pin-fill"></i>
				</div>
				<span class="app-label">Locais</span>
			</a>

			<!-- Radar -->
			<a href="<?php echo esc_url( home_url( '/radar/' ) ); ?>" class="app-item">
				<div class="app-icon" style="background: linear-gradient(135deg, #ec4899, #db2777);">
					<i class="ri-radar-fill"></i>
				</div>
				<span class="app-label">Radar</span>
			</a>

			<!-- Meu Perfil -->
			<a href="<?php echo esc_url( $user_profile_url ); ?>" class="app-item">
				<div class="app-icon" style="background: linear-gradient(135deg, #64748b, #475569);">
					<i class="ri-user-line"></i>
				</div>
				<span class="app-label">Perfil</span>
			</a>

			<!-- Configurações -->
			<a href="<?php echo esc_url( home_url( '/editar-perfil/' ) ); ?>" class="app-item">
				<div class="app-icon" style="background: linear-gradient(135deg, #6366f1, #4f46e5);">
					<i class="ri-settings-3-line"></i>
				</div>
				<span class="app-label">Configurações</span>
			</a>

			<!-- Sair -->
			<a href="<?php echo esc_url( wp_logout_url( home_url() ) ); ?>" class="app-item">
				<div class="app-icon" style="background: linear-gradient(135deg, #ef4444, #dc2626);">
					<i class="ri-logout-box-line"></i>
				</div>
				<span class="app-label">Sair</span>
			</a>
		</div>
	</div>
</div>
<?php endif; ?>
