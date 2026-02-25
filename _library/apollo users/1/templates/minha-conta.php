<?php
/**
 * Minha Conta Template - /minha-conta
 *
 * Account management page (ported from UsersWP account patterns).
 * Sections: account, change-password, privacy, delete-account
 *
 * @package Apollo\Users
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! is_user_logged_in() ) {
	wp_redirect( home_url( '/acesso/' ) );
	exit;
}

global $apollo_account_section;
$section = $apollo_account_section ?? 'account';
$valid_sections = array_keys( APOLLO_USERS_ACCOUNT_SECTIONS );
if ( ! in_array( $section, $valid_sections, true ) ) {
	$section = 'account';
}

$user    = wp_get_current_user();
$user_id = $user->ID;

// Current data
$social_name     = get_user_meta( $user_id, '_apollo_social_name', true );
$bio             = get_user_meta( $user_id, '_apollo_bio', true );
$phone           = get_user_meta( $user_id, '_apollo_phone', true );
$location        = get_user_meta( $user_id, 'user_location', true );
$website         = get_user_meta( $user_id, '_apollo_website', true );
$privacy_profile = get_user_meta( $user_id, '_apollo_privacy_profile', true ) ?: 'public';
$privacy_email   = get_user_meta( $user_id, '_apollo_privacy_email', true );
$disable_author  = get_user_meta( $user_id, '_apollo_disable_author_url', true );
$avatar_url      = \Apollo\Users\apollo_get_user_avatar_url( $user_id, 'medium' );
$nonce           = wp_create_nonce( 'apollo_account_nonce' );

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Minha Conta | Apollo</title>

	<script src="https://cdn.apollo.rio.br/v1.0.0/core.js" fetchpriority="high"></script>
	<link href="https://fonts.googleapis.com/css2?family=Shrikhand&family=Space+Grotesk:wght@300..700&family=Space+Mono:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
	<link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

	<?php
	wp_enqueue_style( 'apollo-users-account' );
	wp_enqueue_script( 'apollo-users-account' );
	wp_localize_script( 'apollo-users-account', 'apolloAccount', [
		'ajaxUrl' => admin_url( 'admin-ajax.php' ),
		'nonce'   => $nonce,
		'userId'  => $user_id,
	] );
	wp_head();
	?>
</head>
<body class="apollo-account-page">

<div class="account-container">
	<!-- Back to profile -->
	<div class="account-topbar">
		<a href="<?php echo esc_url( home_url( '/id/' . $user->user_login . '/' ) ); ?>" class="account-back">
			<i class="ri-arrow-left-line"></i> Meu Perfil
		</a>
		<h1 class="account-page-title">Minha Conta</h1>
		<a href="<?php echo esc_url( home_url( '/sair/' ) ); ?>" class="account-logout">
			<i class="ri-logout-box-r-line"></i> Sair
		</a>
	</div>

	<div class="account-layout">
		<!-- Sidebar Navigation -->
		<nav class="account-nav">
			<div class="account-nav-avatar">
				<img src="<?php echo esc_url( $avatar_url ); ?>" alt="">
				<span class="account-nav-name"><?php echo esc_html( $social_name ?: $user->display_name ); ?></span>
				<span class="account-nav-handle">@<?php echo esc_html( $user->user_login ); ?></span>
			</div>
			<ul class="account-nav-list">
				<?php foreach ( APOLLO_USERS_ACCOUNT_SECTIONS as $key => $label ) :
					$active = ( $key === $section ) ? ' active' : '';
					$icon_map = [
						'account'         => 'ri-user-settings-line',
						'change-password' => 'ri-lock-password-line',
						'privacy'         => 'ri-shield-keyhole-line',
						'delete-account'  => 'ri-delete-bin-line',
					];
				?>
					<li>
						<a href="<?php echo esc_url( home_url( '/minha-conta/' . $key . '/' ) ); ?>" class="account-nav-item<?php echo $active; ?>">
							<i class="<?php echo esc_attr( $icon_map[ $key ] ?? 'ri-settings-line' ); ?>"></i>
							<?php echo esc_html( $label ); ?>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</nav>

		<!-- Content Area -->
		<div class="account-content">

			<?php if ( $section === 'account' ) : ?>
			<!-- ═══════ ACCOUNT INFO ═══════ -->
			<div class="account-section">
				<h2 class="account-section-title">Informações da Conta</h2>
				<form id="account-form" class="account-form">
					<input type="hidden" name="action" value="apollo_update_account">
					<input type="hidden" name="nonce" value="<?php echo esc_attr( $nonce ); ?>">

					<div class="form-group">
						<label for="social_name">Nome Social</label>
						<input type="text" id="social_name" name="social_name" value="<?php echo esc_attr( $social_name ?: $user->display_name ); ?>" required>
					</div>

					<div class="form-group">
						<label for="email">E-mail</label>
						<input type="email" id="email" name="email" value="<?php echo esc_attr( $user->user_email ); ?>" required>
					</div>

					<div class="form-group">
						<label for="bio">Bio</label>
						<textarea id="bio" name="bio" rows="4" maxlength="500" placeholder="Conte sobre você..."><?php echo esc_textarea( $bio ); ?></textarea>
						<span class="form-hint"><span id="bio-count"><?php echo mb_strlen( $bio ); ?></span>/500</span>
					</div>

					<div class="form-row">
						<div class="form-group">
							<label for="location">Localização</label>
							<input type="text" id="location" name="location" value="<?php echo esc_attr( $location ); ?>" placeholder="Rio de Janeiro, RJ">
						</div>
						<div class="form-group">
							<label for="phone">Telefone/WhatsApp</label>
							<input type="tel" id="phone" name="phone" value="<?php echo esc_attr( $phone ); ?>" placeholder="(21) 99999-9999">
						</div>
					</div>

					<div class="form-group">
						<label for="website">Website</label>
						<input type="url" id="website" name="website" value="<?php echo esc_attr( $website ); ?>" placeholder="https://">
					</div>

					<div class="form-group">
						<label>Instagram</label>
						<input type="text" value="@<?php echo esc_attr( $user->user_login ); ?>" disabled class="form-input-disabled">
						<span class="form-hint">O Instagram é o seu login e não pode ser alterado.</span>
					</div>

					<button type="submit" class="account-btn-primary" id="save-account">
						<i class="ri-save-line"></i> Salvar Alterações
					</button>
				</form>
			</div>

			<?php elseif ( $section === 'change-password' ) : ?>
			<!-- ═══════ CHANGE PASSWORD ═══════ -->
			<div class="account-section">
				<h2 class="account-section-title">Alterar Senha</h2>
				<form id="password-form" class="account-form">
					<input type="hidden" name="action" value="apollo_change_password">
					<input type="hidden" name="nonce" value="<?php echo esc_attr( $nonce ); ?>">

					<div class="form-group">
						<label for="current_password">Senha Atual</label>
						<input type="password" id="current_password" name="current_password" required autocomplete="current-password">
					</div>

					<div class="form-group">
						<label for="new_password">Nova Senha</label>
						<input type="password" id="new_password" name="new_password" required minlength="8" autocomplete="new-password">
						<span class="form-hint">Mínimo 8 caracteres.</span>
					</div>

					<div class="form-group">
						<label for="confirm_password">Confirmar Nova Senha</label>
						<input type="password" id="confirm_password" name="confirm_password" required autocomplete="new-password">
					</div>

					<div id="password-strength"></div>

					<button type="submit" class="account-btn-primary">
						<i class="ri-lock-password-line"></i> Alterar Senha
					</button>
				</form>
			</div>

			<?php elseif ( $section === 'privacy' ) : ?>
			<!-- ═══════ PRIVACY ═══════ -->
			<div class="account-section">
				<h2 class="account-section-title">Privacidade</h2>
				<form id="privacy-form" class="account-form">
					<input type="hidden" name="action" value="apollo_update_privacy">
					<input type="hidden" name="nonce" value="<?php echo esc_attr( $nonce ); ?>">

					<div class="form-group">
						<label for="privacy_profile">Visibilidade do Perfil</label>
						<select id="privacy_profile" name="privacy_profile">
							<option value="public" <?php selected( $privacy_profile, 'public' ); ?>>Público - Todos podem ver</option>
							<option value="members" <?php selected( $privacy_profile, 'members' ); ?>>Membros - Apenas logados</option>
							<option value="private" <?php selected( $privacy_profile, 'private' ); ?>>Privado - Somente eu</option>
						</select>
					</div>

					<div class="form-group form-group--checkbox">
						<label>
							<input type="checkbox" name="privacy_email" value="1" <?php checked( $privacy_email ); ?>>
							Ocultar e-mail no perfil público
						</label>
					</div>

					<div class="form-group form-group--checkbox">
						<label>
							<input type="checkbox" name="disable_author_url" value="1" <?php checked( $disable_author ); ?>>
							Bloquear URL de autor do WordPress
						</label>
					</div>

					<button type="submit" class="account-btn-primary">
						<i class="ri-shield-check-line"></i> Salvar Privacidade
					</button>
				</form>
			</div>

			<?php elseif ( $section === 'delete-account' ) : ?>
			<!-- ═══════ DELETE ACCOUNT ═══════ -->
			<div class="account-section account-section--danger">
				<h2 class="account-section-title account-section-title--danger">
					<i class="ri-error-warning-line"></i> Excluir Conta
				</h2>
				<div class="account-danger-notice">
					<p>Esta ação é <strong>irreversível</strong>. Todos os seus dados, publicações e interações serão permanentemente removidos.</p>
				</div>

				<?php if ( current_user_can( 'manage_options' ) ) : ?>
					<p class="account-info">Administradores não podem excluir suas contas por esta página.</p>
				<?php else : ?>
					<form id="delete-form" class="account-form">
						<input type="hidden" name="action" value="apollo_delete_account">
						<input type="hidden" name="nonce" value="<?php echo esc_attr( $nonce ); ?>">

						<div class="form-group">
							<label for="delete_password">Digite sua senha para confirmar</label>
							<input type="password" id="delete_password" name="password" required autocomplete="current-password">
						</div>

						<div class="form-group form-group--checkbox">
							<label>
								<input type="checkbox" id="delete_confirm" required>
								Eu entendo que esta ação é irreversível e desejo excluir minha conta permanentemente.
							</label>
						</div>

						<button type="submit" class="account-btn-danger" id="delete-btn" disabled>
							<i class="ri-delete-bin-line"></i> Excluir Minha Conta
						</button>
					</form>
				<?php endif; ?>
			</div>
			<?php endif; ?>

		</div><!-- /account-content -->
	</div><!-- /account-layout -->
</div><!-- /account-container -->

<?php wp_footer(); ?>
</body>
</html>
