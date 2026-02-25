<?php

/**
 * Base email template wrapper.
 *
 * All emails are wrapped in this responsive HTML shell.
 * Variables available: $email_content, $brand_color, $brand_logo,
 * $site_name, $site_url, $footer_text, $footer_address,
 * $current_year, $unsubscribe_url, $preferences_url
 *
 * @package Apollo\Email
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="x-apple-disable-message-reformatting">
	<title><?php echo esc_html( $site_name ?? 'Apollo Rio' ); ?></title>
	<!--[if mso]>
	<noscript>
		<xml>
			<o:OfficeDocumentSettings>
				<o:PixelsPerInch>96</o:PixelsPerInch>
			</o:OfficeDocumentSettings>
		</xml>
	</noscript>
	<![endif]-->
	<style>
		/* Reset */
		body,
		table,
		td,
		p,
		a,
		li,
		blockquote {
			-webkit-text-size-adjust: 100%;
			-ms-text-size-adjust: 100%;
		}

		table,
		td {
			mso-table-lspace: 0pt;
			mso-table-rspace: 0pt;
		}

		img {
			-ms-interpolation-mode: bicubic;
			border: 0;
			height: auto;
			line-height: 100%;
			outline: none;
			text-decoration: none;
		}

		body {
			height: 100% !important;
			margin: 0 !important;
			padding: 0 !important;
			width: 100% !important;
		}

		a[x-apple-data-detectors] {
			color: inherit !important;
			text-decoration: none !important;
			font-size: inherit !important;
			font-family: inherit !important;
			font-weight: inherit !important;
			line-height: inherit !important;
		}

		/* Apollo Email Styles — V2 */
		.email-body {
			background-color: #f4f4f5;
		}

		.email-container {
			max-width: 600px;
			margin: 0 auto;
			background-color: #ffffff;
			border-radius: 16px;
			overflow: hidden;
			border: 1px solid #e4e4e7;
		}

		.email-header {
			padding: 32px 40px;
			text-align: center;
			background: <?php echo esc_attr( $brand_color ?? '#121214' ); ?>;
		}

		.email-header img {
			max-width: 180px;
			height: auto;
		}

		.email-header h1 {
			color: #ffffff;
			font-size: 24px;
			font-weight: 700;
			margin: 16px 0 0;
			font-family: 'Space Grotesk', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
		}

		.email-content {
			padding: 40px;
			color: #121214;
			font-family: 'Space Grotesk', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
			font-size: 16px;
			line-height: 1.6;
		}

		.email-content h2 {
			color: #121214;
			font-size: 20px;
			margin-bottom: 16px;
		}

		.email-content p {
			margin: 0 0 16px;
		}

		.email-content a {
			color: <?php echo esc_attr( $brand_color ?? '#f45f00' ); ?>;
			text-decoration: underline;
		}

		.email-btn {
			display: inline-block;
			padding: 14px 32px;
			background: <?php echo esc_attr( $brand_color ?? '#f45f00' ); ?>;
			color: #ffffff !important;
			text-decoration: none !important;
			border-radius: 8px;
			font-weight: 600;
			font-size: 16px;
			text-align: center;
		}

		.email-btn:hover {
			opacity: 0.9;
		}

		.email-divider {
			border: 0;
			border-top: 1px solid #e4e4e7;
			margin: 24px 0;
		}

		.email-footer {
			padding: 24px 40px;
			background-color: #f4f4f5;
			text-align: center;
			font-size: 13px;
			color: #666;
			font-family: 'Space Grotesk', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
		}

		.email-footer a {
			color: <?php echo esc_attr( $brand_color ?? '#f45f00' ); ?>;
			text-decoration: none;
		}

		.email-footer p {
			margin: 4px 0;
		}

		/* Dark mode tweaks for clients that support it */
		@media (prefers-color-scheme: dark) {
			.email-body {
				background-color: #121214 !important;
			}

			.email-container {
				background-color: #1e1e22 !important;
				border-color: #27272a !important;
			}

			.email-content {
				color: #e4e4e7 !important;
			}

			.email-content h2 {
				color: #e4e4e7 !important;
			}

			.email-footer {
				background-color: #121214 !important;
				color: #71717a !important;
			}
		}

		/* Responsive */
		@media screen and (max-width: 620px) {
			.email-container {
				width: 100% !important;
				border-radius: 0 !important;
			}

			.email-header,
			.email-content,
			.email-footer {
				padding-left: 20px !important;
				padding-right: 20px !important;
			}
		}
	</style>
</head>

<body class="email-body" style="margin: 0; padding: 0; background-color: #f4f4f5;">
	<table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" class="email-body" style="background-color: #f4f4f5;">
		<tr>
			<td align="center" style="padding: 24px 16px;">
				<!-- Email Container -->
				<table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" class="email-container" style="max-width: 600px; background-color: #ffffff; border-radius: 16px; overflow: hidden; border: 1px solid #e4e4e7;">
					<!-- Header -->
					<tr>
						<td class="email-header" style="padding: 32px 40px; text-align: center; background: <?php echo esc_attr( $brand_color ?? '#121214' ); ?>;">
							<?php if ( ! empty( $brand_logo ) ) : ?>
								<img src="<?php echo esc_url( $brand_logo ); ?>" alt="<?php echo esc_attr( $site_name ?? 'Apollo' ); ?>" style="max-width: 180px; height: auto;">
							<?php else : ?>
								<h1 style="color: #ffffff; font-size: 28px; font-weight: 700; margin: 0; font-family: 'Space Grotesk', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; letter-spacing: -0.5px;">
									<?php echo esc_html( $site_name ?? 'Apollo Rio' ); ?>
								</h1>
							<?php endif; ?>
						</td>
					</tr>

					<!-- Content -->
					<tr>
						<td class="email-content" style="padding: 40px; color: #121214; font-family: 'Space Grotesk', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 16px; line-height: 1.6;">
							<?php echo wp_kses_post( $email_content ?? '' ); ?>
						</td>
					</tr>

					<!-- Footer -->
					<tr>
						<td class="email-footer" style="padding: 24px 40px; background-color: #f4f4f5; text-align: center; font-size: 13px; color: #666; font-family: 'Space Grotesk', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
							<?php if ( ! empty( $footer_text ) ) : ?>
								<p style="margin: 4px 0;"><?php echo wp_kses_post( $footer_text ); ?></p>
							<?php else : ?>
								<p style="margin: 4px 0;">&copy; <?php echo esc_html( $current_year ?? gmdate( 'Y' ) ); ?> <?php echo esc_html( $site_name ?? 'Apollo Rio' ); ?>. Todos os direitos reservados.</p>
							<?php endif; ?>

							<?php if ( ! empty( $footer_address ) ) : ?>
								<p style="margin: 4px 0; color: #666;"><?php echo esc_html( $footer_address ); ?></p>
							<?php endif; ?>

							<p style="margin: 12px 0 4px;">
								<?php if ( ! empty( $preferences_url ) ) : ?>
									<a href="<?php echo esc_url( $preferences_url ); ?>" style="color: <?php echo esc_attr( $brand_color ?? '#f45f00' ); ?>; text-decoration: none;">Preferências</a>
								<?php endif; ?>
								<?php if ( ! empty( $unsubscribe_url ) ) : ?>
									&nbsp;&bull;&nbsp;
									<a href="<?php echo esc_url( $unsubscribe_url ); ?>" style="color: <?php echo esc_attr( $brand_color ?? '#f45f00' ); ?>; text-decoration: none;">Cancelar inscrição</a>
								<?php endif; ?>
							</p>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</body>

</html>
