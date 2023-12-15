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

		/**
		 * Main common class
		 */
		require_once( VIEW_ANALYTICS_PLUGIN_PATH . 'includes/class-view-analytics-common.php' );


		if ( View_Analytics_Common::instance()->get_view_setting_active( 'delete-tables' ) ) {
			

			require_once( VIEW_ANALYTICS_PLUGIN_PATH . 'includes/avatar/class-view-analytics-common.php' );
			require_once( VIEW_ANALYTICS_PLUGIN_PATH . 'includes/avatar/class-view-analytics-table.php' );
			View_Analytics_Avatar_Common::instance()->table->delete_table();

			require_once( VIEW_ANALYTICS_PLUGIN_PATH . 'includes/forum/class-view-analytics-common.php' );
			require_once( VIEW_ANALYTICS_PLUGIN_PATH . 'includes/forum/class-view-analytics-table.php' );
			View_Analytics_Forum_Common::instance()->table->delete_table();

			require_once( VIEW_ANALYTICS_PLUGIN_PATH . 'includes/group/class-view-analytics-common.php' );
			require_once( VIEW_ANALYTICS_PLUGIN_PATH . 'includes/group/class-view-analytics-table.php' );
			View_Analytics_Group_Common::instance()->table->delete_table();

			require_once( VIEW_ANALYTICS_PLUGIN_PATH . 'includes/media/class-view-analytics-common.php' );
			require_once( VIEW_ANALYTICS_PLUGIN_PATH . 'includes/media/class-view-analytics-table.php' );
			View_Analytics_Media_Common::instance()->table->delete_table();

			require_once( VIEW_ANALYTICS_PLUGIN_PATH . 'includes/profile/class-view-analytics-common.php' );
			require_once( VIEW_ANALYTICS_PLUGIN_PATH . 'includes/profile/class-view-analytics-table.php' );
			View_Analytics_Profile_Common::instance()->table->delete_table();
			
			/**
			 * Add the option to enable the all the setting if the plugin is activiating for the first time
			 */
			$default_active_keys = array(
				View_Analytics_Avatar_Common::instance()->view_count_key(),
				View_Analytics_Forum_Common::instance()->view_count_key(),
				View_Analytics_Group_Common::instance()->view_count_key(),
				View_Analytics_Media_Common::instance()->view_count_key(),
				View_Analytics_Profile_Common::instance()->view_count_key(),
				View_Analytics_Common::instance()->view_count_key(),
			);

			foreach( $default_active_keys as $key ) {	
				delete_option( $key );
			}
		}
	}
}
