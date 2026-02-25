<?php

/**
 * Password reset email template content block.
 *
 * Variables: $user_name, $reset_url, $site_name, $expires_in
 *
 * @package Apollo\Email
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<h2 style="color: #121214; font-size: 22px; margin: 0 0 16px;">Nova chave de acesso &#128274;</h2>

<p>Olá, <?php echo esc_html( $user_name ?? 'usuário(a)' ); ?>!</p>

<p>Recebemos uma solicitação para redefinir sua senha no <?php echo esc_html( $site_name ?? 'Apollo Rio' ); ?>.</p>

<p>Clique no botão abaixo para criar uma nova senha:</p>

<br>

<table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center">
	<tr>
		<td style="border-radius: 8px; background: <?php echo esc_attr( $brand_color ?? '#f45f00' ); ?>;">
			<a href="<?php echo esc_url( $reset_url ?? '#' ); ?>" class="email-btn" style="display: inline-block; padding: 14px 32px; color: #ffffff; text-decoration: none; font-weight: 600; font-size: 16px;">
				Redefinir minha senha
			</a>
		</td>
	</tr>
</table>

<br>

<hr style="border: 0; border-top: 1px solid #e4e4e7; margin: 24px 0;">

<p style="font-size: 14px; color: #666;">
	&#9200; Este link expira em <strong><?php echo esc_html( $expires_in ?? '1 hora' ); ?></strong>.<br>
	Se você não solicitou a redefinição, ignore este email — sua senha atual permanece segura.
</p>
