<?php

/**
 * ================================================================================
 * APOLLO AUTH - Login Form Template Part
 * ================================================================================
 * Displays the login form with username/email and password fields.
 *
 * @package Apollo_Social
 * @since 1.0.0
 *
 * PLACEHOLDERS:
 * - {{username_label}} - Label for username field
 * - {{password_label}} - Label for password field
 * - {{remember_label}} - Label for remember me toggle
 * - {{login_button}} - Submit button text
 * - {{forgot_password_text}} - Forgot password link text
 * - {{register_text}} - Register link text
 *
 * STATUS MESSAGES (Updated as per user request):
 * - Line 1: "Versão BETA LAB, caso de bug, relate <a>aqui</a>."
 * - Line 2: "'Sua Plataforma Digital da cultura carioca.'"
 * ================================================================================
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get URLs from config.
$bug_report_url      = '#';
$bug_report_is_modal = true;
?>

<!-- Status Messages -->
<div class="flavor-text" data-tooltip="<?php esc_attr_e( 'Mensagens de status do sistema', 'apollo-social' ); ?>">
	<span data-tooltip="<?php esc_attr_e( 'Versão atual', 'apollo-social' ); ?>">
		> <?php esc_html_e( 'Versão BETA LAB, caso de bug, relate', 'apollo-social' ); ?>
		<a href="#" data-apollo-report-trigger
			style="color: var(--color-accent); cursor: pointer;" data-tooltip="<?php esc_attr_e( 'Reportar bug', 'apollo-social' ); ?>">
			<?php esc_html_e( 'aqui', 'apollo-social' ); ?>
		</a>.
	</span>
</div>
<div class="flavor-text" style="margin-bottom: 16px;" data-tooltip="<?php esc_attr_e( 'Slogan', 'apollo-social' ); ?>">
	<span>> '<?php esc_html_e( 'Sua Plataforma Digital da cultura carioca.', 'apollo-social' ); ?>'</span>
</div>

<!-- Login Form -->
<form id="login-form" method="post"
	action="<?php echo esc_url( isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : home_url() ); ?>"
	data-tooltip="<?php esc_attr_e( '', 'apollo-social' ); ?>">

	<?php wp_nonce_field( 'apollo_login_action', 'apollo_login_nonce' ); ?>

	<!-- Redirect field -->
	<input type="hidden" name="redirect_to" value="<?php echo esc_url( home_url() ); ?>">

	<!-- Username/Email/CPF/Passport Field with Auto-Detection -->
	<div class="form-group" data-tooltip="<?php esc_attr_e( 'Campo de identificação', 'apollo-social' ); ?>">
		<label for="log"><?php esc_html_e( 'Identificação', 'apollo-social' ); ?></label>
		<div class="input-wrapper">
			<span class="input-prefix" id="login-doc-prefix"
				data-tooltip="<?php esc_attr_e( 'Tipo de identificação detectada', 'apollo-social' ); ?>">></span>
			<style>
				/* Simulated placeholder style */
				#log.simulated-placeholder {
					color: #888 !important;
					/* light gray like normal placeholder */
					font-style: italic;
				}

				#log:focus {
					color: inherit !important;
				}
			</style>
			<script>
				document.addEventListener('DOMContentLoaded', function() {
					// Phrases
					const phrases = [
						"Usuárix apollo::rio",
						"@Nome_do_insta",
						"E-mail",
						"CPF",
						"Passport",
						"Sinal de Fumaça"
					];

					// Medium speed settings
					const typeSpeed = 90;
					const deleteSpeed = 45;
					const pauseAtEnd = 1600;
					const pauseBeforeNext = 550;

					const input = document.getElementById('log');
					if (!input) return;

					let phraseIndex = 0;
					let charIndex = 0;
					let isDeleting = false;
					let timeoutId = null;

					// Main typewriter function
					function typePlaceholder() {
						const currentPhrase = phrases[phraseIndex];
						let displayText = "";

						if (isDeleting) {
							displayText = currentPhrase.substring(0, charIndex - 1);
							charIndex--;
						} else {
							displayText = currentPhrase.substring(0, charIndex + 1);
							charIndex++;
						}

						input.value = displayText;
						input.classList.add('simulated-placeholder');

						let delay = isDeleting ? deleteSpeed : typeSpeed;

						if (!isDeleting && charIndex === currentPhrase.length) {
							isDeleting = true;
							delay = pauseAtEnd;
						} else if (isDeleting && charIndex === 0) {
							isDeleting = false;
							phraseIndex = (phraseIndex + 1) % phrases.length;
							delay = pauseBeforeNext;
						}

						timeoutId = setTimeout(typePlaceholder, delay);
					}

					// Start / Stop functions
					function startEffect() {
						if (timeoutId) clearTimeout(timeoutId);
						charIndex = 0;
						isDeleting = false;
						phraseIndex = 0;
						typePlaceholder();
					}

					function stopEffect() {
						if (timeoutId) clearTimeout(timeoutId);
						input.classList.remove('simulated-placeholder');
					}

					// Events
					input.addEventListener('focus', () => {
						stopEffect();
						// Clear if it's one of our placeholder texts
						if (phrases.includes(input.value)) {
							input.value = '';
						}
					});

					input.addEventListener('blur', () => {
						if (input.value.trim() === '') {
							startEffect();
						}
					});

					// Start the effect when page loads
					startEffect();
				});
			</script>
			<input type="text" id="log" name="log"
				placeholder="<?php esc_attr_e( 'e-mail, CPF ou passaporte', 'apollo-social' ); ?>"
				autocomplete="username" required maxlength="50"
				data-tooltip="<?php esc_attr_e( 'Digite seu e-mail, CPF (000.000.000-00) ou passaporte', 'apollo-social' ); ?>">
		</div>
		<p class="small-note" id="login-doc-hint" style="color: rgba(148,163,184,0.7); transition: color 0.3s;">
			<?php esc_html_e( 'Aceita: e-mail, CPF ou passaporte', 'apollo-social' ); ?>
		</p>
	</div>
	<input type="hidden" name="login_doc_type" id="login_doc_type" value="email">

	<!-- Password Field -->
	<div class="form-group" data-tooltip="<?php esc_attr_e( 'Campo de senha', 'apollo-social' ); ?>">
		<label for="pwd"><?php esc_html_e( 'Chave de Acesso', 'apollo-social' ); ?></label>
		<div class="input-wrapper">
			<span class="input-prefix"
				data-tooltip="<?php esc_attr_e( 'Prefixo do campo', 'apollo-social' ); ?>">></span>
			<input type="password" id="pwd" name="pwd" placeholder="<?php esc_attr_e( '••••••••', 'apollo-social' ); ?>"
				autocomplete="current-password" required
				data-tooltip="<?php esc_attr_e( 'Digite sua senha', 'apollo-social' ); ?>">
		</div>
	</div>

	<!-- Remember Session Toggle -->
	<div class="form-group extra-xps" style="display: flex; justify-content: space-between; align-items: center;"
		data-tooltip="<?php esc_attr_e( 'Opções adicionais', 'apollo-social' ); ?>">
		<div class="custom-toggle" data-tooltip="<?php esc_attr_e( 'Manter sessão ativa', 'apollo-social' ); ?>">
			<div class="toggle-track">
				<div class="toggle-thumb bttm-extra"></div>
			</div>
			<span><?php esc_html_e( 'Manter sessão', 'apollo-social' ); ?></span>
			<input type="hidden" name="rememberme" value="0">
		</div>
		<button type="button" class="btn-text bttm-extra" id="forgot-password"
			data-tooltip="<?php esc_attr_e( 'Recuperar acesso', 'apollo-social' ); ?>">
			<?php esc_html_e( 'Esqueci a chave', 'apollo-social' ); ?>
		</button>
	</div>

	<!-- Submit Button -->
	<button type="submit" class="btn-primary"
		data-tooltip="<?php esc_attr_e( 'Acessar o sistema', 'apollo-social' ); ?>">
		<span><?php esc_html_e( 'ACESSAR TERMINAL', 'apollo-social' ); ?></span>
		<i class="ri-arrow-right-line"></i>
	</button>

