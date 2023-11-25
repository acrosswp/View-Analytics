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

		/**
		 * Avatar and cover image update of Profile and Group
		 */
		$avatar_view_table_name		 = $wpdb->prefix . 'awp_va_avatar_view_log';
		$avatar_view_sql = "DROP TABLE IF EXISTS $avatar_view_table_name";

		/**
		 * Forum View
		 */
		$forum_view_table_name_main		 = $wpdb->prefix . 'awp_va_forum_view';
		$forum_view_sql_main = "DROP TABLE IF EXISTS $forum_view_table_name_main";

		$forum_view_table_name		 = $wpdb->prefix . 'awp_va_forum_view_log';
		$forum_view_sql = "DROP TABLE IF EXISTS $forum_view_table_name";

		/**
		 * Group and Group View Log
		 */
		$group_view_table_name_main		 = $wpdb->prefix . 'awp_va_group_view';
		$group_view_sql_main = "DROP TABLE IF EXISTS $group_view_table_name_main";

		$group_view_table_name		 = $wpdb->prefix . 'awp_va_group_view_log';
		$group_view_sql = "DROP TABLE IF EXISTS $group_view_table_name";
		
		/**
		 * Media and Media View Log
		 */
		$media_view_table_name_main		 = $wpdb->prefix . 'awp_va_media_view';
		$media_view_sql_main = "DROP TABLE IF EXISTS $media_view_table_name_main";

		$media_view_table_name		 = $wpdb->prefix . 'awp_va_media_view_log';
		$media_view_sql = "DROP TABLE IF EXISTS $media_view_table_name";


		/**
		 * profile and Profile view log
		 */
		$profile_view_table_name_main		 = $wpdb->prefix . 'awp_va_profile_view';
		$profile_view_sql_main = "DROP TABLE IF EXISTS $profile_view_table_name_main";

		$profile_view_table_name		 = $wpdb->prefix . 'awp_va_profile_view_log';
		$profile_view_sql = "DROP TABLE IF EXISTS $profile_view_table_name";


		/**
		 * We are not using this table any more
		 */
		$over_all_log_table_name		 = $wpdb->prefix . 'awp_va_log';
		$over_all_log_sql = "DROP TABLE IF EXISTS $over_all_log_table_name";

		$avatar_view_table_name_main		 = $wpdb->prefix . 'awp_va_avatar_view';
		$avatar_view_sql_main = "DROP TABLE IF EXISTS $avatar_view_table_name_main";

		$wpdb->query( $avatar_view_sql );
		$wpdb->query( $avatar_view_sql_main );

		$wpdb->query( $forum_view_sql_main );
		$wpdb->query( $forum_view_sql );

		$wpdb->query( $group_view_sql_main );
		$wpdb->query( $group_view_sql );

		$wpdb->query( $media_view_sql_main );
		$wpdb->query( $media_view_sql );

		$wpdb->query( $profile_view_sql_main );
		$wpdb->query( $profile_view_sql );
		
		$wpdb->query( $over_all_log_sql );
	}
}
