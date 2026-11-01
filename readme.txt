=== Elementor ACF Group Fields ===
Contributors: hushux, divineabah
Tags: elementor, acf, dynamic tags, advanced custom fields, nested fields
Requires at least: 5.8
Tested up to: 6.7
Requires PHP: 8.0
Stable tag: 2.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Expose nested ACF Group, Repeater, Flexible Content, and Clone fields as Elementor Pro Dynamic Tags.

== Description ==

Elementor ACF Group Fields (by Divine Elikplim Abah / Hush User Xperience) recursively discovers nested Advanced Custom Fields and registers them as Elementor Pro Dynamic Tags.

Supports Groups, Repeaters, Flexible Content, Clones, Images, Galleries, Links, URLs, Relationships, Taxonomies, Users, Google Maps, and more — with unlimited practical nesting depth.

== Installation ==

1. Upload the plugin folder to `/wp-content/plugins/elementor-acf-group-fields`
2. Activate the plugin through the 'Plugins' menu
3. Ensure Elementor, Elementor Pro, and ACF are active
4. Use ACF Nested Fields / Image / Gallery / URL / Media dynamic tags in Elementor

== Frequently Asked Questions ==

= Does this replace Elementor's built-in ACF tags? =

No. It adds nested / grouped field discovery that core Elementor ACF tags do not fully cover.

= Are old tag names preserved? =

Yes. `acf_group_tag`, `acf_group_image_tag`, and `acf_group_gallery_tag` remain.

== Changelog ==

= 2.0.0 =
* Complete rewrite by Divine Elikplim Abah (Hush UX)
* Recursive Group / Repeater / Flexible Content / Clone support
* Expanded field type and property coverage

= 1.0.0 =
* Initial release (community)

== Upgrade Notice ==

= 2.0.0 =
Major rewrite with broader ACF support. Existing text/image/gallery tag names remain compatible.
