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
		 * Contain all the value to edit/delete/remove the table row
		 */
		require_once( VIEW_ANALYTICS_PLUGIN_PATH . 'includes/avatar/class-view-analytics-table.php' );
		View_Analytics_Avatar_Table::instance()->create_table();

		require_once( VIEW_ANALYTICS_PLUGIN_PATH . 'includes/forum/class-view-analytics-table.php' );
		View_Analytics_Forum_Table::instance()->create_table();


		require_once( VIEW_ANALYTICS_PLUGIN_PATH . 'includes/group/class-view-analytics-table.php' );
		View_Analytics_Profile_Table::instance()->create_table();


		require_once( VIEW_ANALYTICS_PLUGIN_PATH . 'includes/profile/class-view-analytics-table.php' );
		View_Analytics_Group_Table::instance()->create_table();


		require_once( VIEW_ANALYTICS_PLUGIN_PATH . 'includes/media/class-view-analytics-table.php' );
		View_Analytics_Media_Table::instance()->create_table();

		/**
		 * Add the option to enable the all the setting if the plugin is activiating for the first time
		 */
		$default_active_keys = array(
			'_view_analytics_media_table_count_enable' => array(
				'main' => 1,
				'show_view_user_list' => 1,
				'show_view_count' => 1,
			),
			'_view_analytics_profile_table_count_enable' => array(
				'main' => 1,
				'show_view_count' => 0,
			),
			'_view_analytics_group_table_count_enable' => array(
				'main' => 1,
				'show_view_count' => 0,
			),
			'_view_analytics_avatar_table_count_enable' => array(
				'main' => 1,
				'show_view_count_group_cover' => 0,
				'show_view_count_group_avatar' => 0,
				'show_view_count_profile_cover' => 0,
				'show_view_count_profile_avatar' => 0,
			),
			'_view_analytics_forum_table_count_enable' => array(
				'main' => 1,
			),
			'_view_analytics_delete_table_key' => array(
				'main' => 0,
			),
		);

		foreach( $default_active_keys as $key => $value ) {	
			if ( empty( get_option( $key, false ) ) ) {
				update_option( $key, $value );
			}
		}
	}

}
