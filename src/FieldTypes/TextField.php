<?php
/**
 * Scalar / choice text-like ACF fields.
 *
 * @package HushUX\ElementorAcfGroupFields
 */

declare(strict_types=1);

namespace HushUX\ElementorAcfGroupFields\FieldTypes;

use HushUX\ElementorAcfGroupFields\Helpers\Utils;

final class TextField extends AbstractFieldType {

	public function types(): array {
		return array(
			'text',
			'textarea',
			'number',
			'range',
			'email',
			'password',
			'wysiwyg',
			'select',
			'checkbox',
			'radio',
			'button_group',
			'true_false',
			'date_picker',
			'date_time_picker',
			'time_picker',
			'color_picker',
			'oembed',
		);
	}

	public function categories(): array {
		return array( 'text', 'post_meta' );
	}

	public function tag_kind(): string {
		return 'text';
	}

	public function format( mixed $value, array $field, string $property = '' ): mixed {
		$type = (string) ( $field['type'] ?? '' );

		return match ( $type ) {
			'radio', 'button_group' => $this->format_choice_label( $value, $field ),
			'select'                => $this->format_select( $value, $field ),
			'checkbox'              => $this->format_checkbox( $value, $field ),
			'true_false'            => ( $value ) ? '1' : '0',
			'color_picker'          => is_string( $value ) ? $value : Utils::stringify( $value ),
			'oembed'                => is_string( $value ) ? $value : Utils::stringify( $value ),
			default                 => Utils::stringify( $value ),
		};
	}

	/**
	 * @param mixed                $value
	 * @param array<string, mixed> $field
	 */
	private function format_choice_label( mixed $value, array $field ): string {
		$choices = is_array( $field['choices'] ?? null ) ? $field['choices'] : array();

		if ( is_scalar( $value ) && isset( $choices[ (string) $value ] ) ) {
			return (string) $choices[ (string) $value ];
		}

		return Utils::stringify( $value );
	}

	/**
	 * @param mixed                $value
	 * @param array<string, mixed> $field
	 */
	private function format_select( mixed $value, array $field ): string {
		$choices = is_array( $field['choices'] ?? null ) ? $field['choices'] : array();
		$values  = (array) $value;
		$labels  = array();

		foreach ( $values as $item ) {
			if ( is_array( $item ) ) {
				$labels[] = (string) ( $item['label'] ?? $item['value'] ?? '' );
				continue;
			}
			$key        = (string) $item;
			$labels[] = isset( $choices[ $key ] ) ? (string) $choices[ $key ] : $key;
		}

		return implode( ', ', array_filter( $labels, static fn( $l ) => $l !== '' ) );
	}

	/**
	 * @param mixed                $value
	 * @param array<string, mixed> $field
	 */
	private function format_checkbox( mixed $value, array $field ): string {
		$choices = is_array( $field['choices'] ?? null ) ? $field['choices'] : array();
		$labels  = array();

		foreach ( (array) $value as $item ) {
			if ( is_array( $item ) ) {
				$labels[] = (string) ( $item['label'] ?? $item['value'] ?? '' );
				continue;
			}
			$key = (string) $item;
			$labels[] = isset( $choices[ $key ] ) ? (string) $choices[ $key ] : $key;
		}

		return implode( ', ', array_filter( $labels, static fn( $l ) => $l !== '' ) );
	}
}
