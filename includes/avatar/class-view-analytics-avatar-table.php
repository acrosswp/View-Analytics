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
		return $wpdb->prefix . 'awp_va_avatar_view';
    }

	/**
	 * Add the current user has view avatar count
	 */
	public function user_add( $key_id, $user_id, $action = 'user', $value = 1 ) {
		global $wpdb;

		return $wpdb->insert(
			$this->table_name(),
			array( 
				'key_id' => $key_id,
				'user_id' => $user_id,
				'action' => $action,
				'value' => $value,
			),
			array(
				'%d',
				'%d',
				'%s',
				'%d',
			)
		);
	}

	/**
	 * Get the current user has already view the avatar or not
	 */
	public function user_get( $key_id, $action = 'user' ) {
		global $wpdb;

		$table_name = $this->table_name();

		return $wpdb->get_row(
			$wpdb->prepare( 
				"SELECT * FROM $table_name WHERE key_id = %d AND action = %s",
				$key_id,
				$action
			)
		);
	}

	/**
	 * Update the current user has view avatar count
	 */
	public function user_update( $id, $value, $mysql_time = false ) {
		global $wpdb;

		if ( empty( $mysql_time ) ) {
			$mysql_time = $wpdb->get_var( 'select CURRENT_TIMESTAMP()' );
		}

		$wpdb->update(
			$this->table_name(),
			array(
				'last_date' => $mysql_time,
				'value' => $value,
			),
			array( 
				'id' => $id 
			),
			array( '%s','%d' ),
			array( '%d' )
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
	public function get_details( $key_id, $action = 'user' ) {
		global $wpdb;

		$table_name = $this->table_name();

		return $wpdb->get_results(
			$wpdb->prepare( 
				"SELECT * FROM $table_name WHERE key_id = %d AND action = %s",
				$key_id,
				$action
			)
		);
	}
}