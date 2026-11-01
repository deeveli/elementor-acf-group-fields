<?php
/**
 * ACF File field.
 *
 * @package HushUX\ElementorAcfGroupFields
 */

declare(strict_types=1);

namespace HushUX\ElementorAcfGroupFields\FieldTypes;

use HushUX\ElementorAcfGroupFields\Helpers\Utils;

final class FileField extends AbstractFieldType {

	public function types(): array {
		return array( 'file' );
	}

	public function categories(): array {
		return array( 'media', 'url', 'text', 'post_meta' );
	}

	public function tag_kind(): string {
		return 'media';
	}

	public function properties(): array {
		return array(
			'url'       => __( 'URL', 'elementor-acf-group-fields' ),
			'title'     => __( 'Title', 'elementor-acf-group-fields' ),
			'filename'  => __( 'Filename', 'elementor-acf-group-fields' ),
			'filesize'  => __( 'Size', 'elementor-acf-group-fields' ),
			'mime_type' => __( 'Mime', 'elementor-acf-group-fields' ),
			'extension' => __( 'Extension', 'elementor-acf-group-fields' ),
			'id'        => __( 'ID', 'elementor-acf-group-fields' ),
		);
	}

	public function format( mixed $value, array $field, string $property = '' ): mixed {
		$data = $this->normalize( $value );

		if ( $property === '' ) {
			$property = 'url';
		}

		if ( $property === 'url' ) {
			return array(
				'id'  => (int) ( $data['id'] ?? 0 ),
				'url' => (string) ( $data['url'] ?? '' ),
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
			'id'        => 0,
			'url'       => '',
			'title'     => '',
			'filename'  => '',
			'filesize'  => '',
			'mime_type' => '',
			'extension' => '',
		);

		if ( empty( $value ) ) {
			return $empty;
		}

		if ( is_numeric( $value ) ) {
			return $this->from_id( (int) $value );
		}

		if ( is_string( $value ) ) {
			$empty['url']      = $value;
			$empty['filename'] = basename( $value );
			$empty['extension'] = pathinfo( $value, PATHINFO_EXTENSION );
			return $empty;
		}

		if ( ! is_array( $value ) ) {
			return $empty;
		}

		$id = (int) ( $value['ID'] ?? $value['id'] ?? 0 );

		if ( $id > 0 && empty( $value['url'] ) ) {
			return $this->from_id( $id );
		}

		$url = (string) ( $value['url'] ?? '' );

		return array(
			'id'        => $id,
			'url'       => $url,
			'title'     => (string) ( $value['title'] ?? '' ),
			'filename'  => (string) ( $value['filename'] ?? basename( $url ) ),
			'filesize'  => (string) ( $value['filesize'] ?? $value['size'] ?? '' ),
			'mime_type' => (string) ( $value['mime_type'] ?? '' ),
			'extension' => (string) ( $value['subtype'] ?? pathinfo( $url, PATHINFO_EXTENSION ) ),
		);
	}

	/**
	 * @return array<string, mixed>
	 */
	private function from_id( int $id ): array {
		$url  = (string) wp_get_attachment_url( $id );
		$file = get_attached_file( $id );
		$path = $file ?: $url;

		return array(
			'id'        => $id,
			'url'       => $url,
			'title'     => (string) get_the_title( $id ),
			'filename'  => $file ? basename( $file ) : basename( $url ),
			'filesize'  => $file && file_exists( $file ) ? (string) filesize( $file ) : '',
			'mime_type' => (string) get_post_mime_type( $id ),
			'extension' => pathinfo( $path, PATHINFO_EXTENSION ),
		);
	}
}
