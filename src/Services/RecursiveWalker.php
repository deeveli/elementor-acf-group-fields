<?php
/**
 * Recursive ACF field tree walker — unlimited nesting, loop-safe.
 *
 * @package HushUX\ElementorAcfGroupFields
 */

declare(strict_types=1);

namespace HushUX\ElementorAcfGroupFields\Services;

use HushUX\ElementorAcfGroupFields\FieldTypes\FieldTypeManager;
use HushUX\ElementorAcfGroupFields\Helpers\Utils;

final class RecursiveWalker {

	private const MAX_DEPTH = 64;

	public function __construct(
		private FieldTypeManager $field_types
	) {}

	/**
	 * Walk fields and yield leaf descriptors (including expanded properties).
	 *
	 * @param array<int, array<string, mixed>> $fields
	 * @param list<string>                     $name_path
	 * @param list<string>                     $label_path
	 * @param array<string, bool>              $visited_keys
	 * @return \Generator<int, FieldDescriptor>
	 */
	public function walk(
		array $fields,
		string $group_title,
		array $name_path = array(),
		array $label_path = array(),
		array $visited_keys = array(),
		bool $in_repeater = false,
		int $depth = 0
	): \Generator {
		if ( $depth >= self::MAX_DEPTH ) {
			return;
		}

		foreach ( $fields as $field ) {
			if ( ! is_array( $field ) ) {
				continue;
			}

			$type = (string) ( $field['type'] ?? '' );
			$key  = (string) ( $field['key'] ?? '' );
			$name = (string) ( $field['name'] ?? '' );

			if ( $type === '' || $name === '' ) {
				continue;
			}

			// Prevent infinite clone / circular references.
			if ( $key !== '' ) {
				$visit_token = $key . '@' . implode( '/', $name_path );
				if ( isset( $visited_keys[ $visit_token ] ) ) {
					continue;
				}
				$visited_keys[ $visit_token ] = true;
			}

			$handler = $this->field_types->get_or_default( $type );

			if ( $handler->is_layout_only() ) {
				continue;
			}

			$child_names  = array_merge( $name_path, array( $name ) );
			$child_labels = array_merge( $label_path, array( (string) ( $field['label'] ?? $name ) ) );

			// Flexible Content layout annotation for clearer labels.
			$flex_layout = (string) ( $field['_hux_flex_layout'] ?? '' );
			if ( $flex_layout !== '' ) {
				$layout_label = (string) ( $field['_hux_flex_layout_label'] ?? $flex_layout );
				// Insert layout label before this field's own label.
				array_splice( $child_labels, -1, 0, array( $layout_label ) );
			}

			if ( $handler->is_container() ) {
				$next_in_repeater = $in_repeater || in_array( $type, array( 'repeater', 'flexible_content' ), true );
				$children         = $handler->children( $field );

				yield from $this->walk(
					$children,
					$group_title,
					$child_names,
					$child_labels,
					$visited_keys,
					$next_in_repeater,
					$depth + 1
				);
				continue;
			}

			$meta_path = Utils::join_meta_path( $child_names );
			$label     = Utils::join_label_path( $child_labels );
			$tag_kind  = $handler->tag_kind();
			$props     = $handler->properties();

			/**
			 * Filter whether a leaf field should be registered.
			 *
			 * @param bool                 $register
			 * @param array<string, mixed> $field
			 */
			if ( ! apply_filters( 'hux_eagf/should_register_field', true, $field ) ) {
				continue;
			}

			if ( $props === array() ) {
				yield new FieldDescriptor(
					acf_type: $type,
					field_key: $key,
					meta_path: $meta_path,
					label: $label,
					group_title: $group_title,
					name_path: $child_names,
					label_path: $child_labels,
					field: $field,
					property: '',
					tag_kind: $tag_kind,
					in_repeater: $in_repeater,
					flex_layout: $flex_layout,
				);
				continue;
			}

			foreach ( $props as $prop_key => $prop_label ) {
				$prop_field                          = $field;
				$prop_field['_hux_property_label']   = $prop_label;

				// Image "image" property stays on the image tag; scalar props also on text/url tags.
				$prop_kind = $this->property_tag_kind( $type, (string) $prop_key, $tag_kind );

				yield new FieldDescriptor(
					acf_type: $type,
					field_key: $key,
					meta_path: $meta_path,
					label: $label,
					group_title: $group_title,
					name_path: $child_names,
					label_path: $child_labels,
					field: $prop_field,
					property: (string) $prop_key,
					tag_kind: $prop_kind,
					in_repeater: $in_repeater,
					flex_layout: $flex_layout,
				);
			}
		}
	}

	/**
	 * Decide which Elementor tag kind should expose a property.
	 */
	private function property_tag_kind( string $acf_type, string $property, string $default_kind ): string {
		if ( $acf_type === 'image' && $property === 'image' ) {
			return 'image';
		}

		if ( $acf_type === 'image' ) {
			return in_array( $property, array( 'url' ), true ) ? 'url' : 'text';
		}

		if ( $acf_type === 'file' && $property === 'url' ) {
			return 'media';
		}

		if ( $acf_type === 'file' ) {
			return 'text';
		}

		if ( $acf_type === 'link' ) {
			return $property === 'url' ? 'url' : 'text';
		}

		if ( in_array( $acf_type, array( 'post_object', 'page_link' ), true ) && $property === 'permalink' ) {
			return 'url';
		}

		if ( $acf_type === 'relationship' && $property === 'urls' ) {
			return 'url';
		}

		if ( $acf_type === 'taxonomy' && $property === 'archive_url' ) {
			return 'url';
		}

		if ( $acf_type === 'user' && $property === 'website' ) {
			return 'url';
		}

		if ( $acf_type === 'user' && $property === 'avatar' ) {
			return 'image';
		}

		return $default_kind === 'image' || $default_kind === 'gallery' || $default_kind === 'media'
			? 'text'
			: $default_kind;
	}
}
