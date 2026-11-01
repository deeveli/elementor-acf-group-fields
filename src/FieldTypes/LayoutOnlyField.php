<?php
/**
 * Non-data ACF UI fields (tab, accordion, message).
 *
 * @package HushUX\ElementorAcfGroupFields
 */

declare(strict_types=1);

namespace HushUX\ElementorAcfGroupFields\FieldTypes;

final class LayoutOnlyField extends AbstractFieldType {

	public function types(): array {
		return array( 'tab', 'accordion', 'message', 'separator' );
	}

	public function is_layout_only(): bool {
		return true;
	}
}
