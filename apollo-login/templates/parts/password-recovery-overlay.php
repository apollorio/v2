<?php

/**
 * Password Recovery Overlay
 * Fullscreen slide-in panel from right (100vw → 0)
 *
 * @package Apollo_Login
 * @since 6.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<!-- PASSWORD RECOVERY OVERLAY (Fullscreen Slide-in from Right) -->
<div id="password-recovery-overlay" class="password-recovery-overlay" style="display: none;">
	<div class="overlay-backdrop" data-close-overlay="true"></div>

	<div class="overlay-panel">
		<!-- Close Button -->
		<button type="button" class="overlay-close" aria-label="<?php esc_attr_e( 'Fechar', 'apollo-login' ); ?>" data-close-overlay="true">
			<i class="ri-close-line"></i>
		</button>

		<!-- Panel Header -->
		<header class="overlay-header">
			<div class="logo-mark">
				<svg width="48" height="48" viewBox="0 0 150 150" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M75 135C59.087 135 45 130.217 45 124.5C45 118.783 59.087 114 75 114C90.913 114 105 118.783 105 124.5C105 130.217 90.913 135 75 135Z" fill="url(#paint0_linear_logo)" />
					<circle cx="75" cy="45" r="30" fill="url(#paint1_linear_logo)" />
					<path d="M75 75L105 120H45L75 75Z" fill="url(#paint2_linear_logo)" />
					<defs>
						<linearGradient id="paint0_linear_logo" x1="45" y1="124.5" x2="105" y2="124.5" gradientUnits="userSpaceOnUse">
							<stop stop-color="#F45F00" />
							<stop offset="1" stop-color="#FF8640" />
						</linearGradient>
						<linearGradient id="paint1_linear_logo" x1="45" y1="45" x2="105" y2="45" gradientUnits="userSpaceOnUse">
							<stop stop-color="#651FFF" />
							<stop offset="1" stop-color="#9C4DFF" />
						</linearGradient>
						<linearGradient id="paint2_linear_logo" x1="75" y1="75" x2="75" y2="120" gradientUnits="userSpaceOnUse">
							<stop stop-color="#F45F00" />
							<stop offset="1" stop-color="#651FFF" />
						</linearGradient>
					</defs>
				</svg>
			</div>
			<h1><?php esc_html_e( 'Recuperar Senha', 'apollo-login' ); ?></h1>
			<p class="subtitle"><?php esc_html_e( 'Digite seu e-mail para receber o link de recuperação', 'apollo-login' ); ?></p>
		</header>

		<!-- Form Content -->
		<div class="overlay-content">
			<form id="forgot-password-form" method="post" class="apollo-form">
				<?php wp_nonce_field( 'apollo_forgot_password', 'forgot_password_nonce' ); ?>

				<div class="form-group">
					<label for="forgot_email" class="form-label">
						<i class="ri-mail-line"></i>
						<?php esc_html_e( 'E-mail', 'apollo-login' ); ?>
					</label>
					<input
						type="email"
						id="forgot_email"
						name="user_email"
						class="form-input"
						placeholder="<?php esc_attr_e( 'seu@email.com', 'apollo-login' ); ?>"
						required
						autocomplete="email"
						autofocus />
					<span class="field-hint"><?php esc_html_e( 'Usamos e-mail para autenticação por segurança', 'apollo-login' ); ?></span>
				</div>

				<div class="form-actions">
					<button type="submit" class="btn btn-primary btn-block">
						<span><?php esc_html_e( 'ENVIAR LINK DE RECUPERAÇÃO', 'apollo-login' ); ?></span>
						<i class="ri-mail-send-line"></i>
					</button>

					<button type="button" class="btn btn-ghost btn-block" data-close-overlay="true">
						<i class="ri-arrow-left-line"></i>
						<span><?php esc_html_e( 'Voltar ao Login', 'apollo-login' ); ?></span>
					</button>
				</div>

				<!-- Security Notice -->
				<div class="security-notice">
					<i class="ri-shield-check-line"></i>
					<p><?php esc_html_e( 'Por segurança, o link expira em 1 hora e só pode ser usado uma vez.', 'apollo-login' ); ?></p>
				</div>
			</form>
		</div>

		<!-- Footer -->
		<footer class="overlay-footer">
			<p class="help-text">
				<?php esc_html_e( 'Problemas?', 'apollo-login' ); ?>
				<a href="/contato" class="link-orange"><?php esc_html_e( 'Entre em contato', 'apollo-login' ); ?></a>
			</p>
		</footer>
	</div>
</div>

<script>
	document.addEventListener('DOMContentLoaded', function() {
		const overlay = document.getElementById('password-recovery-overlay');
		const panel = overlay?.querySelector('.overlay-panel');

		if (!overlay || !panel) return;

		// Check URL params
		const urlParams = new URLSearchParams(window.location.search);
		const showOverlay = urlParams.get('quero') === 'recuperar-chave' ||
			urlParams.get('action') === 'lostpassword';

		// Initialize GSAP
		if (typeof gsap !== 'undefined') {
			gsap.set(panel, {
				x: '100%'
			});
		}

		// Show overlay if URL param exists
		if (showOverlay) {
			openPasswordOverlay();
		}

		// Open overlay function
		window.openPasswordOverlay = function() {
			overlay.style.display = 'flex';
			document.body.style.overflow = 'hidden';

			if (typeof gsap !== 'undefined') {
				gsap.to(panel, {
					x: 0,
					duration: 0.6,
					ease: 'power3.out'
				});
			} else {
				panel.style.transform = 'translateX(0)';
			}

			// Focus email input
			setTimeout(() => {
				const emailInput = document.getElementById('forgot_email');
				if (emailInput) emailInput.focus();
			}, 650);
		};

		// Close overlay function
		window.closePasswordOverlay = function() {
			if (typeof gsap !== 'undefined') {
				gsap.to(panel, {
					x: '100%',
					duration: 0.5,
					ease: 'power3.in',
					onComplete: () => {
						overlay.style.display = 'none';
						document.body.style.overflow = '';
					}
				});
			} else {
				panel.style.transform = 'translateX(100%)';
				setTimeout(() => {
					overlay.style.display = 'none';
					document.body.style.overflow = '';
				}, 500);
			}

			// Update URL without reload
			const url = new URL(window.location);
			url.searchParams.delete('quero');
			url.searchParams.delete('action');
			window.history.replaceState({}, '', url);
		};

		// Close on backdrop/button click
		overlay.addEventListener('click', function(e) {
			if (e.target.dataset.closeOverlay || e.target.closest('[data-close-overlay]')) {
				closePasswordOverlay();
			}
		});

		// Close on ESC key
		document.addEventListener('keydown', function(e) {
			if (e.key === 'Escape' && overlay.style.display === 'flex') {
				closePasswordOverlay();
			}
		});

		// Handle form submission
		const form = document.getElementById('forgot-password-form');
		if (form) {
			form.addEventListener('submit', async function(e) {
				e.preventDefault();

				const email = document.getElementById('forgot_email').value;
				const submitBtn = form.querySelector('button[type="submit"]');
				const originalHTML = submitBtn.innerHTML;

				submitBtn.disabled = true;
				submitBtn.innerHTML = '<span><?php esc_html_e( 'ENVIANDO...', 'apollo-login' ); ?></span><i class="ri-loader-4-line ri-spin"></i>';

				try {
					const formData = new FormData(form);
					formData.append('action', 'apollo_forgot_password');

					const response = await fetch(window.apolloAuthConfig?.ajaxUrl || '/wp-admin/admin-ajax.php', {
						method: 'POST',
						body: formData
					});

					const result = await response.json();

					if (result.success) {
						// Show success notification
						if (window.showNotification) {
							window.showNotification(
								result.data.message || '<?php esc_js( __( 'Link enviado! Verifique seu e-mail.', 'apollo-login' ) ); ?>',
								'success'
							);
						}

						// Close overlay and redirect to login
						setTimeout(() => {
							closePasswordOverlay();
							setTimeout(() => {
								window.location.href = '/acesso';
							}, 300);
						}, 1500);
					} else {
						// Show error
						if (window.showNotification) {
							window.showNotification(
								result.data || '<?php esc_js( __( 'Erro ao enviar e-mail. Verifique se o e-mail está correto.', 'apollo-login' ) ); ?>',
								'error'
							);
						}

						submitBtn.disabled = false;
						submitBtn.innerHTML = originalHTML;
					}
				} catch (error) {
					console.error('Forgot password error:', error);

					if (window.showNotification) {
						window.showNotification(
							'<?php esc_js( __( 'Erro de conexão. Tente novamente.', 'apollo-login' ) ); ?>',
							'error'
						);
					}

					submitBtn.disabled = false;
					submitBtn.innerHTML = originalHTML;
				}
			});
		}
	});
</script>
