<?php
/**
 * Base field-type handler with sensible defaults.
 *
 * @package HushUX\ElementorAcfGroupFields
 */

declare(strict_types=1);

namespace HushUX\ElementorAcfGroupFields\FieldTypes;

use HushUX\ElementorAcfGroupFields\Contracts\FieldTypeHandlerInterface;
use HushUX\ElementorAcfGroupFields\Helpers\Utils;

abstract class AbstractFieldType implements FieldTypeHandlerInterface {

	abstract public function types(): array;

	public function is_container(): bool {
		return false;
	}

	public function is_layout_only(): bool {
		return false;
	}

	public function children( array $field ): array {
		return array();
	}

	public function layouts( array $field ): array {
		return array();
	}

	public function categories(): array {
		return array( 'text', 'post_meta' );
	}

	public function properties(): array {
		return array();
	}

	public function tag_kind(): string {
		return 'text';
	}

	public function format( mixed $value, array $field, string $property = '' ): mixed {
		if ( $property !== '' && is_array( $value ) && array_key_exists( $property, $value ) ) {
			return Utils::stringify( $value[ $property ] );
		}

		return Utils::stringify( $value );
	}
}