</form>

<!-- Register Link -->
<div style="text-align: center; margin-top: 20px;"
	data-tooltip="<?php esc_attr_e( 'Link para registro', 'apollo-social' ); ?>">
	<p style="font-size: 12px; color: rgba(148,163,184,0.9);">
		<?php esc_html_e( 'Não possui acesso?', 'apollo-social' ); ?>
		<button type="button" class="btn-text" id="switch-to-register"
			data-tooltip="<?php esc_attr_e( 'Criar nova conta', 'apollo-social' ); ?>">
			<?php esc_html_e( 'Solicitar registro', 'apollo-social' ); ?>
		</button>
	</p>
</div>

<!-- Login Identity Auto-Detection Script -->
<script>
	/**
	 * APOLLO::RIO IDENTITY ENGINE FOR LOGIN
	 * Dual-Mode Validator:
	 * 1. Numeric Start (11 digits) -> CPF with mask
	 * 2. Alpha Start -> Passport (uppercase)
	 * 3. Contains @ -> Email
	 */
	document.addEventListener('DOMContentLoaded', function() {
		const loginInput = document.getElementById('log');
		const loginPrefix = document.getElementById('login-doc-prefix');
		const loginHint = document.getElementById('login-doc-hint');
		const loginDocType = document.getElementById('login_doc_type');

		if (!loginInput) return;

		loginInput.addEventListener('input', function(e) {
			const value = e.target.value.trim();
			const firstChar = value.charAt(0);

			// Reset if empty
			if (value.length === 0) {
				loginPrefix.textContent = '>';
				loginHint.textContent =
					'<?php echo esc_js( __( 'Aceita: e-mail, CPF ou passaporte', 'apollo-social' ) ); ?>';
				loginHint.style.color = 'rgba(148,163,184,0.7)';
				loginDocType.value = 'email';
				return;
			}

			// Check for email (@)
			if (value.includes('@')) {
				loginPrefix.textContent = '@';
				loginHint.textContent =
					'<?php echo esc_js( __( 'E-mail detectado', 'apollo-social' ) ); ?>';
				loginHint.style.color = 'var(--color-accent)';
				loginDocType.value = 'email';
				return;
			}

			// Check if starts with number -> CPF
			if (/^\d/.test(firstChar)) {
				loginDocType.value = 'cpf';
				loginPrefix.textContent = 'BR';
				loginHint.style.color = 'var(--color-accent)';

				// Apply CPF mask: 000.000.000-00
				let digits = value.replace(/\D/g, '');
				if (digits.length <= 11) {
					digits = digits.replace(/(\d{3})(\d)/, '$1.$2');
					digits = digits.replace(/(\d{3})(\d)/, '$1.$2');
					digits = digits.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
					e.target.value = digits;

					// Validate CPF
					const cleanCpf = digits.replace(/\D/g, '');
					if (cleanCpf.length === 11) {
						if (validateCPFLogin(cleanCpf)) {
							loginHint.innerHTML =
								'<i class="ri-checkbox-circle-fill" style="color: var(--color-success);"></i> <?php echo esc_js( __( 'CPF válido', 'apollo-social' ) ); ?>';
							loginHint.style.color = 'var(--color-success)';
						} else {
							loginHint.innerHTML =
								'<i class="ri-error-warning-fill" style="color: var(--color-danger);"></i> <?php echo esc_js( __( 'CPF inválido', 'apollo-social' ) ); ?>';
							loginHint.style.color = 'var(--color-danger)';
						}
					} else {
						loginHint.textContent =
							'<?php echo esc_js( __( 'CPF detectado', 'apollo-social' ) ); ?>';
					}
				}
				return;
			}

			// Check if starts with letter -> Could be username or passport
			if (/^[a-zA-Z]/.test(firstChar)) {
				// Check if it looks like a passport (alphanumeric, reasonable length)
				if (/^[A-Z0-9]{6,12}$/.test(value.toUpperCase())) {
					loginDocType.value = 'passport';
					loginPrefix.textContent = '✈';
					loginHint.textContent =
						'<?php echo esc_js( __( 'Passaporte detectado', 'apollo-social' ) ); ?>';
					loginHint.style.color = 'var(--color-accent)';
					e.target.value = value.toUpperCase();
					return;
				} else {
					// Regular username - don't convert case
					loginDocType.value = 'username';
					loginPrefix.textContent = '>';
					loginHint.textContent =
						'<?php echo esc_js( __( 'Nome de usuário detectado', 'apollo-social' ) ); ?>';
					loginHint.style.color = 'var(--color-accent)';
					return;
				}
			}
		});

		// CPF Validation (Mod 11 algorithm)
		function validateCPFLogin(cpf) {
			if (/^(\d)\1{10}$/.test(cpf)) return false;

			let sum = 0,
				remainder;
			for (let i = 1; i <= 9; i++) sum += parseInt(cpf.substring(i - 1, i)) * (11 - i);
			remainder = (sum * 10) % 11;
			if (remainder === 10 || remainder === 11) remainder = 0;
			if (remainder !== parseInt(cpf.substring(9, 10))) return false;

			sum = 0;
			for (let i = 1; i <= 10; i++) sum += parseInt(cpf.substring(i - 1, i)) * (12 - i);
			remainder = (sum * 10) % 11;
			if (remainder === 10 || remainder === 11) remainder = 0;
			if (remainder !== parseInt(cpf.substring(10, 11))) return false;

			return true;
		}
	});
</script>

<?php
// ── Apollo Report Modal (shared component) ──
if ( file_exists( WP_PLUGIN_DIR . '/apollo-core/includes/report-modal.php' ) ) {
	require_once WP_PLUGIN_DIR . '/apollo-core/includes/report-modal.php';
	apollo_render_report_modal( 'apolloLoginBugReport', 'frontend' );
}
