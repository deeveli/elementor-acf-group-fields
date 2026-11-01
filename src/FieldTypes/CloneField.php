<?php
/**
 * ACF Clone container — resolves referenced fields.
 *
 * @package HushUX\ElementorAcfGroupFields
 */

declare(strict_types=1);

namespace HushUX\ElementorAcfGroupFields\FieldTypes;

use HushUX\ElementorAcfGroupFields\Helpers\ACF;

final class CloneField extends AbstractFieldType {

	public function types(): array {
		return array( 'clone' );
	}

	public function is_container(): bool {
		return true;
	}

	public function children( array $field ): array {
		return ACF::resolve_clone_fields( $field );
	}
}
