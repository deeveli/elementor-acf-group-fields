<?php
/**
 * ACF Group container.
 *
 * @package HushUX\ElementorAcfGroupFields
 */

declare(strict_types=1);

namespace HushUX\ElementorAcfGroupFields\FieldTypes;

final class GroupField extends AbstractFieldType {

	public function types(): array {
		return array( 'group' );
	}

	public function is_container(): bool {
		return true;
	}

	public function children( array $field ): array {
		$subs = $field['sub_fields'] ?? array();
		return is_array( $subs ) ? $subs : array();
	}
}
