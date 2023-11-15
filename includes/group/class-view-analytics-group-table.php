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
class View_Analytics_Group_Table {

	/**
	 * The single instance of the class.
	 *
	 * @var View_Analytics_Group_Table
	 * @since 1.0.0
	 */
	protected static $_instance = null;

	/**
	 * Main View_Analytics_Group_Table Instance.
	 *
	 * Ensures only one instance of WooCommerce is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see View_Analytics_Group_Table()
	 * @return View_Analytics_Group_Table - Main instance.
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
		return $wpdb->prefix . 'awp_va_group_view';
    }

	/**
	 * Add the current user has view group count
	 */
	public function user_group_add( $group_id, $viewer_id, $value = 1 ) {
		global $wpdb;

		return $wpdb->insert(
			$this->table_name(),
			array( 
				'group_id' => $group_id,
				'viewer_id' => $viewer_id,
				'value' => $value,
			),
			array(
				'%d',
				'%d',
				'%d',
			)
		);
	}

	/**
	 * Get the current user has already view the group or not
	 */
	public function user_group_get( $group_id, $viewer_id ) {
		global $wpdb;

		$table_name = $this->table_name();

		return $wpdb->get_row(
			$wpdb->prepare( 
				"SELECT * FROM $table_name WHERE group_id = %d AND viewer_id = %d",
				$group_id,
				$viewer_id
			)
		);
	}

	/**
	 * Update the current user has view group count
	 */
	public function user_group_update( $id, $value ) {
		global $wpdb;
		$wpdb->update(
			$this->table_name(),
			array(
				'value' => $value,
			),
			array( 
				'id' => $id 
			),
			array( '%d' ),
			array( '%d' )
		);
	}

	/**
	 * Delete the current user has view group count
	 */
	public function user_group_delete( $group_id ) {
		global $wpdb;
		$wpdb->delete( $this->table_name(), array( 'group_id' => $group_id ), array( '%d' ) );
	}

	/**
	 * Get the group view details via $group_id
	 */
	public function group_get_details( $group_id ) {
		global $wpdb;

		$table_name = $this->table_name();

		return $wpdb->get_results(
			$wpdb->prepare( 
				"SELECT * FROM $table_name WHERE group_id = %d",
				$group_id
			)
		);
	}
}
