<?php
/**
 * Thin wrappers around ACF APIs with caching.
 *
 * @package HushUX\ElementorAcfGroupFields
 */

declare(strict_types=1);

namespace HushUX\ElementorAcfGroupFields\Helpers;

final class ACF {

	/** @var array<string, mixed>|null */
	private static ?array $field_groups_cache = null;

	/** @var array<int|string, array<int, array<string, mixed>>> */
	private static array $fields_cache = array();

	/**
	 * All ACF field groups (cached per request).
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public static function get_field_groups(): array {
		if ( self::$field_groups_cache !== null ) {
			return self::$field_groups_cache;
		}

		if ( function_exists( 'acf_get_field_groups' ) ) {
			self::$field_groups_cache = acf_get_field_groups() ?: array();
		} else {
			self::$field_groups_cache = apply_filters( 'acf/get_field_groups', array() ) ?: array();
		}

		return self::$field_groups_cache;
	}

	/**
	 * Fields belonging to a field group (cached).
	 *
	 * @param array<string, mixed> $group
	 * @return array<int, array<string, mixed>>
	 */
	public static function get_fields_for_group( array $group ): array {
		$cache_key = (string) ( $group['key'] ?? $group['ID'] ?? $group['id'] ?? md5( serialize( $group ) ) );

		if ( isset( self::$fields_cache[ $cache_key ] ) ) {
			return self::$fields_cache[ $cache_key ];
		}

		$fields = array();

		if ( function_exists( 'acf_get_fields' ) ) {
			if ( ! empty( $group['ID'] ) ) {
				$fields = acf_get_fields( $group['ID'] ) ?: array();
			} else {
				$fields = acf_get_fields( $group ) ?: array();
			}
		} else {
			$fields = apply_filters( 'acf/field_group/get_fields', array(), $group['id'] ?? 0 ) ?: array();
		}

		self::$fields_cache[ $cache_key ] = is_array( $fields ) ? $fields : array();

		return self::$fields_cache[ $cache_key ];
	}

	/**
	 * Resolve cloned fields into concrete field definitions.
	 *
	 * @param array<string, mixed> $clone_field
	 * @return array<int, array<string, mixed>>
	 */
	public static function resolve_clone_fields( array $clone_field ): array {
		if ( ! function_exists( 'acf_get_field' ) ) {
			return array();
		}

		$resolved = array();
		$seen     = array();
		$targets  = $clone_field['clone'] ?? array();

		if ( ! is_array( $targets ) ) {
			return array();
		}

		foreach ( $targets as $target ) {
			$field = is_string( $target ) ? acf_get_field( $target ) : null;

			if ( ! is_array( $field ) ) {
				continue;
			}

			// Clone of an entire field group.
			if ( ( $field['type'] ?? '' ) === 'clone' ) {
				foreach ( self::resolve_clone_fields( $field ) as $nested ) {
					$key = $nested['key'] ?? '';
					if ( $key === '' || isset( $seen[ $key ] ) ) {
						continue;
					}
					$seen[ $key ] = true;
					$resolved[]   = self::apply_clone_prefix( $nested, $clone_field );
				}
				continue;
			}

			$key = $field['key'] ?? '';
			if ( $key === '' || isset( $seen[ $key ] ) ) {
				continue;
			}

			$seen[ $key ] = true;
			$resolved[]   = self::apply_clone_prefix( $field, $clone_field );
		}

		/**
		 * Filter resolved clone children.
		 *
		 * @param array<int, array<string, mixed>> $resolved
		 * @param array<string, mixed>             $clone_field
		 */
		return apply_filters( 'hux_eagf/clone_fields', $resolved, $clone_field );
	}

	/**
	 * @param array<string, mixed> $field
	 * @param array<string, mixed> $clone_field
	 * @return array<string, mixed>
	 */
	private static function apply_clone_prefix( array $field, array $clone_field ): array {
		$prefix_name = ! empty( $clone_field['prefix_name'] );

		if ( $prefix_name && ! empty( $clone_field['name'] ) ) {
			$field['name']  = $clone_field['name'] . '_' . ( $field['name'] ?? '' );
			$field['label'] = ( $clone_field['label'] ?? 'Clone' ) . ' → ' . ( $field['label'] ?? '' );
		}

		return $field;
	}

	/**
	 * Current ACF post/term/user/options context ID.
	 *
	 * @return int|string
	 */
	public static function get_current_object_id(): int|string {
		if ( function_exists( 'acf_get_valid_post_id' ) ) {
			return acf_get_valid_post_id( false );
		}

		if ( is_singular() ) {
			return get_the_ID() ?: 0;
		}

		if ( is_tax() || is_category() || is_tag() ) {
			return 'term_' . get_queried_object_id();
		}

		if ( is_author() ) {
			return 'user_' . get_queried_object_id();
		}

		return get_the_ID() ?: 0;
	}

	/**
	 * Flush request caches (useful in tests).
	 */
	public static function flush_cache(): void {
		self::$field_groups_cache = null;
		self::$fields_cache       = array();
	}
}
