<?php
/**
 * Lightweight PSR-4 autoloader used when Composer vendor/ is absent.
 *
 * @package HushUX\ElementorAcfGroupFields
 */

declare(strict_types=1);

namespace HushUX\ElementorAcfGroupFields;

final class Autoloader {

	/**
	 * Register a PSR-4 namespace prefix.
	 *
	 * @param string $prefix  Namespace prefix with trailing backslash.
	 * @param string $base_dir Absolute directory with trailing slash.
	 */
	public static function register( string $prefix, string $base_dir ): void {
		$prefix   = rtrim( $prefix, '\\' ) . '\\';
		$base_dir = rtrim( $base_dir, '/\\' ) . DIRECTORY_SEPARATOR;

		spl_autoload_register(
			static function ( string $class ) use ( $prefix, $base_dir ): void {
				if ( strncmp( $prefix, $class, strlen( $prefix ) ) !== 0 ) {
					return;
				}

				$relative = substr( $class, strlen( $prefix ) );
				$file     = $base_dir . str_replace( '\\', DIRECTORY_SEPARATOR, $relative ) . '.php';

				if ( is_readable( $file ) ) {
					require_once $file;
				}
			}
		);
	}
}
