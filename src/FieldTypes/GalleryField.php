<?php
/**
 * ACF Gallery field.
 *
 * @package HushUX\ElementorAcfGroupFields
 */

declare(strict_types=1);

namespace HushUX\ElementorAcfGroupFields\FieldTypes;

final class GalleryField extends AbstractFieldType {

	public function types(): array {
		return array( 'gallery' );
	}

	public function categories(): array {
		return array( 'gallery' );
	}

	public function tag_kind(): string {
		return 'gallery';
	}

	public function format( mixed $value, array $field, string $property = '' ): mixed {
		$images = array();

		if ( ! is_array( $value ) ) {
			return $images;
		}

		foreach ( $value as $image ) {
			$id = 0;

			if ( is_numeric( $image ) ) {
				$id = (int) $image;
			} elseif ( is_array( $image ) ) {
				$id = (int) ( $image['ID'] ?? $image['id'] ?? 0 );
			}

			if ( $id > 0 ) {
				$images[] = array( 'id' => $id );
			}
		}

		return $images;
	}
}
