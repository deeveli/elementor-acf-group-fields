<?php
/**
 * Gallery ACF dynamic tag.
 *
 * Tag name `acf_group_gallery_tag` preserved for backwards compatibility.
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

class GalleryTag extends Data_Tag {

	use TagServicesTrait;

	public function get_name(): string {
		return 'acf_group_gallery_tag';
	}

	public function get_title(): string {
		return esc_html__( 'ACF Nested Gallery', 'elementor-acf-group-fields' );
	}

	public function get_categories(): array {
		return ElementorHelper::map_categories( array( 'gallery' ) );
	}

	protected function register_controls(): void {
		$this->register_key_controls( array(), 'gallery' );
	}

	protected function _register_controls(): void { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
		$this->register_controls();
	}

	/**
	 * @param array<string, mixed> $options
	 * @return list<array{id: int}>
	 */
	public function get_value( array $options = array() ): array {
		$context = $this->resolve_context();
		$result  = $this->format_value( $context['value'], $context['field'], $context['property'] );

		return is_array( $result ) ? $result : array();
	}
}
