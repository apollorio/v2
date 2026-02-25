<?php

/**
 * Welcome email template content block.
 *
 * Variables: $user_name, $username, $profile_url, $site_name, $site_url
 *
 * @package Apollo\Email
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<h2 style="color: #121214; font-size: 22px; margin: 0 0 16px;">Olá, <?php echo esc_html( $user_name ?? 'novo(a) membro' ); ?>! &#127881;</h2>

<p>Você acaba de entrar para a melhor plataforma da cena noturna do Rio de Janeiro.</p>

<p>Agora você faz parte de uma comunidade vibrante de produtores, DJs, artistas e entusiastas que movem a noite carioca.</p>

<hr style="border: 0; border-top: 1px solid #e4e4e7; margin: 24px 0;">

<p><strong>O que fazer agora?</strong></p>

<table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
	<tr>
		<td style="padding: 8px 0;">&#127926; &nbsp; Explore os próximos eventos</td>
	</tr>
	<tr>
		<td style="padding: 8px 0;">&#127911; &nbsp; Descubra DJs e artistas</td>
	</tr>
	<tr>
		<td style="padding: 8px 0;">&#128100; &nbsp; Complete seu perfil</td>
	</tr>
	<tr>
		<td style="padding: 8px 0;">&#129309; &nbsp; Conecte-se com a comunidade</td>
	</tr>
</table>

<br>

<table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center">
	<tr>
		<td style="border-radius: 8px; background: <?php echo esc_attr( $brand_color ?? '#f45f00' ); ?>;">
			<a href="<?php echo esc_url( $profile_url ?? ( $site_url ?? '#' ) ); ?>" class="email-btn" style="display: inline-block; padding: 14px 32px; color: #ffffff; text-decoration: none; font-weight: 600; font-size: 16px; background: <?php echo esc_attr( $brand_color ?? '#f45f00' ); ?>; border-radius: 8px;">
				Acessar meu Perfil
			</a>
		</td>
	</tr>
</table>

<br>

<p style="font-size: 14px; color: #666;">Seu username: <strong style="color: #121214;">@<?php echo esc_html( $username ?? '' ); ?></strong></p>
