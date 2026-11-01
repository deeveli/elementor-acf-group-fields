<?php
/**
 * ACF Taxonomy field.
 *
 * @package HushUX\ElementorAcfGroupFields
 */

declare(strict_types=1);

namespace HushUX\ElementorAcfGroupFields\FieldTypes;

use WP_Term;

final class TaxonomyField extends AbstractFieldType {

	public function types(): array {
		return array( 'taxonomy' );
	}

	public function categories(): array {
		return array( 'text', 'url', 'post_meta' );
	}

	public function tag_kind(): string {
		return 'text';
	}

	public function properties(): array {
		return array(
			'name'        => __( 'Name', 'elementor-acf-group-fields' ),
			'slug'        => __( 'Slug', 'elementor-acf-group-fields' ),
			'description' => __( 'Description', 'elementor-acf-group-fields' ),
			'archive_url' => __( 'Archive URL', 'elementor-acf-group-fields' ),
			'id'          => __( 'ID', 'elementor-acf-group-fields' ),
		);
	}

	public function format( mixed $value, array $field, string $property = '' ): mixed {
		$term = $this->first_term( $value );

		if ( ! $term instanceof WP_Term ) {
			return '';
		}

		if ( $property === '' ) {
			$property = 'name';
		}

		return match ( $property ) {
			'id'          => (string) $term->term_id,
			'slug'        => (string) $term->slug,
			'description' => (string) $term->description,
			'archive_url' => (string) get_term_link( $term ),
			default       => (string) $term->name,
		};
	}

	/**
	 * @param mixed $value
	 */
	private function first_term( mixed $value ): ?WP_Term {
		if ( $value instanceof WP_Term ) {
			return $value;
		}

		if ( is_numeric( $value ) ) {
			$term = get_term( (int) $value );
			return $term instanceof WP_Term ? $term : null;
		}

		if ( is_array( $value ) ) {
			$first = reset( $value );
			return $this->first_term( $first );
		}

		return null;
	}
}
