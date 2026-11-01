<?php
/**
 * Resolves ACF values for nested meta keys, including repeater row selection.
 *
 * @package HushUX\ElementorAcfGroupFields
 */

declare(strict_types=1);

namespace HushUX\ElementorAcfGroupFields\Services;

use HushUX\ElementorAcfGroupFields\Helpers\ACF;
use HushUX\ElementorAcfGroupFields\Helpers\Elementor;
use HushUX\ElementorAcfGroupFields\Helpers\Utils;

final class FieldResolver {

	/**
	 * Resolve field object + raw value for a dynamic tag.
	 *
	 * @param \Elementor\Core\DynamicTags\Base_Tag $tag
	 * @return array{field: ?array<string, mixed>, value: mixed, meta_key: string, property: string}
	 */
	public function resolve( $tag ): array {
		$raw_key  = (string) $tag->get_settings( 'key' );
		$parsed   = Utils::parse_option_key( $raw_key );
		$meta_key = $parsed['meta_key'];
		$property = $parsed['property'];
		$row      = (int) $tag->get_settings( 'repeater_row' );

		$field = null;
		$value = null;

		// Prefer Elementor Pro helper for base keys (backwards compatible behaviour).
		[ $el_field, $el_meta ] = Elementor::get_tag_value_field( $tag );

		if ( is_array( $el_field ) ) {
			$field = $el_field;
			$value = $el_field['value'] ?? null;
		}

		if ( $meta_key === '' && is_string( $el_meta ) ) {
			$meta_key = $el_meta;
		}

		$object_id = ACF::get_current_object_id();

		// Nested / repeater-aware resolution.
		if ( $row > 0 ) {
			$value = $this->get_repeater_aware_value( $meta_key, $object_id, $row - 1 );
		}

		if ( ( $value === null || $value === false || $value === '' ) && $meta_key !== '' ) {
			if ( function_exists( 'get_field' ) ) {
				$value = get_field( $meta_key, $object_id );
			}
		}

		if ( ( $value === null || $value === false || $value === '' ) && $meta_key !== '' ) {
			$value = $this->get_raw_meta( $meta_key, $object_id );
		}

		if ( ! is_array( $field ) && $meta_key !== '' && function_exists( 'get_field_object' ) ) {
			$object = get_field_object( $meta_key, $object_id, false, true );
			if ( is_array( $object ) ) {
				$field = $object;
			}
		}

		if ( is_array( $field ) ) {
			$field['value'] = $value;
		}

		return array(
			'field'    => $field,
			'value'    => $value,
			'meta_key' => $meta_key,
			'property' => $property,
		);
	}

	/**
	 * Attempt to read `parent_N_child` style keys with an explicit row index.
	 *
	 * When the select option points at `repeater_subfield` (no index), inject the row.
	 *
	 * @param int|string $object_id
	 * @return mixed
	 */
	private function get_repeater_aware_value( string $meta_key, int|string $object_id, int $row_index ) {
		if ( ! function_exists( 'get_field' ) ) {
			return null;
		}

		// If meta already contains an explicit numeric segment, honour as-is.
		if ( preg_match( '/_\d+_/', $meta_key ) ) {
			return get_field( $meta_key, $object_id );
		}

		// Heuristic: try injecting row after each path segment from the left.
		$parts = explode( '_', $meta_key );
		$count = count( $parts );

		for ( $i = 1; $i < $count; $i++ ) {
			$candidate_parts   = $parts;
			array_splice( $candidate_parts, $i, 0, array( (string) $row_index ) );
			$candidate         = implode( '_', $candidate_parts );
			$candidate_value   = get_field( $candidate, $object_id );

			if ( $candidate_value !== null && $candidate_value !== false && $candidate_value !== '' ) {
				return $candidate_value;
			}
		}

		// Fallback: load parent repeater array and walk.
		$parent = $parts[0] ?? '';
		if ( $parent === '' ) {
			return null;
		}

		$rows = get_field( $parent, $object_id );

		if ( ! is_array( $rows ) || ! isset( $rows[ $row_index ] ) ) {
			return null;
		}

		$cursor = $rows[ $row_index ];
		$rest   = array_slice( $parts, 1 );

		foreach ( $rest as $segment ) {
			if ( ! is_array( $cursor ) || ! array_key_exists( $segment, $cursor ) ) {
				return null;
			}
			$cursor = $cursor[ $segment ];
		}

		return $cursor;
	}

	/**
	 * @param int|string $object_id
	 * @return mixed
	 */
	private function get_raw_meta( string $meta_key, int|string $object_id ) {
		if ( is_numeric( $object_id ) ) {
			return get_post_meta( (int) $object_id, $meta_key, true );
		}

		if ( is_string( $object_id ) && str_starts_with( $object_id, 'term_' ) ) {
			return get_term_meta( (int) substr( $object_id, 5 ), $meta_key, true );
		}

		if ( is_string( $object_id ) && str_starts_with( $object_id, 'user_' ) ) {
			return get_user_meta( (int) substr( $object_id, 5 ), $meta_key, true );
		}

		return null;
	}
}
