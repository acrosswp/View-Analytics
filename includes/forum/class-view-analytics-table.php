<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Register all actions and filters for the plugin
 *
 * @link       https://acrosswp.com
 * @since      1.0.0
 *
 * @package    View_Analytics
 * @subpackage View_Analytics/includes
 */

/**
 * Register all actions and filters for the plugin.
 *
 * Maintain a list of all hooks that are registered throughout
 * the plugin, and register them with the WordPress API. Call the
 * run function to execute the list of actions and filters.
 *
 * @package    View_Analytics
 * @subpackage View_Analytics/includes
 * @author     AcrossWP <contact@acrosswp.com>
 */
class View_Analytics_Forum_Table {

	/**
	 * The single instance of the class.
	 *
	 * @var View_Analytics_Forum_Table
	 * @since 1.0.0
	 */
	protected static $_instance = null;

	/**
	 * Main View_Analytics_Forum_Table Instance.
	 *
	 * Ensures only one instance of WooCommerce is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see View_Analytics_Forum_Table()
	 * @return View_Analytics_Forum_Table - Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
     * Return the View Analytics Media Count Ket
     */
    public function table_name() {
		global $wpdb;
		return $wpdb->prefix . 'awp_va_forum_view';
    }

	/**
     * Return the View Analytics Media Count Ket
     */
    public function table_name_log() {
		global $wpdb;
		return $wpdb->prefix . 'awp_va_forum_view_log';
    }

	/**
	 * Create table
	 */
	public function create_table() {

		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
		
		/**
		 * Profile View
		 */
		$table_name		 = $this->table_name();
		$view_sql = "CREATE TABLE {$table_name} (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			blog_id bigint(20) NULL,
			post_id bigint(20) NOT NULL,
			author_id bigint(20) NOT NULL,
			users_list longtext NULL,
			user_count bigint(20) NOT NULL DEFAULT 1,
			ref_count bigint(20) NOT NULL DEFAULT 1,
			session_count bigint(20) NOT NULL DEFAULT 1,
			is_new tinyint(1) NOT NULL DEFAULT 1,
			locale varchar(50) NOT NULL,
			PRIMARY KEY  (id)
		) {$charset_collate};";

		$table_name_log		 = $this->table_name_log();
		$view_sql_log = "CREATE TABLE {$table_name_log} (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			match_id bigint(20) NOT NULL,
			blog_id bigint(20) NULL,
			session varchar(255) NOT NULL DEFAULT '',
			post_id bigint(20) NOT NULL,
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

		maybe_create_table( $table_name, $view_sql );
		maybe_create_table( $table_name_log, $view_sql_log );	
	}

	/**
	 * Create table
	 */
	public function delete_table() {
		global $wpdb;

		/**
		 * profile and Profile view log
		 */
		$table_name		 = $this->table_name();
		$view_sql = "DROP TABLE IF EXISTS $table_name";

		$table_name_log		 = $this->table_name_log();
		$view_sql_log = "DROP TABLE IF EXISTS $table_name_log";

		$wpdb->query( $view_sql );
		$wpdb->query( $view_sql_log );

	}

	/**
	 * Add the current user has view forum count
	 */
	public function user_add( $post_id, $author_id, $viewer_id, $components, $is_new = 1 ) {
		global $wpdb;

		$add = $wpdb->insert(
			$this->table_name(),
			array( 
				'blog_id' => get_current_blog_id(),
				'post_id' => $post_id,
				'author_id' => $author_id,
				'users_list' => serialize( array( $viewer_id ) ),
				'is_new' => $is_new,
			),
			array(
				'%d',
				'%d',
				'%d',
				'%s',
				'%d',
			)
		);

		if( $add ) {
			$this->add_log( $wpdb->insert_id, $post_id, $viewer_id, $components );
		}

		return $add;
	}

	/**
	 * Get the current user has already view the media or not
	 */
	public function user_get( $viewer_id, $post_id, $session = false ) {
		global $wpdb;

		$table_name = $this->table_name_log();

		if ( $session ) {
			$session = View_Analytics_Common::instance()->wp_get_current_session();
			$sql = $wpdb->prepare( 
				"SELECT * FROM $table_name WHERE viewer_id = %d AND post_id = %s AND session = %s",
				$viewer_id,
				$post_id,
				$session,
			);
		} else {
			$sql = $wpdb->prepare( 
				"SELECT * FROM $table_name WHERE viewer_id = %d AND post_id = %s",
				$viewer_id,
				$post_id
			);
		}
		return $wpdb->get_results( $sql, ARRAY_A );
	}

	/**
	 * Update the current user has view forum count
	 */
	public function user_update( $id, $users_list, $user_count, $ref_count, $session_count, $viewer_id, $details = false, $components = array() ) {
		global $wpdb;

		$update = $wpdb->update(
			$this->table_name(),
			array(
				'users_list' => serialize( $users_list ),
				'user_count' => $user_count,
				'ref_count' => $ref_count,
				'session_count' => $session_count,
				'is_new' => 1,
			),
			array( 
				'id' => $id 
			),
			array( '%s', '%d', '%d', '%d', '%d' ),
			array( '%d' )
		);

		if ( 
			$update 
			&& ! empty( $details['author_id'] ) 
			) {
			$this->add_log( $id, $details['post_id'], $viewer_id, $components );
		}

		return $update;
	}

	/**
	 * Delete the current user has view forum count
	 */
	public function user_delete( $user_id ) {
		global $wpdb;
		$wpdb->delete( $this->table_name(), array( 'author_id' => $user_id ), array( '%d' ) );
		$wpdb->delete( $this->table_name(), array( 'viewer_id' => $user_id ), array( '%d' ) );
	}

	/**
	 * Get the forum view details via $user_id
	 */
	public function get_details( $post_id ) {
		global $wpdb;

		$table_name = $this->table_name();

		return $wpdb->get_row(
			$wpdb->prepare( 
				"SELECT * FROM {$table_name} WHERE post_id = %d",
				$post_id
			),
			ARRAY_A
		);
	}

	/**
	 * Add value in Log table
	 */
	public function add_log( $match_id, $post_id, $viewer_id, $components ) {
		global $wpdb;

		$device = wp_is_mobile() ? 'mobile' : 'desktop';
		$session = View_Analytics_Common::instance()->wp_get_current_session();

		$add = $wpdb->insert(
			$this->table_name_log(),
			array( 
				'blog_id' => get_current_blog_id(),
				'session' => $session,
				'match_id' => $match_id,
				'post_id' => $post_id,
				'viewer_id' => $viewer_id,
				'url' => $components['url'],
				'components' => $components['components'],
				'object' => $components['object'],
				'primitive' => $components['primitive'],
				'variable' => $components['variable'],
				'device' => $device,
				'locale' => get_user_locale(),
			),
			array(
				'%d',
				'%s',
				'%d',
				'%d',
				'%d',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
			)
		);
	}
}
