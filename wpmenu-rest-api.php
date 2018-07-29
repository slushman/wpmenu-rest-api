<?php

/**
 * The plugin bootstrap file
 *
 * @link              https://www.slushman.com
 * @since             1.0.0
 * @package           Wpmenu_Rest_Api
 *
 * @wordpress-plugin
 * Plugin Name:       WPMenu REST API
 * Plugin URI:        https://www.github.com/slushman/wpmenu-rest-api
 * Description:       Adds REST endpoints for menus and menu locations.
 * Version:           1.0.2
 * Author:            slushman
 * Author URI:        https://www.slushman.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wpmenu-rest-api
 * Domain Path:       /languages
 */

use WpmenuRestApi\Includes as Inc;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) { die; }

/**
 * Set plugin constants.
 */
define( 'WPMRA_VERSION', '1.0.2' );

/**
 * Include the autoloader.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-autoloader.php';

/**
 * Activation and Deactivation Hooks.
 */
register_activation_hook( __FILE__, array( 'WpmenuRestApi\Includes\Activator', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'WpmenuRestApi\Includes\Deactivator', 'deactivate' ) );

/**
 * Initializes each class and adds the hooks action in each to init.
 *
 * @global 		$tout_social_buttons
 */
function wpmenu_rest_api_init() {

	$classes[] = new Inc\i18n();
	$classes[] = new Inc\Cors();
	$classes[] = new Inc\Endpoints();

	foreach ( $classes as $class ) {

		add_action( 'init', array( $class, 'hooks' ) );

	}

} // wpmenu_rest_api_init()

add_action( 'plugins_loaded', 'wpmenu_rest_api_init' );