<?php
/**
 * ACF Relationship field (multiple posts).
 *
 * @package HushUX\ElementorAcfGroupFields
 */

declare(strict_types=1);

namespace HushUX\ElementorAcfGroupFields\FieldTypes;

use WP_Post;

final class RelationshipField extends AbstractFieldType {

	public function types(): array {
		return array( 'relationship' );
	}

	public function categories(): array {
		return array( 'text', 'url', 'post_meta' );
	}

	public function tag_kind(): string {
		return 'text';
	}

	public function properties(): array {
		return array(
			'titles' => __( 'Titles', 'elementor-acf-group-fields' ),
			'urls'   => __( 'URLs', 'elementor-acf-group-fields' ),
			'ids'    => __( 'IDs', 'elementor-acf-group-fields' ),
		);
	}

	public function format( mixed $value, array $field, string $property = '' ): mixed {
		$posts = $this->collect_posts( $value );

		if ( $property === '' ) {
			$property = 'titles';
		}

		$parts = array();

		foreach ( $posts as $post ) {
			$parts[] = match ( $property ) {
				'ids'    => (string) $post->ID,
				'urls'   => (string) get_permalink( $post ),
				default  => get_the_title( $post ),
			};
		}

		return implode( ', ', array_filter( $parts ) );
	}

	/**
	 * @param mixed $value
	 * @return list<WP_Post>
	 */
	private function collect_posts( mixed $value ): array {
		$posts = array();

		foreach ( (array) $value as $item ) {
			if ( $item instanceof WP_Post ) {
				$posts[] = $item;
				continue;
			}
			if ( is_numeric( $item ) ) {
				$post = get_post( (int) $item );
				if ( $post instanceof WP_Post ) {
					$posts[] = $post;
				}
			}
		}

		return $posts;
	}
}
