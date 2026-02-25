<?php

/**
 * Weekly digest email template content block.
 *
 * Variables: $user_name, $notifications, $digest_title, $digest_intro, $digest_sections, $site_name, $site_url
 *
 * @package Apollo\Email
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$notifications   = $notifications ?? array();
$digest_title    = $digest_title ?? __( 'Seu Resumo Semanal', 'apollo-email' );
$digest_intro    = $digest_intro ?? '';
$digest_sections = $digest_sections ?? array();

if ( empty( $digest_sections ) && ! empty( $notifications ) ) {
	$legacy_items = array();
	foreach ( $notifications as $notif ) {
		if ( is_array( $notif ) ) {
			$legacy_items[] = array(
				'heading' => (string) ( $notif['title'] ?? '' ),
				'message' => (string) ( $notif['message'] ?? '' ),
				'time'    => (string) ( $notif['time'] ?? '' ),
				'url'     => (string) ( $notif['url'] ?? '' ),
			);
			continue;
		}

		$legacy_items[] = array(
			'heading' => '',
			'message' => (string) $notif,
			'time'    => '',
			'url'     => '',
		);
	}

	$digest_sections = array(
		array(
			'title' => __( 'Notificações', 'apollo-email' ),
			'items' => $legacy_items,
		),
	);
}
?>
<h2 style="color: #121214; font-size: 22px; margin: 0 0 16px;"><?php echo esc_html( $digest_title ); ?> &#128240;</h2>

<p>Olá, <?php echo esc_html( $user_name ?? 'usuário(a)' ); ?>!</p>

<p>Veja o que aconteceu no <?php echo esc_html( $site_name ?? 'Apollo Rio' ); ?>:</p>

<?php if ( ! empty( $digest_intro ) ) : ?>
	<p><?php echo esc_html( $digest_intro ); ?></p>
<?php endif; ?>

<hr style="border: 0; border-top: 1px solid #e4e4e7; margin: 24px 0;">

<?php if ( ! empty( $digest_sections ) ) : ?>
	<?php foreach ( $digest_sections as $section ) : ?>
		<?php $items = is_array( $section['items'] ?? null ) ? $section['items'] : array(); ?>
		<?php if ( empty( $items ) ) : ?>
			<?php continue; ?>
		<?php endif; ?>

		<h3 style="font-size:16px; color:#121214; margin: 0 0 10px;">
			<?php echo esc_html( $section['title'] ?? __( 'Atualizações', 'apollo-email' ) ); ?>
		</h3>

		<table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-bottom:16px;">
			<?php foreach ( array_slice( $items, 0, 10 ) as $row ) : ?>
				<tr>
					<td style="padding: 12px 0; border-bottom: 1px solid #e4e4e7;">
						<?php if ( ! empty( $row['heading'] ) ) : ?>
							<p style="margin: 0 0 4px; color: #121214; font-weight: 600;">
								<?php echo esc_html( $row['heading'] ); ?>
							</p>
						<?php endif; ?>
						<p style="margin: 0; color: #121214;">
							<?php echo wp_kses_post( $row['message'] ?? '' ); ?>
						</p>
						<?php if ( ! empty( $row['time'] ) ) : ?>
							<p style="margin: 4px 0 0; font-size: 12px; color: #666;">
								<?php echo esc_html( $row['time'] ); ?>
							</p>
						<?php endif; ?>
						<?php if ( ! empty( $row['url'] ) ) : ?>
							<p style="margin: 6px 0 0;">
								<a href="<?php echo esc_url( $row['url'] ); ?>" style="color:#f45f00;text-decoration:none;">
									<?php esc_html_e( 'Abrir atualização', 'apollo-email' ); ?>
								</a>
							</p>
						<?php endif; ?>
					</td>
				</tr>
			<?php endforeach; ?>
		</table>
	<?php endforeach; ?>
<?php else : ?>
	<p style="color: #666;">Nenhuma novidade no momento. Explore novos eventos!</p>
<?php endif; ?>

<br>

<table role="presentation" cellspacing="0" cellpadding="0" border="0" align="center">
	<tr>
		<td style="border-radius: 8px; background: <?php echo esc_attr( $brand_color ?? '#f45f00' ); ?>;">
			<a href="<?php echo esc_url( $site_url ?? '#' ); ?>" class="email-btn" style="display: inline-block; padding: 14px 32px; color: #ffffff; text-decoration: none; font-weight: 600; font-size: 16px;">
				Explorar Apollo Rio
			</a>
		</td>
	</tr>
</table>
