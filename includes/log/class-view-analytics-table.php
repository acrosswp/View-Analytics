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
class View_Analytics_Log_Table {

	/**
	 * The single instance of the class.
	 *
	 * @var View_Analytics_Log_Table
	 * @since 1.0.0
	 */
	protected static $_instance = null;

	/**
	 * Main View_Analytics_Log_Table Instance.
	 *
	 * Ensures only one instance of WooCommerce is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see View_Analytics_Log_Table()
	 * @return View_Analytics_Log_Table - Main instance.
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
		return $wpdb->prefix . 'awp_va_log';
    }

	/**
	 * Add the current user has view avatar count
	 */
	public function user_add( $action, $key_id, $user_id = 0, $viewer_id = 0, $type = '', $sub_type = '' ) {
		global $wpdb;

		return $wpdb->insert(
			$this->table_name(),
			array(
				'action' => $action,
				'key_id' => $key_id,
				'user_id' => $user_id,
				'viewer_id' => $viewer_id,
				'type' => $type,
				'sub_type' => $sub_type,
			),
			array(
				'%s',
				'%s',
				'%d',
				'%d',
				'%s',
				'%s',
			)
		);
	}

	/**
	 * Delete the current user has view avatar count
	 */
	public function user_delete( $key_id ) {
		global $wpdb;
		$wpdb->delete( $this->table_name(), array( 'viewer_id' => $key_id ), array( '%d' ) );
		$wpdb->delete( $this->table_name(), array( 'user_id' => $key_id ), array( '%d' ) );
	}
}
