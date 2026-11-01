<?php
/**
 * Formats resolved ACF values via field-type handlers.
 *
 * @package HushUX\ElementorAcfGroupFields
 */

declare(strict_types=1);

namespace HushUX\ElementorAcfGroupFields\Services;

use HushUX\ElementorAcfGroupFields\FieldTypes\FieldTypeManager;
use HushUX\ElementorAcfGroupFields\Helpers\Utils;

final class FieldFormatter {

	public function __construct(
		private FieldTypeManager $field_types
	) {}

	/**
	 * @param mixed                     $value
	 * @param array<string, mixed>|null $field
	 * @return mixed
	 */
	public function format( mixed $value, ?array $field, string $property = '' ): mixed {
		if ( ! is_array( $field ) || empty( $field['type'] ) ) {
			return Utils::stringify( $value );
		}

		$handler = $this->field_types->get_or_default( (string) $field['type'] );

		/**
		 * Filter formatted dynamic tag output.
		 *
		 * @param mixed                $formatted
		 * @param mixed                $value
		 * @param array<string, mixed> $field
		 * @param string               $property
		 */
		return apply_filters(
			'hux_eagf/format_value',
			$handler->format( $value, $field, $property ),
			$value,
			$field,
			$property
		);
	}
}
