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
		require_once( VIEW_ANALYTICS_PLUGIN_PATH . 'vendor/woocommerce/action-scheduler/action-scheduler.php' );


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
		
		$media_view_table_name		 = $wpdb->prefix . 'awp_va_media_view';
		$media_view_sql = "DROP TABLE IF EXISTS $media_view_table_name";

		$profile_view_table_name		 = $wpdb->prefix . 'awp_va_profile_view';
		$profile_view_sql = "DROP TABLE IF EXISTS $profile_view_table_name";

		$group_view_table_name		 = $wpdb->prefix . 'awp_va_group_view';
		$group_view_sql = "DROP TABLE IF EXISTS $group_view_table_name";


		$avatar_view_table_name		 = $wpdb->prefix . 'awp_va_avatar_view';
		$avatar_view_sql = "DROP TABLE IF EXISTS $avatar_view_table_name";


		$wpdb->query( $media_view_sql );
		$wpdb->query( $profile_view_sql );
		$wpdb->query( $group_view_sql );
		$wpdb->query( $avatar_view_sql );
	}
}
