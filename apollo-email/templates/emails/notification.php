<?php

/**
 * Generic notification email template content block.
 *
 * Variables: $user_name, $title, $message, $action_url, $action_text, $site_name
 *
 * @package Apollo\Email
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<h2 style="color: #121214; font-size: 22px; margin: 0 0 16px;"><?php echo esc_html( $title ?? 'Notificação' ); ?></h2>

<p>Olá, <?php echo esc_html( $user_name ?? 'usuário(a)' ); ?>!</p>

<p><?php echo wp_kses_post( $message ?? '' ); ?></p>

<?php if ( ! empty( $action_url ) ) : ?>
	<br>

	<table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center">
		<tr>
			<td style="border-radius: 8px; background: <?php echo esc_attr( $brand_color ?? '#f45f00' ); ?>;">
				<a href="<?php echo esc_url( $action_url ); ?>" class="email-btn" style="display: inline-block; padding: 14px 32px; color: #ffffff; text-decoration: none; font-weight: 600; font-size: 16px;">
					<?php echo esc_html( $action_text ?? 'Ver mais' ); ?>
				</a>
			</td>
		</tr>
	</table>
<?php endif; ?>
