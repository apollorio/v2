<?php
/**
 * Comunas Directory — /comunas
 *
 * Shows only comunas (public communities).
 * Wrapper that loads groups-directory.php with $page_type = 'comunas'.
 *
 * Registry: { slug: "comunas", template: "comunas.php", type: "virtual" }
 *
 * @package Apollo\Groups
 * @since   3.0.0
 */

defined( 'ABSPATH' ) || exit;

// Force "comunas" context
$page_type = 'comunas';

require __DIR__ . '/groups-directory.php';
