<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Fired during plugin activation
 *
 * @link       https://acrosswp.com
 * @since      1.0.0
 *
 * @package    View_Analytics
 * @subpackage View_Analytics/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    View_Analytics
 * @subpackage View_Analytics/includes
 * @author     AcrossWP <contact@acrosswp.com>
 */
class View_Analytics_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		/**
		 * Add composer file
		 */
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		/**
		 * Create the Table
		 */
		View_Analytics_Activator::create_table();
	}

	/**
	 * Create the View Activity Table
	 */
	public static function create_table() {

		/**
		 * Main common class
		 */
		require_once( VIEW_ANALYTICS_PLUGIN_PATH . 'includes/class-view-analytics-common.php' );

		/**
		 * Contain all the value to edit/delete/remove the table row
		 */
		require_once( VIEW_ANALYTICS_PLUGIN_PATH . 'includes/avatar/class-view-analytics-common.php' );
		require_once( VIEW_ANALYTICS_PLUGIN_PATH . 'includes/avatar/class-view-analytics-table.php' );
		View_Analytics_Avatar_Common::instance()->table->create_table();

		require_once( VIEW_ANALYTICS_PLUGIN_PATH . 'includes/profile/class-view-analytics-common.php' );
		require_once( VIEW_ANALYTICS_PLUGIN_PATH . 'includes/profile/class-view-analytics-table.php' );
		View_Analytics_Profile_Common::instance()->table->create_table();


		require_once( VIEW_ANALYTICS_PLUGIN_PATH . 'includes/media/class-view-analytics-common.php' );
		require_once( VIEW_ANALYTICS_PLUGIN_PATH . 'includes/media/class-view-analytics-table.php' );
		View_Analytics_Media_Common::instance()->table->create_table();

		/**
		 * Add the option to enable the all the setting if the plugin is activiating for the first time
		 */
		$default_active_keys = array(
			View_Analytics_Avatar_Common::instance()->view_count_key() => View_Analytics_Avatar_Common::instance()->default_value(),
			View_Analytics_Media_Common::instance()->view_count_key() => View_Analytics_Media_Common::instance()->default_value(),
			View_Analytics_Profile_Common::instance()->view_count_key() => View_Analytics_Profile_Common::instance()->default_value(),
			View_Analytics_Common::instance()->view_count_key() => View_Analytics_Common::instance()->default_value(),
		);

		foreach( $default_active_keys as $key => $value ) {	
			if ( empty( get_option( $key, false ) ) ) {
				update_option( $key, $value );
			}
		}
	}

}
