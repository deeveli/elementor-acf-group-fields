<?php
/**
 * URL / Link ACF dynamic tag.
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

class UrlTag extends Data_Tag {

	use TagServicesTrait;

	public function get_name(): string {
		return 'acf_group_url_tag';
	}

	public function get_title(): string {
		return esc_html__( 'ACF Nested URL / Link', 'elementor-acf-group-fields' );
	}

	public function get_categories(): array {
		return ElementorHelper::map_categories( array( 'url' ) );
	}

	protected function register_controls(): void {
		$this->register_key_controls( array(), 'url' );
	}

	protected function _register_controls(): void { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
		$this->register_controls();
	}

	/**
	 * @param array<string, mixed> $options
	 */
	public function get_value( array $options = array() ): string {
		$context   = $this->resolve_context();
		$property  = $context['property'];
		$formatted = $this->format_value( $context['value'], $context['field'], $property );

		if ( is_array( $formatted ) ) {
			return isset( $formatted['url'] ) ? (string) $formatted['url'] : '';
		}

		return esc_url( (string) $formatted );
	}
}
