<?php
/**
 * Discovers ACF fields across all field groups and caches control options.
 *
 * @package HushUX\ElementorAcfGroupFields
 */

declare(strict_types=1);

namespace HushUX\ElementorAcfGroupFields\Services;

use HushUX\ElementorAcfGroupFields\Helpers\ACF;

final class FieldDiscovery {

	private const CACHE_GROUP = 'hux_eagf';
	private const CACHE_KEY   = 'discovered_v2';

	/** @var list<FieldDescriptor>|null */
	private ?array $descriptors = null;

	/** @var array<string, array<int, array{label: string, options: array<string, string>}>> */
	private array $control_options = array();

	public function __construct(
		private RecursiveWalker $walker
	) {}

	/**
	 * All leaf descriptors (request-cached + object-cache).
	 *
	 * @return list<FieldDescriptor>
	 */
	public function all(): array {
		if ( $this->descriptors !== null ) {
			return $this->descriptors;
		}

		$cache_bust = wp_cache_get( self::CACHE_KEY, self::CACHE_GROUP );

		$descriptors = array();
		$groups      = ACF::get_field_groups();

		foreach ( $groups as $group ) {
			$title  = (string) ( $group['title'] ?? __( 'ACF', 'elementor-acf-group-fields' ) );
			$fields = ACF::get_fields_for_group( $group );

			foreach ( $this->walker->walk( $fields, $title ) as $descriptor ) {
				$descriptors[] = $descriptor;
			}
		}

		/**
		 * Filter all discovered field descriptors.
		 *
		 * @param list<FieldDescriptor> $descriptors
		 */
		$this->descriptors = apply_filters( 'hux_eagf/discovered_fields', $descriptors );

		// Store a generation token so flush() can invalidate collaborating caches.
		if ( false === $cache_bust ) {
			wp_cache_set( self::CACHE_KEY, time(), self::CACHE_GROUP, HOUR_IN_SECONDS );
		}

		return $this->descriptors;
	}

	/**
	 * Build Elementor SELECT control groups for a tag kind and optional type whitelist.
	 *
	 * @param list<string>|null $acf_types Restrict to these ACF types (null = any for kind).
	 * @return array<int, array{label: string, options: array<string, string>}>
	 */
	public function control_options_for( string $tag_kind, ?array $acf_types = null ): array {
		$cache_key = $tag_kind . ':' . md5( wp_json_encode( $acf_types ) );

		if ( isset( $this->control_options[ $cache_key ] ) ) {
			return $this->control_options[ $cache_key ];
		}

		$grouped = array();

		foreach ( $this->all() as $descriptor ) {
			if ( $descriptor->tag_kind !== $tag_kind ) {
				continue;
			}

			if ( $acf_types !== null && ! in_array( $descriptor->acf_type, $acf_types, true ) ) {
				continue;
			}

			$title = $descriptor->group_title;

			if ( ! isset( $grouped[ $title ] ) ) {
				$grouped[ $title ] = array();
			}

			// Prefer nesting under breadcrumb parent when path depth > 1.
			$section = $title;
			if ( count( $descriptor->label_path ) > 1 ) {
				$section = $title . ' — ' . $descriptor->label_path[0];
			}

			if ( ! isset( $grouped[ $section ] ) ) {
				$grouped[ $section ] = array();
			}

			$grouped[ $section ][ $descriptor->option_key() ] = $descriptor->option_label();
		}

		$result = array();

		foreach ( $grouped as $label => $options ) {
			if ( $options === array() ) {
				continue;
			}

			if ( count( $options ) === 1 ) {
				$options = array( '' => ' — ' ) + $options;
			}

			$result[] = array(
				'label'   => $label,
				'options' => $options,
			);
		}

		/**
		 * Filter Elementor control option groups.
		 *
		 * @param array  $result
		 * @param string $tag_kind
		 */
		$this->control_options[ $cache_key ] = apply_filters( 'hux_eagf/control_options', $result, $tag_kind );

		return $this->control_options[ $cache_key ];
	}

	/**
	 * Find a descriptor by option key.
	 */
	public function find_by_option_key( string $option_key ): ?FieldDescriptor {
		foreach ( $this->all() as $descriptor ) {
			if ( $descriptor->option_key() === $option_key ) {
				return $descriptor;
			}
		}

		return null;
	}

	/**
	 * Invalidate caches (call after ACF field group saves).
	 */
	public function flush(): void {
		$this->descriptors     = null;
		$this->control_options = array();
		wp_cache_delete( self::CACHE_KEY, self::CACHE_GROUP );
		ACF::flush_cache();
	}
}
