<?php

/**
 * Email Verification Template Part
 *
 * Renders different states:
 * 1. SUCCESS → if EmailVerification already redirected (shouldn't reach here)
 * 2. ERROR → if server-side verification failed (token invalid)
 * 3. EXPIRED → if token TTL exceeded (offers resend option)
 * 4. NO TOKEN → shows the resend verification form
 * 5. PROCESSING → AJAX fallback if server-side didn't catch it
 *
 * @package Apollo\Login
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$token   = isset( $_GET['token'] ) ? sanitize_text_field( wp_unslash( $_GET['token'] ) ) : '';
$user_id = isset( $_GET['user'] ) ? absint( $_GET['user'] ) : 0;

// Get server-side verification result (set by EmailVerification::process_verification())
$result = \Apollo\Login\Auth\EmailVerification::$verification_result;
?>

<div class="apollo-email-verification">

	<?php if ( $result && 'error' === $result['status'] ) : ?>
		<!-- TOKEN INVALID -->
		<div class="apollo-verification-status">
			<div style="font-size: 48px; margin-bottom: 16px; opacity: 0.6;">✕</div>
			<h2 style="color: #ef4444;"><?php esc_html_e( 'Verificação Falhou', 'apollo-login' ); ?></h2>
			<p style="margin-top: 12px; color: rgba(148,163,184,0.9);">
				<?php echo esc_html( $result['message'] ); ?>
			</p>
			<div style="margin-top: 24px;">
				<a href="<?php echo esc_url( home_url( '/acesso/' ) ); ?>" class="apollo-btn" style="display: inline-block;">
					<?php esc_html_e( 'Voltar ao Login', 'apollo-login' ); ?>
				</a>
			</div>
		</div>

	<?php elseif ( $result && 'expired' === $result['status'] ) : ?>
		<!-- TOKEN EXPIRED — offer resend -->
		<div class="apollo-verification-status">
			<div style="font-size: 48px; margin-bottom: 16px; opacity: 0.6;">⏰</div>
			<h2 style="color: #f59e0b;"><?php esc_html_e( 'Link Expirado', 'apollo-login' ); ?></h2>
			<p style="margin-top: 12px; color: rgba(148,163,184,0.9);">
				<?php echo esc_html( $result['message'] ); ?>
			</p>
			<div class="apollo-verification-form" style="margin-top: 24px;">
				<form id="apollo-resend-verification" method="post">
					<?php wp_nonce_field( 'apollo_resend_verification', 'apollo_resend_nonce' ); ?>
					<div class="apollo-form-group">
						<label for="verification_email"><?php esc_html_e( 'Seu e-mail', 'apollo-login' ); ?></label>
						<input type="email" id="verification_email" name="email" required
							placeholder="<?php esc_attr_e( 'seu@email.com', 'apollo-login' ); ?>">
					</div>
					<button type="submit" class="apollo-btn primary">
						<?php esc_html_e( 'Reenviar Verificação', 'apollo-login' ); ?>
					</button>
				</form>
				<div id="resend-feedback" style="margin-top: 12px;"></div>
			</div>
		</div>

	<?php elseif ( ! empty( $token ) && $user_id > 0 ) : ?>
		<!-- AJAX FALLBACK — server-side didn't process (shouldn't normally happen) -->
		<div class="apollo-verification-status">
			<h2><?php esc_html_e( 'Verificando Email...', 'apollo-login' ); ?></h2>
			<div class="apollo-spinner"></div>
			<p style="margin-top: 12px; color: rgba(148,163,184,0.7); font-size: 12px;">
				<?php esc_html_e( 'Aguarde enquanto verificamos seu e-mail.', 'apollo-login' ); ?>
			</p>
		</div>

		<script>
			(function() {
				'use strict';

				var statusEl = document.querySelector('.apollo-verification-status');
				var apiUrl = <?php echo wp_json_encode( esc_url_raw( rest_url( 'apollo/v1/auth/verify-email' ) ) ); ?>;

				var body = new URLSearchParams();
				body.append('token', <?php echo wp_json_encode( $token ); ?>);
				body.append('user_id', <?php echo wp_json_encode( $user_id ); ?>);

				fetch(apiUrl, {
						method: 'POST',
						headers: {
							'Content-Type': 'application/x-www-form-urlencoded'
						},
						body: body,
						credentials: 'same-origin'
					})
					.then(function(r) {
						return r.json();
					})
					.then(function(data) {
						if (data.success || (data.data && data.data.status === 200)) {
							var msg = (data.data && data.data.message) || data.message || 'Email verificado!';
							statusEl.innerHTML = '<div style="font-size:48px;margin-bottom:16px;">✓</div>' +
								'<h2 style="color:var(--color-success,#22c55e);">Email Verificado!</h2>' +
								'<p style="margin-top:12px;color:rgba(148,163,184,0.9);">' + msg + '</p>' +
								'<a href="<?php echo esc_url( home_url( '/acesso/' ) ); ?>" class="apollo-btn" style="display:inline-block;margin-top:20px;">Fazer Login</a>';
						} else {
							var errMsg = (data.data && data.data.message) || (data.message) || 'Token inválido.';
							statusEl.innerHTML = '<div style="font-size:48px;margin-bottom:16px;opacity:0.6;">✕</div>' +
								'<h2 style="color:#ef4444;">Verificação Falhou</h2>' +
								'<p style="margin-top:12px;color:rgba(148,163,184,0.9);">' + errMsg + '</p>' +
								'<a href="<?php echo esc_url( home_url( '/acesso/' ) ); ?>" class="apollo-btn" style="display:inline-block;margin-top:20px;">Voltar ao Login</a>';
						}
					})
					.catch(function() {
						statusEl.innerHTML = '<div style="font-size:48px;margin-bottom:16px;opacity:0.6;">✕</div>' +
							'<h2 style="color:#ef4444;">Erro de Conexão</h2>' +
							'<p style="margin-top:12px;color:rgba(148,163,184,0.9);">Erro ao verificar email. Tente novamente.</p>' +
							'<a href="<?php echo esc_url( home_url( '/acesso/' ) ); ?>" class="apollo-btn" style="display:inline-block;margin-top:20px;">Voltar ao Login</a>';
					});
			})();
		</script>

	<?php else : ?>
		<!-- NO TOKEN — show resend form -->
		<div class="apollo-verification-form">
			<div style="font-size: 48px; margin-bottom: 16px; opacity: 0.6;">✉</div>
			<h2><?php esc_html_e( 'Verificar Email', 'apollo-login' ); ?></h2>
			<p style="margin-top: 12px; color: rgba(148,163,184,0.9);">
				<?php esc_html_e( 'Verifique seu email para ativar sua conta. Não recebeu? Solicite outro abaixo.', 'apollo-login' ); ?>
			</p>

			<form id="apollo-resend-verification" method="post" style="margin-top: 24px;">
				<?php wp_nonce_field( 'apollo_resend_verification', 'apollo_resend_nonce' ); ?>
				<div class="apollo-form-group">
					<label for="verification_email"><?php esc_html_e( 'Seu e-mail', 'apollo-login' ); ?></label>
					<input type="email" id="verification_email" name="email" required
						placeholder="<?php esc_attr_e( 'seu@email.com', 'apollo-login' ); ?>">
				</div>
				<button type="submit" class="apollo-btn primary">
					<?php esc_html_e( 'Reenviar Verificação', 'apollo-login' ); ?>
				</button>
			</form>
			<div id="resend-feedback" style="margin-top: 12px;"></div>

			<div class="apollo-verification-links" style="margin-top: 20px;">
				<a href="<?php echo esc_url( home_url( '/acesso/' ) ); ?>">
					<?php esc_html_e( 'Voltar ao Login', 'apollo-login' ); ?>
				</a>
			</div>
		</div>
	<?php endif; ?>
</div>

<script>
	(function() {
		'use strict';

		var form = document.getElementById('apollo-resend-verification');
		if (!form) return;

		form.addEventListener('submit', function(e) {
			e.preventDefault();

			var email = form.querySelector('[name="email"]').value.trim();
			var feedback = document.getElementById('resend-feedback');
			var btn = form.querySelector('button[type="submit"]');

			if (!email) return;

			btn.disabled = true;
			btn.textContent = 'Enviando...';
			feedback.innerHTML = '';

			var body = new URLSearchParams();
			body.append('action', 'apollo_resend_verification');
			body.append('email', email);
			body.append('nonce', <?php echo wp_json_encode( wp_create_nonce( 'apollo_resend_verification' ) ); ?>);

			fetch(<?php echo wp_json_encode( admin_url( 'admin-ajax.php' ) ); ?>, {
					method: 'POST',
					headers: {
						'Content-Type': 'application/x-www-form-urlencoded'
					},
					body: body,
					credentials: 'same-origin'
				})
				.then(function(r) {
					return r.json();
				})
				.then(function(data) {
					btn.disabled = false;
					btn.textContent = 'Reenviar Verificação';

					if (data.success) {
						feedback.innerHTML = '<p style="color:#22c55e;">✓ ' + (data.data.message || 'E-mail reenviado!') + '</p>';
					} else {
						feedback.innerHTML = '<p style="color:#ef4444;">✕ ' + (data.data && data.data.message || 'Erro ao reenviar.') + '</p>';
					}
				})
				.catch(function() {
					btn.disabled = false;
					btn.textContent = 'Reenviar Verificação';
					feedback.innerHTML = '<p style="color:#ef4444;">✕ Erro de conexão. Tente novamente.</p>';
				});
		});
	})();
</script>