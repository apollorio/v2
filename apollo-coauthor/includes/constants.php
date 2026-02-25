<?php
/**
 * Apollo CoAuthor — Constants.
 *
 * @package Apollo\CoAuthor
 * @since   1.0.0
 */

declare(strict_types=1);

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ── REST ─────────────────────────────────────────────────────────── */
define( 'APOLLO_COAUTHOR_REST_NAMESPACE', 'apollo/v1' );

/* ── Taxonomy ─────────────────────────────────────────────────────── */
define( 'APOLLO_COAUTHOR_TAX', 'coauthor' );

/* ── Supported Post Types ─────────────────────────────────────────── */
define(
	'APOLLO_COAUTHOR_POST_TYPES',
	array( 'event', 'dj', 'classified', 'doc', 'loc', 'post' )
);

/* ── Meta Keys ────────────────────────────────────────────────────── */
define( 'APOLLO_COAUTHOR_META_KEY', '_coauthors' );

/* ── Cache ─────────────────────────────────────────────────────────── */
define( 'APOLLO_COAUTHOR_CACHE_GROUP', 'apollo_coauthor' );
