<?php

/**
 * Report / Contact Form email template content block.
 *
 * Variables: $name, $email, $subject, $message
 *
 * @package Apollo\Email
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$name    = $name ?? 'Anônimo';
$email   = $email ?? 'não informado';
$subject = $subject ?? 'Contato';
$message = $message ?? '';
?>
<h2 style="color: #121214; font-size: 22px; margin: 0 0 16px;">📬 Nova Mensagem de Contato</h2>

<table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: 20px 0;">
	<tr>
		<td style="padding: 12px 16px; background: #f8f8f8; border-left: 3px solid #f45f00;">
			<strong style="color: #666; font-size: 12px; text-transform: uppercase; letter-spacing: 0.05em;">Assunto</strong><br>
			<span style="color: #121214; font-size: 18px; font-weight: 600;"><?php echo esc_html( $subject ); ?></span>
		</td>
	</tr>
</table>

<table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: 20px 0; border: 1px solid #e0e0e0;">
	<tr>
		<td style="padding: 16px; background: #fafafa; width: 50%; border-right: 1px solid #e0e0e0;">
			<strong style="color: #666; font-size: 11px; text-transform: uppercase;">Nome</strong><br>
			<span style="color: #121214;"><?php echo esc_html( $name ); ?></span>
		</td>
		<td style="padding: 16px; background: #fafafa;">
			<strong style="color: #666; font-size: 11px; text-transform: uppercase;">E-mail</strong><br>
			<a href="mailto:<?php echo esc_attr( $email ); ?>" style="color: #f45f00;"><?php echo esc_html( $email ); ?></a>
		</td>
	</tr>
</table>

<div style="padding: 20px; background: #ffffff; border: 1px solid #e0e0e0; border-radius: 4px; margin: 20px 0;">
	<strong style="color: #666; font-size: 11px; text-transform: uppercase; display: block; margin-bottom: 12px;">Mensagem</strong>
	<div style="color: #333; line-height: 1.6; white-space: pre-wrap;"><?php echo wp_kses_post( nl2br( $message ) ); ?></div>
</div>

<p style="color: #999; font-size: 12px; margin-top: 30px; padding-top: 20px; border-top: 1px solid #e0e0e0;">
	<em>Esta mensagem foi enviada via formulário de contato Apollo.</em>
</p>
