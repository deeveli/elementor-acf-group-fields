<?php
/**
 * Shared utility helpers.
 *
 * @package HushUX\ElementorAcfGroupFields
 */

declare(strict_types=1);

namespace HushUX\ElementorAcfGroupFields\Helpers;

final class Utils {

	/**
	 * Print a dismissible admin warning.
	 */
	public static function admin_notice( string $message ): void {
		printf(
			'<div class="notice notice-warning is-dismissible"><p>%s</p></div>',
			wp_kses_post( $message )
		);
	}

	/**
	 * Join ACF nested field names with underscores (ACF meta key convention).
	 *
	 * @param list<string> $segments
	 */
	public static function join_meta_path( array $segments ): string {
		$segments = array_values(
			array_filter(
				$segments,
				static fn( $segment ): bool => is_string( $segment ) && $segment !== '' && $segment !== '{row}'
			)
		);

		return implode( '_', $segments );
	}

	/**
	 * Human-readable breadcrumb for select option labels.
	 *
	 * @param list<string> $labels
	 */
	public static function join_label_path( array $labels ): string {
		$labels = array_values(
			array_filter(
				$labels,
				static fn( $label ): bool => is_string( $label ) && $label !== ''
			)
		);

		return implode( ' → ', $labels );
	}

	/**
	 * Build Elementor control option key: `{field_key}:{meta_path}[|{property}]`.
	 */
	public static function build_option_key( string $field_key, string $meta_path, string $property = '' ): string {
		$key = $field_key . ':' . $meta_path;

		if ( $property !== '' ) {
			$key .= '|' . $property;
		}

		return $key;
	}

	/**
	 * Parse an option key into field key, meta path, and optional property.
	 *
	 * @return array{field_key: string, meta_key: string, property: string}
	 */
	public static function parse_option_key( string $key ): array {
		$property = '';

		if ( str_contains( $key, '|' ) ) {
			[ $key, $property ] = explode( '|', $key, 2 );
		}

		$field_key = '';
		$meta_key  = $key;

		if ( str_contains( $key, ':' ) ) {
			[ $field_key, $meta_key ] = explode( ':', $key, 2 );
		}

		return array(
			'field_key' => $field_key,
			'meta_key'  => $meta_key,
			'property'  => $property,
		);
	}

	/**
	 * Safely cast mixed values to a printable string.
	 *
	 * @param mixed $value
	 */
	public static function stringify( mixed $value ): string {
		if ( $value === null || $value === false ) {
			return '';
		}

		if ( is_bool( $value ) ) {
			return $value ? '1' : '0';
		}

		if ( is_scalar( $value ) ) {
			return (string) $value;
		}

		if ( is_array( $value ) ) {
			$flat = array();

			foreach ( $value as $item ) {
				if ( is_scalar( $item ) ) {
					$flat[] = (string) $item;
				} elseif ( is_array( $item ) && isset( $item['label'] ) ) {
					$flat[] = (string) $item['label'];
				} elseif ( is_array( $item ) && isset( $item['value'] ) ) {
					$flat[] = (string) $item['value'];
				} elseif ( is_object( $item ) && isset( $item->post_title ) ) {
					$flat[] = (string) $item->post_title;
				} elseif ( is_object( $item ) && isset( $item->name ) ) {
					$flat[] = (string) $item->name;
				}
			}

			return implode( ', ', $flat );
		}

		if ( is_object( $value ) ) {
			if ( isset( $value->post_title ) ) {
				return (string) $value->post_title;
			}

			if ( isset( $value->display_name ) ) {
				return (string) $value->display_name;
			}

			if ( isset( $value->name ) ) {
				return (string) $value->name;
			}
		}

		return '';
	}
}
