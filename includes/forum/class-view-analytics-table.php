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
				'viewer_id' => $viewer_id,
				'is_new' => $is_new,
			),
			array(
				'%d',
				'%d',
				'%d',
				'%d',
				'%d',
			)
		);

		if( $add ) {
			$this->add_log( $wpdb->insert_id, $post_id, $author_id, $viewer_id, $components );
		}

		return $add;
	}

	/**
	 * Get the current user has already view the forum or not
	 */
	public function user_get( $post_id, $viewer_id ) {
		global $wpdb;

		$table_name = $this->table_name();

		return $wpdb->get_row(
			$wpdb->prepare( 
				"SELECT * FROM $table_name WHERE post_id = %d AND viewer_id = %d",
				$post_id,
				$viewer_id
			)
		);
	}

	/**
	 * Update the current user has view forum count
	 */
	public function user_update( $id, $value, $post_id, $author_id, $viewer_id, $components, $is_new = 1, $mysql_time = false ) {
		global $wpdb;


		if ( empty( $mysql_time ) ) {
			$mysql_time = $wpdb->get_var( 'select CURRENT_TIMESTAMP()' );
		}

		$update = $wpdb->update(
			$this->table_name(),
			array(
				'value' => absint( $value ),
				'is_new' => absint( $is_new ),
				'last_date' => $mysql_time,
			),
			array( 
				'id' => absint( $id ) 
			),
			array( 
				'%d',
				'%d',
				'%s',
			),
			array( '%d' )
		);

		if( $update ) {
			$this->add_log( $id, $post_id, $author_id, $viewer_id, $components );
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

		return $wpdb->get_results(
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
	public function add_log( $match_id, $post_id, $author_id, $viewer_id, $components ) {
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
				'author_id' => $author_id,
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
