<?php
/**
 * ACF URL field.
 *
 * @package HushUX\ElementorAcfGroupFields
 */

declare(strict_types=1);

namespace HushUX\ElementorAcfGroupFields\FieldTypes;

use HushUX\ElementorAcfGroupFields\Helpers\Utils;

final class UrlField extends AbstractFieldType {

	public function types(): array {
		return array( 'url' );
	}

	public function categories(): array {
		return array( 'url', 'text', 'post_meta' );
	}

	public function tag_kind(): string {
		return 'url';
	}

	public function format( mixed $value, array $field, string $property = '' ): mixed {
		return esc_url( Utils::stringify( $value ) );
	}
}
