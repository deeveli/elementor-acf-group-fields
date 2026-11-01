<?php
/**
 * Boots the plugin after WordPress loads dependencies.
 *
 * @package HushUX\ElementorAcfGroupFields
 */

declare(strict_types=1);

namespace HushUX\ElementorAcfGroupFields;

use HushUX\ElementorAcfGroupFields\Helpers\Utils;

final class Bootstrap {

	private static bool $booted = false;

	/**
	 * Hook into WordPress.
	 */
	public static function init(): void {
		add_action( 'plugins_loaded', array( self::class, 'on_plugins_loaded' ), 20 );
	}

	/**
	 * Compatibility gate, then hand off to Plugin.
	 */
	public static function on_plugins_loaded(): void {
		if ( self::$booted ) {
			return;
		}

		load_plugin_textdomain(
			'elementor-acf-group-fields',
			false,
			dirname( HUX_EAGF_BASENAME ) . '/languages'
		);

		if ( ! self::dependencies_met() ) {
			return;
		}

		self::$booted = true;
		Plugin::instance()->boot();
	}

	/**
	 * Verify Elementor, Elementor Pro (dynamic tags), ACF, PHP.
	 */
	private static function dependencies_met(): bool {
		$ok = true;

		if ( version_compare( PHP_VERSION, '8.0', '<' ) ) {
			add_action(
				'admin_notices',
				static function (): void {
					Utils::admin_notice(
						sprintf(
							/* translators: 1: plugin name 2: PHP version */
							esc_html__( '"%1$s" requires PHP %2$s or greater.', 'elementor-acf-group-fields' ),
							'Elementor ACF Group Fields',
							'8.0'
						)
					);
				}
			);
			$ok = false;
		}

		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action(
				'admin_notices',
				static function (): void {
					Utils::admin_notice(
						sprintf(
							/* translators: 1: plugin name 2: dependency name */
							esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'elementor-acf-group-fields' ),
							'Elementor ACF Group Fields',
							'Elementor'
						)
					);
				}
			);
			$ok = false;
		}

		// Elementor Pro provides the ACF dynamic-tag module; detect by class or init action.
		$has_pro = class_exists( '\ElementorPro\Plugin' )
			|| did_action( 'elementor_pro/init' )
			|| class_exists( '\ElementorPro\Modules\DynamicTags\ACF\Module' );

		if ( ! $has_pro ) {
			add_action(
				'admin_notices',
				static function (): void {
					Utils::admin_notice(
						sprintf(
							/* translators: 1: plugin name 2: dependency name */
							esc_html__( '"%1$s" requires "%2$s" for Dynamic Tags support.', 'elementor-acf-group-fields' ),
							'Elementor ACF Group Fields',
							'Elementor Pro'
						)
					);
				}
			);
			$ok = false;
		}

		if ( ! class_exists( '\ACF' ) && ! function_exists( 'acf_get_field_groups' ) ) {
			add_action(
				'admin_notices',
				static function (): void {
					Utils::admin_notice(
						sprintf(
							/* translators: 1: plugin name 2: dependency name */
							esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'elementor-acf-group-fields' ),
							'Elementor ACF Group Fields',
							'Advanced Custom Fields'
						)
					);
				}
			);
			$ok = false;
		}

		return $ok;
	}
}
