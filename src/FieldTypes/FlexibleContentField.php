<?php
/**
 * ACF Flexible Content container.
 *
 * @package HushUX\ElementorAcfGroupFields
 */

declare(strict_types=1);

namespace HushUX\ElementorAcfGroupFields\FieldTypes;

final class FlexibleContentField extends AbstractFieldType {

	public function types(): array {
		return array( 'flexible_content' );
	}

	public function is_container(): bool {
		return true;
	}

	public function layouts( array $field ): array {
		$layouts = $field['layouts'] ?? array();
		$map     = array();

		if ( ! is_array( $layouts ) ) {
			return $map;
		}

		foreach ( $layouts as $layout ) {
			if ( ! is_array( $layout ) ) {
				continue;
			}
			$name = (string) ( $layout['name'] ?? '' );
			if ( $name === '' ) {
				continue;
			}
			$map[ $name ] = (string) ( $layout['label'] ?? $name );
		}

		return $map;
	}

	/**
	 * Flatten all layout sub-fields; RecursiveWalker annotates layout context.
	 *
	 * @param array<string, mixed> $field
	 * @return array<int, array<string, mixed>>
	 */
	public function children( array $field ): array {
		$layouts  = $field['layouts'] ?? array();
		$children = array();

		if ( ! is_array( $layouts ) ) {
			return $children;
		}

		foreach ( $layouts as $layout ) {
			if ( ! is_array( $layout ) ) {
				continue;
			}

			$subs = $layout['sub_fields'] ?? array();
			if ( ! is_array( $subs ) ) {
				continue;
			}

			foreach ( $subs as $sub ) {
				if ( ! is_array( $sub ) ) {
					continue;
				}
				$sub['_hux_flex_layout']       = (string) ( $layout['name'] ?? '' );
				$sub['_hux_flex_layout_label'] = (string) ( $layout['label'] ?? $layout['name'] ?? '' );
				$children[]                    = $sub;
			}
		}

		return $children;
	}
}
