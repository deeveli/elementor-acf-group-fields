<?php
/**
 * Plugin Name:       Elementor ACF Group Fields
 * Plugin URI:        https://github.com/deeveli/elementor-acf-group-fields
 * Description:       Expose nested ACF Group, Repeater, Flexible Content, Clone, and related fields as Elementor Pro Dynamic Tags — with unlimited nesting depth.
 * Version:           2.0.0
 * Requires at least: 5.8
 * Requires PHP:      8.0
 * Author:            Divine Elikplim Abah (Hush User Xperience / Hush UX / HUX)
 * Author URI:        https://github.com/deeveli
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       elementor-acf-group-fields
 * Domain Path:       /languages
 *
 * @package HushUX\ElementorAcfGroupFields
 */

declare(strict_types=1);

namespace HushUX\ElementorAcfGroupFields;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'HUX_EAGF_VERSION', '2.0.0' );
define( 'HUX_EAGF_FILE', __FILE__ );
define( 'HUX_EAGF_PATH', plugin_dir_path( __FILE__ ) );
define( 'HUX_EAGF_URL', plugin_dir_url( __FILE__ ) );
define( 'HUX_EAGF_BASENAME', plugin_basename( __FILE__ ) );

$hux_eagf_autoload = HUX_EAGF_PATH . 'vendor/autoload.php';

if ( is_readable( $hux_eagf_autoload ) ) {
	require_once $hux_eagf_autoload;
} else {
	require_once HUX_EAGF_PATH . 'src/Autoloader.php';
	Autoloader::register( 'HushUX\\ElementorAcfGroupFields\\', HUX_EAGF_PATH . 'src/' );
}

Bootstrap::init();
