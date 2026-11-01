<?php
/**
 * Immutable description of a discoverable leaf (or property) field.
 *
 * @package HushUX\ElementorAcfGroupFields
 */

declare(strict_types=1);

namespace HushUX\ElementorAcfGroupFields\Services;

final class FieldDescriptor {

	/**
	 * @param list<string>         $name_path   ACF name segments (groups / repeaters).
	 * @param list<string>         $label_path  Human labels for UI.
	 * @param array<string, mixed> $field       ACF field array.
	 * @param bool                 $in_repeater Whether any ancestor is repeater/flexible.
	 */
	public function __construct(
		public readonly string $acf_type,
		public readonly string $field_key,
		public readonly string $meta_path,
		public readonly string $label,
		public readonly string $group_title,
		public readonly array $name_path,
		public readonly array $label_path,
		public readonly array $field,
		public readonly string $property = '',
		public readonly string $tag_kind = 'text',
		public readonly bool $in_repeater = false,
		public readonly string $flex_layout = '',
	) {}

	/**
	 * Elementor select option key.
	 */
	public function option_key(): string {
		$key = $this->field_key . ':' . $this->meta_path;

		if ( $this->property !== '' ) {
			$key .= '|' . $this->property;
		}

		return $key;
	}

	/**
	 * Select option label.
	 */
	public function option_label(): string {
		if ( $this->property === '' ) {
			return $this->label;
		}

		$props = $this->field['_hux_property_label'] ?? $this->property;
		return $this->label . ' → ' . (string) $props;
	}
}
