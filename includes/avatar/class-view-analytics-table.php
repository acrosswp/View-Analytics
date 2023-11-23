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
class View_Analytics_Avatar_Table {

	/**
	 * The single instance of the class.
	 *
	 * @var View_Analytics_Avatar_Table
	 * @since 1.0.0
	 */
	protected static $_instance = null;

	/**
	 * Main View_Analytics_Avatar_Table Instance.
	 *
	 * Ensures only one instance of WooCommerce is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see View_Analytics_Avatar_Table()
	 * @return View_Analytics_Avatar_Table - Main instance.
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
		return $wpdb->prefix . 'awp_va_avatar_view_log';
    }

	/**
	 * Add the current user has view avatar count
	 */
	public function user_add( $key_id, $user_id, $type = 'xprofile', $action = 'avatar', $value = 1 ) {
		global $wpdb;

		return $wpdb->insert(
			$this->table_name(),
			array( 
				'blog_id' => get_current_blog_id(),
				'key_id' => $key_id,
				'user_id' => $user_id,
				'type' => $type,
				'action' => $action,
				'locale' => get_user_locale(),
			),
			array(
				'%d',
				'%d',
				'%d',
				'%s',
				'%s',
				'%s',
			)
		);
	}

	/**
	 * Get the current user has already view the avatar or not
	 */
	public function user_get( $key_id, $type = 'xprofile', $action = 'avatar' ) {
		global $wpdb;

		$table_name = $this->table_name();

		return $wpdb->get_row(
			$wpdb->prepare( 
				"SELECT * FROM $table_name WHERE key_id = %d AND type = %s AND action = %s",
				$key_id,
				$type,
				$action
			)
		);
	}

	/**
	 * Delete the current user has view avatar count
	 */
	public function user_delete( $key_id ) {
		global $wpdb;
		$wpdb->delete( $this->table_name(), array( 'key_id' => $key_id ), array( '%d' ) );
		$wpdb->delete( $this->table_name(), array( 'user_id' => $key_id ), array( '%d' ) );
	}

	/**
	 * Get the avatar view details via $user_id
	 */
	public function get_details( $key_id, $type = 'xprofile', $action = 'avatar' ) {
		global $wpdb;

		$table_name = $this->table_name();

		return $wpdb->get_results(
			$wpdb->prepare( 
				"SELECT * FROM $table_name WHERE key_id = %d AND type = %s AND action = %s",
				$key_id,
				$type,
				$action
			),
			ARRAY_A
		);
	}
}
