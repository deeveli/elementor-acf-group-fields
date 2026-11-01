<?php
/**
 * Text / scalar ACF dynamic tag.
 *
 * Tag name `acf_group_tag` preserved for backwards compatibility.
 *
 * @package HushUX\ElementorAcfGroupFields
 */

declare(strict_types=1);

namespace HushUX\ElementorAcfGroupFields\Tags;

use Elementor\Core\DynamicTags\Tag;
use HushUX\ElementorAcfGroupFields\Helpers\Elementor as ElementorHelper;
use HushUX\ElementorAcfGroupFields\Helpers\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class TextTag extends Tag {

	use TagServicesTrait;

	public function get_name(): string {
		return 'acf_group_tag';
	}

	public function get_title(): string {
		return esc_html__( 'ACF Nested Fields', 'elementor-acf-group-fields' );
	}

	public function get_categories(): array {
		return ElementorHelper::map_categories( array( 'text', 'post_meta' ) );
	}

	protected function register_controls(): void {
		// null types = all descriptors whose tag_kind is text (includes expanded properties).
		$this->register_key_controls( array(), 'text' );
	}

	/**
	 * Legacy Elementor control registration alias.
	 */
	protected function _register_controls(): void { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
		$this->register_controls();
	}

	public function render(): void {
		$context  = $this->resolve_context();
		$field    = $context['field'];
		$value    = $context['value'];
		$property = $context['property'];

		$formatted = $this->format_value( $value, $field, $property );

		if ( is_array( $formatted ) ) {
			$formatted = Utils::stringify( $formatted );
		}

		echo wp_kses_post( (string) $formatted );
	}
}
