<?php
/**
 * ACF Post Object field.
 *
 * @package HushUX\ElementorAcfGroupFields
 */

declare(strict_types=1);

namespace HushUX\ElementorAcfGroupFields\FieldTypes;

use HushUX\ElementorAcfGroupFields\Helpers\Utils;
use WP_Post;

final class PostObjectField extends AbstractFieldType {

	public function types(): array {
		return array( 'post_object', 'page_link' );
	}

	public function categories(): array {
		return array( 'text', 'url', 'post_meta' );
	}

	public function tag_kind(): string {
		return 'text';
	}

	public function properties(): array {
		return array(
			'id'             => __( 'ID', 'elementor-acf-group-fields' ),
			'title'          => __( 'Title', 'elementor-acf-group-fields' ),
			'permalink'      => __( 'Permalink', 'elementor-acf-group-fields' ),
			'slug'           => __( 'Slug', 'elementor-acf-group-fields' ),
			'excerpt'        => __( 'Excerpt', 'elementor-acf-group-fields' ),
			'featured_image' => __( 'Featured Image URL', 'elementor-acf-group-fields' ),
			'author'         => __( 'Author', 'elementor-acf-group-fields' ),
			'date'           => __( 'Date', 'elementor-acf-group-fields' ),
		);
	}

	public function format( mixed $value, array $field, string $property = '' ): mixed {
		$post = $this->first_post( $value );

		if ( ! $post instanceof WP_Post ) {
			// page_link may return a raw URL.
			if ( ( $field['type'] ?? '' ) === 'page_link' && is_string( $value ) ) {
				return $property === '' || $property === 'permalink' ? esc_url( $value ) : '';
			}
			return '';
		}

		if ( $property === '' ) {
			$property = 'title';
		}

		return match ( $property ) {
			'id'             => (string) $post->ID,
			'title'          => get_the_title( $post ),
			'permalink'      => (string) get_permalink( $post ),
			'slug'           => (string) $post->post_name,
			'excerpt'        => get_the_excerpt( $post ),
			'featured_image' => (string) get_the_post_thumbnail_url( $post, 'full' ),
			'author'         => (string) get_the_author_meta( 'display_name', (int) $post->post_author ),
			'date'           => get_the_date( '', $post ),
			default          => Utils::stringify( $post->post_title ),
		};
	}

	/**
	 * @param mixed $value
	 */
	private function first_post( mixed $value ): ?WP_Post {
		if ( $value instanceof WP_Post ) {
			return $value;
		}

		if ( is_numeric( $value ) ) {
			$post = get_post( (int) $value );
			return $post instanceof WP_Post ? $post : null;
		}

		if ( is_array( $value ) ) {
			$first = reset( $value );
			return $this->first_post( $first );
		}

		return null;
	}
}
