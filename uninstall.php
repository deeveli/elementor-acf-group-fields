<?php
/**
 * Uninstall cleanup — this plugin stores no options; reserved for future use.
 *
 * @package HushUX\ElementorAcfGroupFields
 */

declare(strict_types=1);

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

wp_cache_delete( 'discovered_v2', 'hux_eagf' );
