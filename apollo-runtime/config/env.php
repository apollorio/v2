<?php
declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
	'environment' => wp_get_environment_type(),
	'debug'       => defined( 'WP_DEBUG' ) && WP_DEBUG,
);
