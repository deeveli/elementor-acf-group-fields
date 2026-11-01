<?php
/**
 * ACF Link field — exposes URL, Title, Target.
 *
 * @package HushUX\ElementorAcfGroupFields
 */

declare(strict_types=1);

namespace HushUX\ElementorAcfGroupFields\FieldTypes;

use HushUX\ElementorAcfGroupFields\Helpers\Utils;

final class LinkField extends AbstractFieldType {

	public function types(): array {
		return array( 'link' );
	}

	public function categories(): array {
		return array( 'url', 'text', 'post_meta' );
	}

	public function tag_kind(): string {
		return 'url';
	}

	public function properties(): array {
		return array(
			'url'    => __( 'URL', 'elementor-acf-group-fields' ),
			'title'  => __( 'Title', 'elementor-acf-group-fields' ),
			'target' => __( 'Target', 'elementor-acf-group-fields' ),
		);
	}

	public function format( mixed $value, array $field, string $property = '' ): mixed {
		$normalized = $this->normalize( $value );

		if ( $property === '' ) {
			$property = 'url';
		}

		return match ( $property ) {
			'url'    => esc_url( (string) ( $normalized['url'] ?? '' ) ),
			'title'  => (string) ( $normalized['title'] ?? '' ),
			'target' => (string) ( $normalized['target'] ?? '' ),
			default  => Utils::stringify( $normalized[ $property ] ?? '' ),
		};
	}

	/**
	 * @param mixed $value
	 * @return array{url: string, title: string, target: string}
	 */
	private function normalize( mixed $value ): array {
		if ( is_string( $value ) ) {
			return array(
				'url'    => $value,
				'title'  => '',
				'target' => '',
			);
		}

		if ( ! is_array( $value ) ) {
			return array(
				'url'    => '',
				'title'  => '',
				'target' => '',
			);
		}

		return array(
			'url'    => (string) ( $value['url'] ?? '' ),
			'title'  => (string) ( $value['title'] ?? '' ),
			'target' => (string) ( $value['target'] ?? '' ),
		);
	}
}
