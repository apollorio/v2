<?php
/**
 * Constantes do Apollo DJs
 *
 * @package Apollo\DJs
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ─── REST ────────────────────────────────────────────────────────────────────
define( 'APOLLO_DJ_REST_NAMESPACE', 'apollo/v1' );

// ─── CPT ─────────────────────────────────────────────────────────────────────
define( 'APOLLO_DJ_CPT', 'dj' );

// ─── Taxonomies (GLOBAL BRIDGE via apollo-core) ─────────────────────────────
// sound taxonomy é compartilhada com apollo-events
define( 'APOLLO_DJ_TAX_SOUND', 'sound' );

// ─── Style ───────────────────────────────────────────────────────────────────
define( 'APOLLO_DJ_DEFAULT_STYLE', 'apollo-v1' );

// ─── Cache ───────────────────────────────────────────────────────────────────
define( 'APOLLO_DJ_CACHE_GROUP', 'apollo_djs' );
define( 'APOLLO_DJ_CACHE_TTL', 300 );

// ─── Meta Keys — conforme apollo-registry.json ──────────────────────────────
define(
	'APOLLO_DJ_META_KEYS',
	array(
		'_dj_image',
		'_dj_banner',
		'_dj_website',
		'_dj_instagram',
		'_dj_soundcloud',
		'_dj_spotify',
		'_dj_youtube',
		'_dj_mixcloud',
		'_dj_user_id',
		'_dj_verified',
		'_dj_bio_short',
		'_dj_name',
		'_dj_bio',
		'_dj_facebook',
		'_dj_bandcamp',
		'_dj_beatport',
		'_dj_resident_advisor',
		'_dj_twitter',
		'_dj_tiktok',
		'_dj_original_project_1',
		'_dj_original_project_2',
		'_dj_original_project_3',
		'_dj_set_url',
		'_dj_media_kit_url',
		'_dj_rider_url',
		'_dj_mix_url',
	)
);
