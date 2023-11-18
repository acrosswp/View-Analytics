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
		require_once( VIEW_ANALYTICS_PLUGIN_PATH . 'vendor/woocommerce/action-scheduler/action-scheduler.php' );

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
			viewer_id bigint(20) NOT NULL DEFAULT 0,
			key_id varchar(255) NOT NULL DEFAULT 0,
			hash_id varchar(255) NOT NULL DEFAULT 0,
			media_id bigint(20) NOT NULL DEFAULT 0,
			attachment_id bigint(20) NOT NULL DEFAULT 0,
			value bigint(20) NOT NULL DEFAULT 1,
			is_new tinyint(1) NOT NULL DEFAULT 1,
			last_date TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
			action_date TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (id)
		) {$charset_collate};";


		/**
		 * Profile View
		 */
		$profile_view_table_name		 = $wpdb->prefix . 'awp_va_profile_view';

		$profile_view_sql = "CREATE TABLE {$profile_view_table_name} (
			id bigint(20) NOT NULL AUTO_INCREMENT ,
			user_id bigint(20) NOT NULL,
			viewer_id bigint(20) NOT NULL,
			value bigint(20) NOT NULL DEFAULT 1,
			is_new tinyint(1) NOT NULL DEFAULT 1,
			last_date TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
			action_date TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (id)
		) {$charset_collate};";


		/**
		 * Group View
		 */
		$group_view_table_name		 = $wpdb->prefix . 'awp_va_group_view';

		$group_view_sql = "CREATE TABLE {$group_view_table_name} (
			id bigint(20) NOT NULL AUTO_INCREMENT ,
			group_id bigint(20) NOT NULL,
			viewer_id bigint(20) NOT NULL,
			value bigint(20) NOT NULL DEFAULT 1,
			is_new tinyint(1) NOT NULL DEFAULT 1,
			last_date TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
			action_date TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (id)
		) {$charset_collate};";


		/**
		 * Profile Fields View
		 */
		$avatar_view_table_name		 = $wpdb->prefix . 'awp_va_avatar_view';

		$avatar_view_sql = "CREATE TABLE {$avatar_view_table_name} (
			id 			bigint(20) NOT NULL AUTO_INCREMENT ,
			key_id		varchar(255) NULL,
			user_id 	bigint(20) NOT NULL,
			type		varchar(255) NULL,
			action		varchar(255) NULL,
			value		bigint(20) NOT NULL DEFAULT 1,
			is_new		tinyint(1) NOT NULL DEFAULT 1,
			last_date	TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
			action_date TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (id)
		) {$charset_collate};";


		maybe_create_table( $media_view_table_name, $media_view_sql );
		maybe_create_table( $profile_view_table_name, $profile_view_sql );
		maybe_create_table( $group_view_table_name, $group_view_sql );
		maybe_create_table( $avatar_view_table_name, $avatar_view_sql );


		/**
		 * For avatar
		 */
		if ( false === as_has_scheduled_action( '_view_analytics_update_xprofile_avatar' ) ) {
			as_schedule_single_action( strtotime( '+1 minutes' ), '_view_analytics_update_xprofile_avatar', array(), '', true );
		}

		if ( false === as_has_scheduled_action( '_view_analytics_update_group_avatar' ) ) {
			as_schedule_single_action( strtotime( '+1 minutes' ), '_view_analytics_update_group_avatar', array(), '', true );
		}

		/**
		 * For cover image
		 */
		if ( false === as_has_scheduled_action( '_view_analytics_update_xprofile_cover' ) ) {
			as_schedule_single_action( strtotime( '+1 minutes' ), '_view_analytics_update_xprofile_cover', array(), '', true );
		}

		if ( false === as_has_scheduled_action( '_view_analytics_update_group_cover' ) ) {
			as_schedule_single_action( strtotime( '+1 minutes' ), '_view_analytics_update_group_cover', array(), '', true );
		}
	}

}
