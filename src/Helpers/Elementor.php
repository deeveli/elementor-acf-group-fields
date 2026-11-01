<?php
/**
 * Elementor / Elementor Pro helpers.
 *
 * @package HushUX\ElementorAcfGroupFields
 */

declare(strict_types=1);

namespace HushUX\ElementorAcfGroupFields\Helpers;

use ElementorPro\Modules\DynamicTags\ACF\Module as AcfModule;
use HushUX\ElementorAcfGroupFields\Helpers\ACF;

final class Elementor {

	/**
	 * Elementor Dynamic Tags group slug for ACF.
	 */
	public static function acf_group(): string {
		if ( class_exists( AcfModule::class ) ) {
			return AcfModule::ACF_GROUP;
		}

		return 'acf';
	}

	/**
	 * Map logical category keys to Elementor constants when available.
	 *
	 * @param list<string> $categories Logical keys: text, url, image, gallery, media, post_meta.
	 * @return list<string>
	 */
	public static function map_categories( array $categories ): array {
		$map = array(
			'text'      => class_exists( AcfModule::class ) ? AcfModule::TEXT_CATEGORY : 'text',
			'url'       => class_exists( AcfModule::class ) ? AcfModule::URL_CATEGORY : 'url',
			'image'     => class_exists( AcfModule::class ) ? AcfModule::IMAGE_CATEGORY : 'image',
			'gallery'   => class_exists( AcfModule::class ) ? AcfModule::GALLERY_CATEGORY : 'gallery',
			'media'     => 'media',
			'post_meta' => class_exists( AcfModule::class ) ? AcfModule::POST_META_CATEGORY : 'post_meta',
			'color'     => 'color',
		);

		$out = array();

		foreach ( $categories as $category ) {
			$out[] = $map[ $category ] ?? $category;
		}

		return array_values( array_unique( $out ) );
	}

	/**
	 * Resolve ACF field object + meta key for a tag, compatible with Elementor Pro keys.
	 *
	 * @param \Elementor\Core\DynamicTags\Base_Tag $tag
	 * @return array{0: ?array<string, mixed>, 1: string}
	 */
	public static function get_tag_value_field( $tag ): array {
		$key = (string) $tag->get_settings( 'key' );

		if ( $key === '' ) {
			return array( null, '' );
		}

		$parsed   = Utils::parse_option_key( $key );
		$meta_key = $parsed['meta_key'];

		// When there is no property suffix, defer to Elementor Pro's helper for BC.
		if ( $parsed['property'] === '' && class_exists( AcfModule::class ) && method_exists( AcfModule::class, 'get_tag_value_field' ) ) {
			$result = AcfModule::get_tag_value_field( $tag );
			if ( is_array( $result ) ) {
				return $result;
			}
		}

		$field = null;

		if ( $meta_key !== '' && function_exists( 'get_field_object' ) ) {
			$object = get_field_object( $meta_key, ACF::get_current_object_id(), false, true );
			if ( is_array( $object ) ) {
				$field = $object;
			}
		}

		return array( $field, $meta_key );
	}
}
