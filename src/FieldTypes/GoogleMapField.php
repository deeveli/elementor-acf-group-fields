<?php
/**
 * ACF Google Map field.
 *
 * @package HushUX\ElementorAcfGroupFields
 */

declare(strict_types=1);

namespace HushUX\ElementorAcfGroupFields\FieldTypes;

use HushUX\ElementorAcfGroupFields\Helpers\Utils;

final class GoogleMapField extends AbstractFieldType {

	public function types(): array {
		return array( 'google_map' );
	}

	public function categories(): array {
		return array( 'text', 'post_meta' );
	}

	public function tag_kind(): string {
		return 'text';
	}

	public function properties(): array {
		return array(
			'address'   => __( 'Address', 'elementor-acf-group-fields' ),
			'lat'       => __( 'Latitude', 'elementor-acf-group-fields' ),
			'lng'       => __( 'Longitude', 'elementor-acf-group-fields' ),
			'zoom'      => __( 'Zoom', 'elementor-acf-group-fields' ),
		);
	}

	public function format( mixed $value, array $field, string $property = '' ): mixed {
		if ( ! is_array( $value ) ) {
			return '';
		}

		if ( $property === '' ) {
			$property = 'address';
		}

		$map = array(
			'address' => (string) ( $value['address'] ?? '' ),
			'lat'     => (string) ( $value['lat'] ?? '' ),
			'lng'     => (string) ( $value['lng'] ?? '' ),
			'zoom'    => (string) ( $value['zoom'] ?? '' ),
		);

		return Utils::stringify( $map[ $property ] ?? '' );
	}
}
