<?php
/**
 * Contract for ACF field-type handlers.
 *
 * @package HushUX\ElementorAcfGroupFields
 */

declare(strict_types=1);

namespace HushUX\ElementorAcfGroupFields\Contracts;

interface FieldTypeHandlerInterface {

	/**
	 * ACF field type slugs this handler owns.
	 *
	 * @return list<string>
	 */
	public function types(): array;

	/**
	 * Whether this type contains nested sub-fields.
	 */
	public function is_container(): bool;

	/**
	 * Whether this type is layout-only (tab / accordion / message) and should not register tags.
	 */
	public function is_layout_only(): bool;

	/**
	 * Child fields for container types.
	 *
	 * @param array<string, mixed> $field
	 * @return array<int, array<string, mixed>>
	 */
	public function children( array $field ): array;

	/**
	 * Optional layout metadata for flexible content (layout name => label).
	 *
	 * @param array<string, mixed> $field
	 * @return array<string, string>
	 */
	public function layouts( array $field ): array;

	/**
	 * Logical Elementor dynamic-tag categories for leaf registration.
	 *
	 * @return list<string>
	 */
	public function categories(): array;

	/**
	 * Expandable properties (e.g. url, title, target). Empty = single value.
	 *
	 * @return array<string, string> property => label
	 */
	public function properties(): array;

	/**
	 * Which Elementor tag class key should register this field (text|image|gallery|url|media).
	 */
	public function tag_kind(): string;

	/**
	 * Format a raw ACF value for Elementor output.
	 *
	 * @param mixed                $value
	 * @param array<string, mixed> $field
	 * @param string               $property
	 * @return mixed
	 */
	public function format( mixed $value, array $field, string $property = '' ): mixed;
}
