<?php

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
	// Host/environment constants override.
	// Any key here overrides apollo-core/config/constants.php.
	'APOLLO_CDN_URL'     => 'https://cdn.apollo.rio.br/v1.0.0/',
	'APOLLO_CDN_CORE_JS' => 'https://cdn.apollo.rio.br/v1.0.0/core.min.js?v=1.0.0',
);
