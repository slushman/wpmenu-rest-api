=== WPMenu REST API ===
Contributors: slushman
Donate link: https://www.slushman.com
Tags: rest-api, rest, menu, menus
Requires at least: 4.7
Tested up to: 4.9.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Adds REST endpoints for menus and menu locations.

== Description ==

Adds REST endpoints for menus and menu locations. There are four endpoints added:

1. /menus - returns all menus.
2. /menus/$id - returns a specific menu with its menu items.
3. /menu-locations - returns all the menu locations.
4. /menu-locations/$slug - returns the menu and menu items assigned to a specific menu location.

The returned menu items are not nested. If you're using the WPMenu React component, it handles nesting while creating the output.

CSS classes for the menu items are returned both as an array and as a string. Your choice.


== Installation ==

1. Within the WordPress admin, go to Plugins > Add New
2. Click the "Upload Plugin" button
3. Click the "Choose File" button to select and upload the `wpmenu-rest-api.zip` file.
4. Activate the plugin once its uploaded.



== Frequently Asked Questions ==

= Is this just the WP API Menus plugin? =

This is a fork of that plugin. I needed some slightly different formatting returned and that plugin is no longer maintained. In addition to the slightly different formatting, I also added support for menu items that are custom items, categories, and taxonomies. I also added unit tests to ensure future changes don't break things. Props to 


= Does this plugin support the v1 REST API? =

No. That's not included in core, so there's no reason to support it.



== Changelog ==

= 1.0.0 =
* Initial version.



== Upgrade Notice ==

= 1.0.0 =
Initial version.