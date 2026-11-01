<?php
/**
 * Image ACF dynamic tag.
 *
 * Tag name `acf_group_image_tag` preserved for backwards compatibility.
 *
 * @package HushUX\ElementorAcfGroupFields
 */

declare(strict_types=1);

namespace HushUX\ElementorAcfGroupFields\Tags;

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Data_Tag;
use HushUX\ElementorAcfGroupFields\FieldTypes\ImageField;
use HushUX\ElementorAcfGroupFields\Helpers\Elementor as ElementorHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ImageTag extends Data_Tag {

	use TagServicesTrait;

	public function get_name(): string {
		return 'acf_group_image_tag';
	}

	public function get_title(): string {
		return esc_html__( 'ACF Nested Image', 'elementor-acf-group-fields' );
	}

	public function get_categories(): array {
		return ElementorHelper::map_categories( array( 'image' ) );
	}

	protected function register_controls(): void {
		$this->register_key_controls( array(), 'image' );

		$this->add_control(
			'fallback',
			array(
				'label' => esc_html__( 'Fallback', 'elementor-acf-group-fields' ),
				'type'  => Controls_Manager::MEDIA,
			)
		);
	}

	protected function _register_controls(): void { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
		$this->register_controls();
	}

	/**
	 * @param array<string, mixed> $options
	 * @return array{id: ?int, url: string}
	 */
	public function get_value( array $options = array() ): array {
		$image_data = array(
			'id'  => null,
			'url' => '',
		);

		$context  = $this->resolve_context();
		$field    = $context['field'];
		$value    = $context['value'];
		$property = $context['property'] !== '' ? $context['property'] : 'image';

		// User avatar property returns a URL string.
		if ( is_array( $field ) && ( $field['type'] ?? '' ) === 'user' ) {
			$url = (string) $this->format_value( $value, $field, $property !== 'image' ? $property : 'avatar' );
			if ( $url !== '' ) {
				return array(
					'id'  => 0,
					'url' => $url,
				);
			}
		}

		$handler = new ImageField();
		$data    = $handler->normalize( $value );

		if ( $property !== 'image' && $property !== '' ) {
			$url = (string) ( $data[ $property ] ?? '' );
			if ( $property === 'url' && $url !== '' ) {
				return array(
					'id'  => (int) ( $data['id'] ?? 0 ),
					'url' => $url,
				);
			}
		}

		if ( ! empty( $data['url'] ) || ! empty( $data['id'] ) ) {
			$image_data['id']  = $data['id'] ? (int) $data['id'] : null;
			$image_data['url'] = (string) $data['url'];
		}

		if ( empty( $image_data['url'] ) && empty( $image_data['id'] ) ) {
			$fallback = $this->get_settings( 'fallback' );
			if ( is_array( $fallback ) && ( ! empty( $fallback['url'] ) || ! empty( $fallback['id'] ) ) ) {
				return array(
					'id'  => isset( $fallback['id'] ) ? (int) $fallback['id'] : null,
					'url' => (string) ( $fallback['url'] ?? '' ),
				);
			}
		}

		return $image_data;
	}
}
