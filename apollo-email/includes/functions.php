<?php

/**
 * Apollo Email — Global Helper Functions.
 *
 * These functions provide a clean API for sending emails
 * from any Apollo plugin without directly coupling to classes.
 *
 * @package Apollo\Email
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Send an email immediately using a template.
 *
 * @param string $to       Recipient email.
 * @param string $subject  Email subject (supports {{merge_tags}}).
 * @param string $template Template slug (e.g., 'welcome', 'password-reset').
 * @param array  $data     Merge tag data.
 * @return bool True on success.
 */
function apollo_send_email( string $to, string $subject, string $template, array $data = array() ): bool {
	if ( ! class_exists( 'Apollo\\Email\\Plugin' ) ) {
		// Fallback to wp_mail if plugin not loaded
		return wp_mail( $to, $subject, $data['message'] ?? '' );
	}

	$plugin = \Apollo\Email\Plugin::instance();
	$result = $plugin->sender()->sendTemplate( $to, $subject, $template, $data );

	return $result['success'] ?? false;
}

/**
 * Queue an email for later sending.
 *
 * @param string $to       Recipient email.
 * @param string $subject  Email subject.
 * @param string $template Template slug.
 * @param array  $data     Merge tag data.
 * @param int    $priority Priority (1=highest, 10=lowest).
 * @return int|false Queue ID or false on failure.
 */
function apollo_queue_email( string $to, string $subject, string $template, array $data = array(), int $priority = 5 ): int|false {
	if ( ! class_exists( 'Apollo\\Email\\Plugin' ) ) {
		// Fallback: send immediately
		apollo_send_email( $to, $subject, $template, $data );
		return false;
	}

	$plugin = \Apollo\Email\Plugin::instance();
	return $plugin->queue()->enqueue( $to, $subject, '', $template, $data, $priority );
}

/**
 * Process the email queue (used by cron).
 *
 * @param int $batch_size Number of emails to process.
 */
function apollo_process_email_queue( int $batch_size = 50 ): void {
	if ( ! class_exists( 'Apollo\\Email\\Plugin' ) ) {
		return;
	}

	\Apollo\Email\Plugin::instance()->queue()->processNext();
}

/**
 * Get an email template by slug.
 *
 * @param string $template_slug Template slug.
 * @return array|null Template data or null.
 */
function apollo_get_email_template( string $template_slug ): ?array {
	if ( ! class_exists( 'Apollo\\Email\\Plugin' ) ) {
		return null;
	}

	return \Apollo\Email\Plugin::instance()->templates()->getTemplate( $template_slug );
}

/**
 * Render an email template with data.
 *
 * @param string $template Template slug.
 * @param array  $data     Merge tag data.
 * @return string Rendered HTML.
 */
function apollo_render_email( string $template, array $data = array() ): string {
	if ( ! class_exists( 'Apollo\\Email\\Plugin' ) ) {
		return '';
	}

	return \Apollo\Email\Plugin::instance()->templates()->render( $template, $data );
}
