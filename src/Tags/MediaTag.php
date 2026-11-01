<?php
/**
 * Media / File ACF dynamic tag.
 *
 * @package HushUX\ElementorAcfGroupFields
 */

declare(strict_types=1);

namespace HushUX\ElementorAcfGroupFields\Tags;

use Elementor\Core\DynamicTags\Data_Tag;
use HushUX\ElementorAcfGroupFields\Helpers\Elementor as ElementorHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MediaTag extends Data_Tag {

	use TagServicesTrait;

	public function get_name(): string {
		return 'acf_group_media_tag';
	}

	public function get_title(): string {
		return esc_html__( 'ACF Nested File / Media', 'elementor-acf-group-fields' );
	}

	public function get_categories(): array {
		return ElementorHelper::map_categories( array( 'media', 'url' ) );
	}

	protected function register_controls(): void {
		$this->register_key_controls( array(), 'media' );
	}

	protected function _register_controls(): void { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
		$this->register_controls();
	}

	/**
	 * @param array<string, mixed> $options
	 * @return array{id: int|null, url: string}|string
	 */
	public function get_value( array $options = array() ) {
		$context   = $this->resolve_context();
		$property  = $context['property'] !== '' ? $context['property'] : 'url';
		$formatted = $this->format_value( $context['value'], $context['field'], $property );

		if ( is_array( $formatted ) ) {
			return array(
				'id'  => isset( $formatted['id'] ) ? (int) $formatted['id'] : null,
				'url' => (string) ( $formatted['url'] ?? '' ),
			);
		}

		return array(
			'id'  => null,
			'url' => esc_url( (string) $formatted ),
		);
	}
}
