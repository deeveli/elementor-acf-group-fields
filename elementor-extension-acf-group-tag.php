<?php
/**
 * Legacy bootstrap (backwards compatibility).
 *
 * Older installs activated this filename. It now loads the modern plugin entry
 * and prevents double-initialization.
 *
 * Plugin Name:       Elementor ACF Group Fields Extension (Legacy Loader)
 * Description:       Legacy loader for Elementor ACF Group Fields. Prefer activating "Elementor ACF Group Fields".
 * Version:           2.0.0
 * Author:            Divine Elikplim Abah (Hush User Xperience / Hush UX / HUX)
 * Author URI:        https://github.com/deeveli
 * License:           GPL-2.0-or-later
 * Text Domain:       elementor-acf-group-fields
 *
 * @package HushUX\ElementorAcfGroupFields
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( defined( 'HUX_EAGF_FILE' ) ) {
	return;
}

require_once __DIR__ . '/elementor-acf-group-fields.php';
