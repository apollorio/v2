<?php

/**
 * Email verification template content block.
 *
 * Variables: $user_name, $verify_url, $site_name
 *
 * @package Apollo\Email
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<h2 style="color: #121214; font-size: 22px; margin: 0 0 16px;">Verifique seu email &#9989;</h2>

<p>Olá, <?php echo esc_html( $user_name ?? 'usuário(a)' ); ?>!</p>

<p>Para completar seu cadastro no <?php echo esc_html( $site_name ?? 'Apollo Rio' ); ?>, precisamos confirmar seu endereço de email.</p>

<p>Clique no botão abaixo para verificar:</p>

<br>

<table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center">
	<tr>
		<td style="border-radius: 8px; background: <?php echo esc_attr( $brand_color ?? '#f45f00' ); ?>;">
			<a href="<?php echo esc_url( $verify_url ?? '#' ); ?>" class="email-btn" style="display: inline-block; padding: 14px 32px; color: #ffffff; text-decoration: none; font-weight: 600; font-size: 16px;">
				Verificar meu email
			</a>
		</td>
	</tr>
</table>

<br>

<p style="font-size: 14px; color: #666;">
	Se você não criou uma conta, ignore este email.
</p>
