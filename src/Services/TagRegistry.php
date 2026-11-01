<?php
/**
 * Registers Elementor Dynamic Tags via a factory map.
 *
 * @package HushUX\ElementorAcfGroupFields
 */

declare(strict_types=1);

namespace HushUX\ElementorAcfGroupFields\Services;

use HushUX\ElementorAcfGroupFields\Tags\GalleryTag;
use HushUX\ElementorAcfGroupFields\Tags\ImageTag;
use HushUX\ElementorAcfGroupFields\Tags\MediaTag;
use HushUX\ElementorAcfGroupFields\Tags\TextTag;
use HushUX\ElementorAcfGroupFields\Tags\UrlTag;

final class TagRegistry {

	private bool $registered = false;

	public function __construct(
		private FieldDiscovery $discovery,
		private FieldResolver $resolver,
		private FieldFormatter $formatter
	) {}

	/**
	 * Elementor 3.5+ registration callback.
	 *
	 * @param \Elementor\Core\DynamicTags\Manager $manager
	 */
	public function register( $manager ): void {
		if ( $this->registered ) {
			return;
		}

		$this->registered = true;
		$this->bind_services_to_tags();

		foreach ( $this->tag_classes() as $class ) {
			$manager->register( new $class() );
		}

		add_action( 'acf/save_post', array( $this->discovery, 'flush' ), 99 );
		add_action( 'acf/update_field_group', array( $this->discovery, 'flush' ) );
	}

	/**
	 * Legacy Elementor registration (`register_tags`).
	 *
	 * @param \Elementor\Core\DynamicTags\Manager $dynamic_tags
	 */
	public function register_legacy( $dynamic_tags ): void {
		if ( $this->registered ) {
			return;
		}

		// Older Elementor used register_tag().
		if ( ! method_exists( $dynamic_tags, 'register_tag' ) ) {
			$this->register( $dynamic_tags );
			return;
		}

		$this->registered = true;
		$this->bind_services_to_tags();

		foreach ( $this->tag_classes() as $class ) {
			$dynamic_tags->register_tag( new $class() );
		}

		add_action( 'acf/save_post', array( $this->discovery, 'flush' ), 99 );
		add_action( 'acf/update_field_group', array( $this->discovery, 'flush' ) );
	}

	/**
	 * @return list<class-string>
	 */
	private function tag_classes(): array {
		$classes = array(
			TextTag::class,
			ImageTag::class,
			GalleryTag::class,
			UrlTag::class,
			MediaTag::class,
		);

		/**
		 * Filter registered dynamic tag classes.
		 *
		 * @param list<class-string> $classes
		 */
		return apply_filters( 'hux_eagf/tag_classes', $classes );
	}

	/**
	 * Inject shared services into tag base for use without service locator sprawl.
	 */
	private function bind_services_to_tags(): void {
		TextTag::set_services( $this->discovery, $this->resolver, $this->formatter );
		ImageTag::set_services( $this->discovery, $this->resolver, $this->formatter );
		GalleryTag::set_services( $this->discovery, $this->resolver, $this->formatter );
		UrlTag::set_services( $this->discovery, $this->resolver, $this->formatter );
		MediaTag::set_services( $this->discovery, $this->resolver, $this->formatter );
	}
}
