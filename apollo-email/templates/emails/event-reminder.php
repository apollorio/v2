<?php

/**
 * Event reminder email template content block.
 *
 * Variables: $user_name, $event_title, $event_url, $event_date, $event_time, $loc_name, $site_name
 *
 * @package Apollo\Email
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<h2 style="color: #121214; font-size: 22px; margin: 0 0 16px;">Lembrete de Evento &#127926;</h2>

<p>Olá, <?php echo esc_html( $user_name ?? 'usuário(a)' ); ?>!</p>

<p>O evento que você marcou está chegando:</p>

<table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f4f4f5; border-radius: 8px; margin: 16px 0;">
	<tr>
		<td style="padding: 20px;">
			<h3 style="color: #121214; margin: 0 0 8px; font-size: 18px;">
				<?php echo esc_html( $event_title ?? 'Evento' ); ?>
			</h3>
			<?php if ( ! empty( $event_date ) ) : ?>
				<p style="margin: 4px 0; color: #71717a;">
					&#128197; <?php echo esc_html( $event_date ); ?>
					<?php if ( ! empty( $event_time ) ) : ?>
						&nbsp;&bull;&nbsp; &#128336; <?php echo esc_html( $event_time ); ?>
					<?php endif; ?>
				</p>
			<?php endif; ?>
			<?php if ( ! empty( $loc_name ) ) : ?>
				<p style="margin: 4px 0; color: #71717a;">
					&#128205; <?php echo esc_html( $loc_name ); ?>
				</p>
			<?php endif; ?>
		</td>
	</tr>
</table>

<br>

<table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center">
	<tr>
		<td style="border-radius: 8px; background: <?php echo esc_attr( $brand_color ?? '#f45f00' ); ?>;">
			<a href="<?php echo esc_url( $event_url ?? '#' ); ?>" class="email-btn" style="display: inline-block; padding: 14px 32px; color: #ffffff; text-decoration: none; font-weight: 600; font-size: 16px;">
				Ver Evento
			</a>
		</td>
	</tr>
</table>
