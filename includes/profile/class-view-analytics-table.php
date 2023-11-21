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
class View_Analytics_Profile_Table {

	/**
	 * The single instance of the class.
	 *
	 * @var View_Analytics_Profile_Table
	 * @since 1.0.0
	 */
	protected static $_instance = null;

	/**
	 * Main View_Analytics_Profile_Table Instance.
	 *
	 * Ensures only one instance of WooCommerce is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see View_Analytics_Profile_Table()
	 * @return View_Analytics_Profile_Table - Main instance.
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
		return $wpdb->prefix . 'awp_va_profile_view';
    }

	/**
	 * Add the current user has view profile count
	 */
	public function user_add( $user_id, $viewer_id, $components, $is_new = 1 ) {
		global $wpdb;

		$add = $wpdb->insert(
			$this->table_name(),
			array( 
				'user_id' => $user_id,
				'viewer_id' => $viewer_id,
				'url' => $components['url'],
				'components' => $components['components'],
				'object' => $components['object'],
				'primitive' => $components['primitive'],
				'variable' => $components['variable'],
				'is_new' => $is_new,
			),
			array(
				'%d',
				'%d',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%d',
			)
		);

		return $add;
	}

	/**
	 * Get the current user has already view the profile or not
	 */
	public function user_get( $user_id, $viewer_id ) {
		global $wpdb;

		$table_name = $this->table_name();

		return $wpdb->get_row(
			$wpdb->prepare( 
				"SELECT * FROM $table_name WHERE user_id = %d AND viewer_id = %d",
				$user_id,
				$viewer_id
			)
		);
	}

	/**
	 * Update the current user has view profile count
	 */
	public function user_update( $id, $is_new ) {
		global $wpdb;

		$update = $wpdb->update(
			$this->table_name(),
			array(
				'value' => $value,
				'is_new' => $is_new,
			),
			array( 
				'id' => $id 
			),
			array( '%d','%d' ),
			array( '%d' )
		);

		return $update;
	}

	/**
	 * Delete the current user has view profile count
	 */
	public function user_delete( $user_id ) {
		global $wpdb;
		$wpdb->delete( $this->table_name(), array( 'user_id' => $user_id ), array( '%d' ) );
		$wpdb->delete( $this->table_name(), array( 'viewer_id' => $user_id ), array( '%d' ) );
	}

	/**
	 * Get the profile view details via $user_id
	 */
	public function get_details( $user_id ) {
		global $wpdb;

		$table_name = $this->table_name();

		return $wpdb->get_results(
			$wpdb->prepare( 
				"SELECT 
					DISTINCT viewer_id,
					MIN(action_date) as first_action_date,
					COUNT(*) as view_count
				FROM {$table_name}
				WHERE user_id = %d
				GROUP BY viewer_id
				ORDER BY first_action_date DESC;",
				$user_id
			)
		);
	}
}
