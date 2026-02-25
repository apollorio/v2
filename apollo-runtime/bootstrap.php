<?php
/**
 * Apollo Runtime Bootstrap
 *
 * Host bootstrap order:
 * 1) Wait for apollo-core initialization
 * 2) Load runtime module bootstraps
 * 3) Fire runtime initialized hook
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'apollo/core/initialized',
	function ( array $info ): void {
		$module_files = glob( APOLLO_RUNTIME_PATH . 'modules/*/bootstrap.php' );

		if ( is_array( $module_files ) ) {
			foreach ( $module_files as $file ) {
				if ( is_readable( $file ) ) {
					require_once $file;
				}
			}
		}

		do_action(
			'apollo/runtime/initialized',
			array(
				'runtime_version' => APOLLO_RUNTIME_VERSION,
				'core_info'       => $info,
				'module_count'    => is_array( $module_files ) ? count( $module_files ) : 0,
			)
		);
	},
	20
);
