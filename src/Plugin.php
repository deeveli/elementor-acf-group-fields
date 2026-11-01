<?php
/**
 * Main plugin orchestrator.
 *
 * @package HushUX\ElementorAcfGroupFields
 */

declare(strict_types=1);

namespace HushUX\ElementorAcfGroupFields;

use HushUX\ElementorAcfGroupFields\FieldTypes\FieldTypeManager;
use HushUX\ElementorAcfGroupFields\Services\FieldDiscovery;
use HushUX\ElementorAcfGroupFields\Services\FieldFormatter;
use HushUX\ElementorAcfGroupFields\Services\FieldResolver;
use HushUX\ElementorAcfGroupFields\Services\RecursiveWalker;
use HushUX\ElementorAcfGroupFields\Services\TagRegistry;

final class Plugin {

	private static ?self $instance = null;

	private FieldTypeManager $field_types;

	private RecursiveWalker $walker;

	private FieldDiscovery $discovery;

	private FieldResolver $resolver;

	private FieldFormatter $formatter;

	private TagRegistry $tag_registry;

	private function __construct() {
		$this->field_types  = new FieldTypeManager();
		$this->walker       = new RecursiveWalker( $this->field_types );
		$this->discovery    = new FieldDiscovery( $this->walker );
		$this->resolver     = new FieldResolver();
		$this->formatter    = new FieldFormatter( $this->field_types );
		$this->tag_registry = new TagRegistry( $this->discovery, $this->resolver, $this->formatter );
	}

	public static function instance(): self {
		return self::$instance ??= new self();
	}

	/**
	 * Wire Elementor hooks once dependencies are confirmed.
	 */
	public function boot(): void {
		$this->maybe_fix_acf_preview_post_id();

		add_action( 'elementor/dynamic_tags/register', array( $this->tag_registry, 'register' ) );
		// Elementor < 3.5 compatibility.
		add_action( 'elementor/dynamic_tags/register_tags', array( $this->tag_registry, 'register_legacy' ) );
	}

	public function discovery(): FieldDiscovery {
		return $this->discovery;
	}

	public function resolver(): FieldResolver {
		return $this->resolver;
	}

	public function formatter(): FieldFormatter {
		return $this->formatter;
	}

	public function field_types(): FieldTypeManager {
		return $this->field_types;
	}

	/**
	 * Preserve the original plugin's ACF preview fix for revision post IDs.
	 */
	private function maybe_fix_acf_preview_post_id(): void {
		if ( ! class_exists( 'acf_revisions' ) || ! function_exists( 'acf' ) ) {
			return;
		}

		$acf = acf();

		if ( ! isset( $acf->revisions ) || ! is_object( $acf->revisions ) ) {
			return;
		}

		remove_filter( 'acf/validate_post_id', array( $acf->revisions, 'acf_validate_post_id' ), 10 );
	}
}
