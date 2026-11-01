<?php
/**
 * Shared service access + controls for all dynamic tags.
 *
 * @package HushUX\ElementorAcfGroupFields
 */

declare(strict_types=1);

namespace HushUX\ElementorAcfGroupFields\Tags;

use Elementor\Controls_Manager;
use HushUX\ElementorAcfGroupFields\Helpers\Elementor as ElementorHelper;
use HushUX\ElementorAcfGroupFields\Services\FieldDiscovery;
use HushUX\ElementorAcfGroupFields\Services\FieldFormatter;
use HushUX\ElementorAcfGroupFields\Services\FieldResolver;

trait TagServicesTrait {

	protected static ?FieldDiscovery $discovery = null;

	protected static ?FieldResolver $resolver = null;

	protected static ?FieldFormatter $formatter = null;

	public static function set_services(
		FieldDiscovery $discovery,
		FieldResolver $resolver,
		FieldFormatter $formatter
	): void {
		self::$discovery = $discovery;
		self::$resolver  = $resolver;
		self::$formatter = $formatter;
	}

	public function get_group() {
		return ElementorHelper::acf_group();
	}

	/**
	 * @param list<string> $acf_types Empty array = do not filter by ACF type (use tag_kind only).
	 */
	protected function register_key_controls( array $acf_types, string $tag_kind ): void {
		$type_filter = $acf_types === array() ? null : $acf_types;

		$groups = self::$discovery
			? self::$discovery->control_options_for( $tag_kind, $type_filter )
			: array();

		$this->add_control(
			'key',
			array(
				'label'   => esc_html__( 'Key', 'elementor-acf-group-fields' ),
				'type'    => Controls_Manager::SELECT,
				'groups'  => $groups,
				'default' => '',
			)
		);

		$this->add_control(
			'repeater_row',
			array(
				'label'       => esc_html__( 'Repeater / Flexible Row', 'elementor-acf-group-fields' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => 0,
				'default'     => 0,
				'description' => esc_html__( '1-based row index when the field lives inside a Repeater or Flexible Content layout. Leave 0 to use ACF’s default (usually the first row / flattened key).', 'elementor-acf-group-fields' ),
			)
		);
	}

	/**
	 * @return array{field: ?array<string, mixed>, value: mixed, meta_key: string, property: string}
	 */
	protected function resolve_context(): array {
		if ( ! self::$resolver ) {
			return array(
				'field'    => null,
				'value'    => null,
				'meta_key' => '',
				'property' => '',
			);
		}

		return self::$resolver->resolve( $this );
	}

	/**
	 * @param mixed                     $value
	 * @param array<string, mixed>|null $field
	 * @return mixed
	 */
	protected function format_value( mixed $value, ?array $field, string $property = '' ): mixed {
		if ( ! self::$formatter ) {
			return $value;
		}

		return self::$formatter->format( $value, $field, $property );
	}
}
