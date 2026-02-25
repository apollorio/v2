<?php
/**
 * Password Reset Form Template Part
 *
 * @package Apollo\Login
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<!-- Password Reset Form -->
<form id="reset-form" method="post" action="<?php echo esc_url( wp_lostpassword_url() ); ?>" data-tooltip="<?php esc_attr_e( 'Formulário de redefinição de senha', 'apollo-login' ); ?>">

	<?php wp_nonce_field( 'apollo_reset_action', 'apollo_reset_nonce' ); ?>

	<!-- Email Field -->
	<div class="form-group" data-tooltip="<?php esc_attr_e( 'Campo de e-mail', 'apollo-login' ); ?>">
		<label for="user_login"><?php esc_html_e( 'E-mail', 'apollo-login' ); ?></label>
		<div class="input-wrapper">
			<span class="input-prefix" data-tooltip="<?php esc_attr_e( 'Prefixo do campo', 'apollo-login' ); ?>">></span>
			<input
				type="email"
				id="user_login"
				name="user_login"
				placeholder="<?php esc_attr_e( 'seu@email.com', 'apollo-login' ); ?>"
				autocomplete="email"
				required
				data-tooltip="<?php esc_attr_e( 'Digite seu e-mail cadastrado', 'apollo-login' ); ?>"
			>
		</div>
	</div>

	<!-- Submit Button -->
	<div class="form-group" data-tooltip="<?php esc_attr_e( 'Botão de envio', 'apollo-login' ); ?>">
		<button type="submit" class="apollo-btn primary" data-tooltip="<?php esc_attr_e( 'Enviar instruções de redefinição', 'apollo-login' ); ?>">
			<?php esc_html_e( 'Enviar Instruções', 'apollo-login' ); ?>
		</button>
	</div>

	<!-- Links -->
	<div class="form-links" data-tooltip="<?php esc_attr_e( 'Links de navegação', 'apollo-login' ); ?>">
		<a href="<?php echo esc_url( get_permalink( get_option( 'apollo_login_page_id' ) ) ); ?>" data-tooltip="<?php esc_attr_e( 'Voltar ao login', 'apollo-login' ); ?>">
			<?php esc_html_e( 'Voltar ao Login', 'apollo-login' ); ?>
		</a>
		|
		<a href="<?php echo esc_url( get_permalink( get_option( 'apollo_register_page_id' ) ) ); ?>" data-tooltip="<?php esc_attr_e( 'Criar conta', 'apollo-login' ); ?>">
			<?php esc_html_e( 'Criar Conta', 'apollo-login' ); ?>
		</a>
	</div>

</form>
