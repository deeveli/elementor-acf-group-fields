<?php
/**
 * ACF User field.
 *
 * @package HushUX\ElementorAcfGroupFields
 */

declare(strict_types=1);

namespace HushUX\ElementorAcfGroupFields\FieldTypes;

use WP_User;

final class UserField extends AbstractFieldType {

	public function types(): array {
		return array( 'user' );
	}

	public function categories(): array {
		return array( 'text', 'url', 'image', 'post_meta' );
	}

	public function tag_kind(): string {
		return 'text';
	}

	public function properties(): array {
		return array(
			'display_name' => __( 'Display Name', 'elementor-acf-group-fields' ),
			'username'     => __( 'Username', 'elementor-acf-group-fields' ),
			'email'        => __( 'Email', 'elementor-acf-group-fields' ),
			'website'      => __( 'Website', 'elementor-acf-group-fields' ),
			'avatar'       => __( 'Avatar URL', 'elementor-acf-group-fields' ),
			'id'           => __( 'ID', 'elementor-acf-group-fields' ),
		);
	}

	public function format( mixed $value, array $field, string $property = '' ): mixed {
		$user = $this->first_user( $value );

		if ( ! $user instanceof WP_User ) {
			return '';
		}

		if ( $property === '' ) {
			$property = 'display_name';
		}

		return match ( $property ) {
			'id'           => (string) $user->ID,
			'username'     => (string) $user->user_login,
			'email'        => (string) $user->user_email,
			'website'      => (string) $user->user_url,
			'avatar'       => (string) get_avatar_url( $user->ID ),
			default        => (string) $user->display_name,
		};
	}

	/**
	 * @param mixed $value
	 */
	private function first_user( mixed $value ): ?WP_User {
		if ( $value instanceof WP_User ) {
			return $value;
		}

		if ( is_array( $value ) && isset( $value['ID'] ) ) {
			$user = get_user_by( 'id', (int) $value['ID'] );
			return $user instanceof WP_User ? $user : null;
		}

		if ( is_numeric( $value ) ) {
			$user = get_user_by( 'id', (int) $value );
			return $user instanceof WP_User ? $user : null;
		}

		if ( is_array( $value ) ) {
			$first = reset( $value );
			return $this->first_user( $first );
		}

		return null;
	}
}
