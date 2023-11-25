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

		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();


		/**
		 * Profile Update View
		 */
		$avatar_view_table_name		 = $wpdb->prefix . 'awp_va_avatar_view_log';

		$avatar_view_sql = "CREATE TABLE {$avatar_view_table_name} (
			id 			bigint(20) NOT NULL AUTO_INCREMENT,
			blog_id bigint(20) NULL,
			key_id		varchar(255) NULL,
			user_id 	bigint(20) NOT NULL,
			type		varchar(255) NULL,
			action		varchar(255) NULL,
			is_new		tinyint(1) NOT NULL DEFAULT 1,
			locale varchar(50) NOT NULL,
			device varchar(50) NOT NULL DEFAULT 'desktop',
			action_date TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (id)
		) {$charset_collate};";


		/**
		 * Forum View
		 */
		$forum_view_table_name		 = $wpdb->prefix . 'awp_va_forum_view';

		$forum_view_sql = "CREATE TABLE {$forum_view_table_name} (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			blog_id bigint(20) NULL,
			post_id bigint(20) NOT NULL,
			author_id bigint(20) NOT NULL,
			viewer_id bigint(20) NOT NULL,
			value bigint(20) NOT NULL DEFAULT 1,
			is_new tinyint(1) NOT NULL DEFAULT 1,
			locale varchar(50) NOT NULL,
			last_date TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
			action_date TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (id)
		) {$charset_collate};";

		$forum_view_table_name_log		 = $wpdb->prefix . 'awp_va_forum_view_log';
		$forum_view_sql_log = "CREATE TABLE {$forum_view_table_name_log} (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			match_id bigint(20) NOT NULL,
			blog_id bigint(20) NULL,
			post_id bigint(20) NOT NULL,
			author_id bigint(20) NOT NULL,
			viewer_id bigint(20) NOT NULL,
			url varchar(255) NOT NULL DEFAULT '',
			components varchar(255) NULL DEFAULT '',
			object varchar(255) NULL DEFAULT '',
			primitive varchar(255) NULL DEFAULT '',
			variable varchar(255) NULL DEFAULT '',
			locale varchar(50) NOT NULL,
			device varchar(50) NOT NULL DEFAULT 'desktop',
			action_date TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (id)
		) {$charset_collate};";


		/**
		 * Group View
		 */
		$group_view_table_name		 = $wpdb->prefix . 'awp_va_group_view';

		$group_view_sql = "CREATE TABLE {$group_view_table_name} (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			blog_id bigint(20) NULL,
			group_id bigint(20) NOT NULL,
			viewer_id bigint(20) NOT NULL,
			value bigint(20) NOT NULL DEFAULT 1,
			is_new tinyint(1) NOT NULL DEFAULT 1,
			locale varchar(50) NOT NULL,
			last_date TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
			action_date TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (id)
		) {$charset_collate};";

		/**
		 * Group View Log
		 */
		$group_view_table_name_log		 = $wpdb->prefix . 'awp_va_group_view_log';

		$group_view_sql_log = "CREATE TABLE {$group_view_table_name_log} (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			match_id bigint(20) NOT NULL,
			blog_id bigint(20) NULL,
			group_id bigint(20) NOT NULL,
			viewer_id bigint(20) NOT NULL,
			url varchar(255) NOT NULL DEFAULT '',
			components varchar(255) NULL DEFAULT '',
			object varchar(255) NULL DEFAULT '',
			primitive varchar(255) NULL DEFAULT '',
			variable varchar(255) NULL DEFAULT '',
			device varchar(50) NOT NULL DEFAULT 'desktop',
			locale varchar(50) NOT NULL,
			action_date TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (id)
		) {$charset_collate};";


		/**
		 * Media View
		 */
		$media_view_table_name		 = $wpdb->prefix . 'awp_va_media_view';

		$media_view_sql = "CREATE TABLE {$media_view_table_name} (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			blog_id bigint(20) NULL,
			author_id bigint(20) NOT NULL DEFAULT 0,
			viewer_id bigint(20) NOT NULL DEFAULT 0,
			key_id varchar(255) NOT NULL DEFAULT 0,
			hash_id varchar(255) NOT NULL DEFAULT 0,
			media_id bigint(20) NOT NULL DEFAULT 0,
			attachment_id bigint(20) NOT NULL DEFAULT 0,
			value bigint(20) NOT NULL DEFAULT 1,
			type varchar(50) NOT NULL DEFAULT 'photo',
			mime_type varchar(50) NOT NULL DEFAULT '',
			is_new tinyint(1) NOT NULL DEFAULT 1,
			locale varchar(50) NOT NULL,
			last_date TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
			action_date TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (id)
		) {$charset_collate};";

		$media_view_table_name_log		 = $wpdb->prefix . 'awp_va_media_view_log';

		$media_view_sql_log = "CREATE TABLE {$media_view_table_name_log} (
			id bigint(20) NOT NULL AUTO_INCREMENT ,
			match_id bigint(20) NOT NULL DEFAULT 0,
			blog_id bigint(20) NULL,
			author_id bigint(20) NOT NULL DEFAULT 0,
			viewer_id bigint(20) NOT NULL DEFAULT 0,
			key_id varchar(255) NOT NULL DEFAULT 0,
			type varchar(50) NOT NULL DEFAULT 'photo',
			mime_type varchar(50) NOT NULL DEFAULT '',
			url varchar(255) NOT NULL DEFAULT '',
			site_components varchar(255) NULL DEFAULT '',
			components varchar(255) NULL DEFAULT '',
			object varchar(255) NULL DEFAULT '',
			primitive varchar(255) NULL DEFAULT '',
			variable varchar(255) NULL DEFAULT '',
			device varchar(50) NOT NULL DEFAULT 'desktop',
			locale varchar(50) NOT NULL,
			action_date TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (id)
		) {$charset_collate};";


		/**
		 * Profile View
		 */
		$profile_view_table_name		 = $wpdb->prefix . 'awp_va_profile_view';

		$profile_view_sql = "CREATE TABLE {$profile_view_table_name} (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			blog_id bigint(20) NULL,
			author_id bigint(20) NOT NULL,
			viewer_id bigint(20) NOT NULL,
			value bigint(20) NOT NULL DEFAULT 1,
			is_new tinyint(1) NOT NULL DEFAULT 1,
			locale varchar(50) NOT NULL,
			last_date TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
			action_date TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (id)
		) {$charset_collate};";

		$profile_view_table_name_log		 = $wpdb->prefix . 'awp_va_profile_view_log';
		$profile_view_sql_log = "CREATE TABLE {$profile_view_table_name_log} (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			match_id bigint(20) NOT NULL,
			blog_id bigint(20) NULL,
			author_id bigint(20) NOT NULL,
			viewer_id bigint(20) NOT NULL,
			url varchar(255) NOT NULL DEFAULT '',
			components varchar(255) NULL DEFAULT '',
			object varchar(255) NULL DEFAULT '',
			primitive varchar(255) NULL DEFAULT '',
			variable varchar(255) NULL DEFAULT '',
			device varchar(50) NOT NULL DEFAULT 'desktop',
			locale varchar(50) NOT NULL,
			action_date TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (id)
		) {$charset_collate};";


		maybe_create_table( $avatar_view_table_name, $avatar_view_sql );

		maybe_create_table( $forum_view_table_name, $forum_view_sql );
		maybe_create_table( $forum_view_table_name_log, $forum_view_sql_log );	

		maybe_create_table( $group_view_table_name, $group_view_sql );
		maybe_create_table( $group_view_table_name_log, $group_view_sql_log );

		maybe_create_table( $media_view_table_name, $media_view_sql );
		maybe_create_table( $media_view_table_name_log, $media_view_sql_log );

		maybe_create_table( $profile_view_table_name, $profile_view_sql );
		maybe_create_table( $profile_view_table_name_log, $profile_view_sql_log );		
	
	}

}
