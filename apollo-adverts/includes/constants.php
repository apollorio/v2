<?php
/**
 * Plugin Constants
 *
 * All constants for the Apollo Adverts plugin.
 * Adapted from WPAdverts constants + apollo-registry.json spec.
 *
 * @package Apollo\Adverts
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ═══════════════════════════════════════════════════════════════════════════
// REST API
// ═══════════════════════════════════════════════════════════════════════════

define( 'APOLLO_ADVERTS_REST_NAMESPACE', 'apollo/v1' );

// ═══════════════════════════════════════════════════════════════════════════
// CPT & TAXONOMY — from apollo-registry.json
// ═══════════════════════════════════════════════════════════════════════════

if ( ! defined( 'APOLLO_CPT_CLASSIFIED' ) ) {
	define( 'APOLLO_CPT_CLASSIFIED', 'classified' );
}

define( 'APOLLO_TAX_CLASSIFIED_DOMAIN', 'classified_domain' );
define( 'APOLLO_TAX_CLASSIFIED_INTENT', 'classified_intent' );

// ═══════════════════════════════════════════════════════════════════════════
// META KEYS — from apollo-registry.json
// ═══════════════════════════════════════════════════════════════════════════

define(
	'APOLLO_ADVERTS_META_KEYS',
	array(
		'_classified_price'            => array(
			'type'    => 'float',
			'default' => 0,
		),
		'_classified_currency'         => array(
			'type'    => 'string',
			'default' => 'BRL',
		),
		'_classified_negotiable'       => array(
			'type'    => 'bool',
			'default' => false,
		),
		'_classified_condition'        => array(
			'type'    => 'string',
			'default' => 'usado',
			'values'  => array( 'novo', 'usado', 'recondicionado' ),
		),
		'_classified_location'         => array(
			'type'    => 'string',
			'default' => '',
		),
		'_classified_contact_phone'    => array(
			'type'    => 'string',
			'default' => '',
		),
		'_classified_contact_whatsapp' => array(
			'type'    => 'string',
			'default' => '',
		),
		'_classified_expires_at'       => array(
			'type'    => 'string',
			'default' => '',
		),
		'_classified_featured'         => array(
			'type'    => 'bool',
			'default' => false,
		),
		'_classified_views'            => array(
			'type'    => 'int',
			'default' => 0,
		),
	)
);

// ═══════════════════════════════════════════════════════════════════════════
// CLASSIFIED INTENTS — from apollo-registry.json
// ═══════════════════════════════════════════════════════════════════════════

define(
	'APOLLO_ADVERTS_INTENTS',
	array(
		'vendo'   => 'Vendo',
		'compro'  => 'Compro',
		'troco'   => 'Troco',
		'alugo'   => 'Alugo',
		'procuro' => 'Procuro',
	)
);

// ═══════════════════════════════════════════════════════════════════════════
// CONDITIONS
// ═══════════════════════════════════════════════════════════════════════════

define(
	'APOLLO_ADVERTS_CONDITIONS',
	array(
		'novo'           => 'Novo',
		'usado'          => 'Usado',
		'recondicionado' => 'Recondicionado',
	)
);

// ═══════════════════════════════════════════════════════════════════════════
// DEFAULTS
// ═══════════════════════════════════════════════════════════════════════════

define( 'APOLLO_ADVERTS_DEFAULT_EXPIRATION', 30 ); // days
define( 'APOLLO_ADVERTS_MAX_IMAGES', 8 );
define( 'APOLLO_ADVERTS_POSTS_PER_PAGE', 12 );

// ═══════════════════════════════════════════════════════════════════════════
// CURRENCY — Valor de referência apenas (informativo)
// Apollo NÃO processa pagamentos. Somos ponte de conexão entre pessoas.
// A negociação e transação final acontecem fora da plataforma.
// ═══════════════════════════════════════════════════════════════════════════

define(
	'APOLLO_ADVERTS_CURRENCY',
	array(
		'code'          => 'BRL',
		'sign'          => 'R$',
		'sign_type'     => 'p', // p=prefix, s=suffix
		'decimals'      => 2,
		'char_decimal'  => ',',
		'char_thousand' => '.',
	)
);

// ═══════════════════════════════════════════════════════════════════════════
// IMAGE SIZES
// ═══════════════════════════════════════════════════════════════════════════

define(
	'APOLLO_ADVERTS_IMAGE_SIZES',
	array(
		'classified-list'    => array(
			'width'  => 310,
			'height' => 310,
			'crop'   => true,
		),
		'classified-gallery' => array(
			'width'  => 650,
			'height' => 400,
			'crop'   => false,
		),
		'classified-thumb'   => array(
			'width'  => 150,
			'height' => 105,
			'crop'   => true,
		),
	)
);
