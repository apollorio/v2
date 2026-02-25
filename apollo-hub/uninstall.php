<?php
/**
 * Uninstall — Apollo Hub
 *
 * Executado apenas quando o usuário APAGA o plugin via admin WP.
 * Remove: posts do CPT hub, postmeta, opções do plugin.
 *
 * @package Apollo\Hub
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

// ── Remove todos os posts do CPT 'hub' e seus metadados ──────────────────────

$hub_ids = $wpdb->get_col(
	$wpdb->prepare(
		"SELECT ID FROM {$wpdb->posts} WHERE post_type = %s",
		'hub'
	)
);

if ( $hub_ids ) {
	$ids_placeholder = implode( ',', array_map( 'absint', $hub_ids ) );

	// Postmeta dos hubs
	$wpdb->query(
		"DELETE FROM {$wpdb->postmeta} WHERE post_id IN ($ids_placeholder)"
	);

	// Posts
	$wpdb->query(
		"DELETE FROM {$wpdb->posts} WHERE post_type = 'hub'"
	);
}

// ── Remove opções do plugin ───────────────────────────────────────────────────

delete_option( 'apollo_hub_version' );
delete_option( 'apollo_hub_flush_rewrite' );

// ── Remove transients de cache ────────────────────────────────────────────────

$wpdb->query(
	"DELETE FROM {$wpdb->options}
     WHERE option_name LIKE '_transient_apollo_hub_%'
        OR option_name LIKE '_transient_timeout_apollo_hub_%'"
);

// ── Limpa rewrite rules ───────────────────────────────────────────────────────

flush_rewrite_rules();
