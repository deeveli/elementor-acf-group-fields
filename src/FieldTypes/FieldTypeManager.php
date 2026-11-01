<?php
/**
 * Registry of field-type handlers (no giant switch statements).
 *
 * @package HushUX\ElementorAcfGroupFields
 */

declare(strict_types=1);

namespace HushUX\ElementorAcfGroupFields\FieldTypes;

use HushUX\ElementorAcfGroupFields\Contracts\FieldTypeHandlerInterface;

final class FieldTypeManager {

	/** @var array<string, FieldTypeHandlerInterface> */
	private array $by_type = array();

	/** @var list<FieldTypeHandlerInterface> */
	private array $handlers = array();

	public function __construct() {
		$this->register_defaults();

		/**
		 * Allow third parties to register additional field-type handlers.
		 *
		 * @param FieldTypeManager $manager
		 */
		do_action( 'hux_eagf/register_field_types', $this );
	}

	public function register( FieldTypeHandlerInterface $handler ): void {
		$this->handlers[] = $handler;

		foreach ( $handler->types() as $type ) {
			$this->by_type[ $type ] = $handler;
		}
	}

	public function get( string $type ): ?FieldTypeHandlerInterface {
		return $this->by_type[ $type ] ?? null;
	}

	/**
	 * Fallback for unknown future ACF types — treat as text leaf.
	 */
	public function get_or_default( string $type ): FieldTypeHandlerInterface {
		return $this->by_type[ $type ] ?? $this->by_type['text'];
	}

	/**
	 * @return list<string>
	 */
	public function leaf_types_for_kind( string $kind ): array {
		$types = array();

		foreach ( $this->by_type as $type => $handler ) {
			if ( $handler->is_container() || $handler->is_layout_only() ) {
				continue;
			}
			if ( $handler->tag_kind() === $kind ) {
				$types[] = $type;
			}
		}

		/**
		 * Filter discovered leaf types for a tag kind.
		 *
		 * @param list<string> $types
		 * @param string       $kind
		 */
		return apply_filters( 'hux_eagf/leaf_types', $types, $kind );
	}

	private function register_defaults(): void {
		$this->register( new LayoutOnlyField() );
		$this->register( new GroupField() );
		$this->register( new RepeaterField() );
		$this->register( new FlexibleContentField() );
		$this->register( new CloneField() );
		$this->register( new TextField() );
		$this->register( new UrlField() );
		$this->register( new LinkField() );
		$this->register( new ImageField() );
		$this->register( new FileField() );
		$this->register( new GalleryField() );
		$this->register( new PostObjectField() );
		$this->register( new RelationshipField() );
		$this->register( new TaxonomyField() );
		$this->register( new UserField() );
		$this->register( new GoogleMapField() );
	}
}
