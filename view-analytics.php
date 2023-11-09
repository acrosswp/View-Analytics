<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://acrosswp.com
 * @since             1.0.0
 * @package           View_Analytics
 *
 * @wordpress-plugin
 * Plugin Name:       View Analytics
 * Plugin URI:        https://acrosswp.com
 * Description:       View Analytics by AcrossWP
 * Version:           1.0.0
 * Author:            AcrossWP
 * Author URI:        https://acrosswp.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       view-analytics
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'VIEW_ANALYTICS_FILES', __FILE__ );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-view-analytics-activator.php
 */
function view_analytics_activate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-view-analytics-activator.php';
	View_Analytics_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-view-analytics-deactivator.php
 */
function view_analytics_deactivate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-view-analytics-deactivator.php';
	View_Analytics_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'view_analytics_activate' );
register_deactivation_hook( __FILE__, 'view_analytics_deactivate' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-view-analytics.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function view_analytics_run() {

	$plugin = View_Analytics::instance();

	/**
	 * Run this plugin on the plugins_loaded functions
	 */
	add_action( 'plugins_loaded', array( $plugin, 'run' ), 0 );

}
view_analytics_run();