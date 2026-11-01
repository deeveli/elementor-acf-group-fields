<?php
/**
 * ACF Image field — image data tag plus text properties.
 *
 * @package HushUX\ElementorAcfGroupFields
 */

declare(strict_types=1);

namespace HushUX\ElementorAcfGroupFields\FieldTypes;

use HushUX\ElementorAcfGroupFields\Helpers\Utils;

final class ImageField extends AbstractFieldType {

	public function types(): array {
		return array( 'image' );
	}

	public function categories(): array {
		return array( 'image' );
	}

	public function tag_kind(): string {
		return 'image';
	}

	public function properties(): array {
		return array(
			'image'       => __( 'Image', 'elementor-acf-group-fields' ),
			'id'          => __( 'ID', 'elementor-acf-group-fields' ),
			'url'         => __( 'URL', 'elementor-acf-group-fields' ),
			'alt'         => __( 'Alt', 'elementor-acf-group-fields' ),
			'caption'     => __( 'Caption', 'elementor-acf-group-fields' ),
			'description' => __( 'Description', 'elementor-acf-group-fields' ),
			'width'       => __( 'Width', 'elementor-acf-group-fields' ),
			'height'      => __( 'Height', 'elementor-acf-group-fields' ),
			'mime_type'   => __( 'Mime Type', 'elementor-acf-group-fields' ),
			'filename'    => __( 'Filename', 'elementor-acf-group-fields' ),
		);
	}

	public function format( mixed $value, array $field, string $property = '' ): mixed {
		$data = $this->normalize( $value );

		if ( $property === '' || $property === 'image' ) {
			return array(
				'id'  => $data['id'],
				'url' => $data['url'],
			);
		}

		return Utils::stringify( $data[ $property ] ?? '' );
	}

	/**
	 * @param mixed $value
	 * @return array<string, mixed>
	 */
	public function normalize( mixed $value ): array {
		$empty = array(
			'id'          => 0,
			'url'         => '',
			'alt'         => '',
			'caption'     => '',
			'description' => '',
			'width'       => '',
			'height'      => '',
			'mime_type'   => '',
			'filename'    => '',
		);

		if ( empty( $value ) ) {
			return $empty;
		}

		if ( is_numeric( $value ) ) {
			return $this->from_attachment_id( (int) $value );
		}

		if ( is_string( $value ) ) {
			$empty['url'] = $value;
			return $empty;
		}

		if ( ! is_array( $value ) ) {
			return $empty;
		}

		$id = (int) ( $value['ID'] ?? $value['id'] ?? 0 );

		if ( $id > 0 && empty( $value['url'] ) ) {
			return $this->from_attachment_id( $id );
		}

		return array(
			'id'          => $id,
			'url'         => (string) ( $value['url'] ?? '' ),
			'alt'         => (string) ( $value['alt'] ?? '' ),
			'caption'     => (string) ( $value['caption'] ?? '' ),
			'description' => (string) ( $value['description'] ?? '' ),
			'width'       => (string) ( $value['width'] ?? '' ),
			'height'      => (string) ( $value['height'] ?? '' ),
			'mime_type'   => (string) ( $value['mime_type'] ?? '' ),
			'filename'    => (string) ( $value['filename'] ?? basename( (string) ( $value['url'] ?? '' ) ) ),
		);
	}

	/**
	 * @return array<string, mixed>
	 */
	private function from_attachment_id( int $id ): array {
		$url  = (string) wp_get_attachment_url( $id );
		$meta = wp_get_attachment_metadata( $id ) ?: array();
		$file = get_attached_file( $id );

		return array(
			'id'          => $id,
			'url'         => $url,
			'alt'         => (string) get_post_meta( $id, '_wp_attachment_image_alt', true ),
			'caption'     => (string) wp_get_attachment_caption( $id ),
			'description' => (string) ( get_post( $id )->post_content ?? '' ),
			'width'       => (string) ( $meta['width'] ?? '' ),
			'height'      => (string) ( $meta['height'] ?? '' ),
			'mime_type'   => (string) get_post_mime_type( $id ),
			'filename'    => $file ? basename( $file ) : '',
		);
	}
}
