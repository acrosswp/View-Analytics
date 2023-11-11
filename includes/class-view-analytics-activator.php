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
		 * Create the Table
		 */
		View_Analytics_Activator::create_table();
	}

	/**
	 * Create the View Activity Table
	 */
	public static function create_table() {

		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		/**
		 * Media View
		 */
		$media_view_table_name		 = $wpdb->prefix . 'awp_va_media_view';

		$media_view_sql = "CREATE TABLE {$media_view_table_name} (
			id bigint(20) NOT NULL AUTO_INCREMENT ,
			user_id bigint(20) NOT NULL DEFAULT 0,
			media_id bigint(20) NOT NULL DEFAULT 0,
			attachment_id bigint(20) NOT NULL DEFAULT 0,
			value bigint(20) NOT NULL DEFAULT 1,
			action_date TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (id)
		) {$charset_collate};";


		/**
		 * Profile View
		 */
		$profile_view_table_name		 = $wpdb->prefix . 'awp_va_profile_view';

		$profile_view_sql = "CREATE TABLE {$profile_view_table_name} (
			id bigint(20) NOT NULL AUTO_INCREMENT ,
			user_id bigint(20) NOT NULL DEFAULT 0,
			viewer_id bigint(20) NOT NULL DEFAULT 0,
			value bigint(20) NOT NULL DEFAULT 1,
			action_date TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (id)
		) {$charset_collate};";


		maybe_create_table( $media_view_table_name, $media_view_sql );
		maybe_create_table( $profile_view_table_name, $profile_view_sql );
	}

}
