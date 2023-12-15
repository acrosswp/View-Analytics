<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Fired during plugin deactivation
 *
 * @link       https://acrosswp.com
 * @since      1.0.0
 *
 * @package    View_Analytics_Deactivator
 * @subpackage View_Analytics_Deactivator/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    View_Analytics_Deactivator
 * @subpackage View_Analytics_Deactivator/includes
 * @author     AcrossWP <contact@acrosswp.com>
 */
class View_Analytics_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		/**
		 * Create the Table
		 */
		View_Analytics_Deactivator::delete_table();

	}

	/**
	 * Delete table code
	 */
	public static function delete_table() {
		global $wpdb;

		require_once( VIEW_ANALYTICS_PLUGIN_PATH . 'includes/avatar/class-view-analytics-table.php' );
		View_Analytics_Avatar_Table::instance()->delete_table();

		require_once( VIEW_ANALYTICS_PLUGIN_PATH . 'includes/forum/class-view-analytics-table.php' );
		View_Analytics_Forum_Table::instance()->delete_table();

		require_once( VIEW_ANALYTICS_PLUGIN_PATH . 'includes/group/class-view-analytics-table.php' );
		View_Analytics_Group_Table::instance()->delete_table();

		require_once( VIEW_ANALYTICS_PLUGIN_PATH . 'includes/media/class-view-analytics-table.php' );
		View_Analytics_Media_Table::instance()->delete_table();

		require_once( VIEW_ANALYTICS_PLUGIN_PATH . 'includes/profile/class-view-analytics-table.php' );
		View_Analytics_Profile_Table::instance()->delete_table();
	
	}
}
