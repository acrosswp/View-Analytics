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
class View_Analytics_Media_Table {

	/**
	 * The single instance of the class.
	 *
	 * @var View_Analytics_Media_Table
	 * @since 1.0.0
	 */
	protected static $_instance = null;

	/**
	 * Main View_Analytics_Media_Table Instance.
	 *
	 * Ensures only one instance of WooCommerce is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see View_Analytics_Media_Table()
	 * @return View_Analytics_Media_Table - Main instance.
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
		return $wpdb->prefix . 'awp_va_media_view';
    }

	/**
	 * Add the current user has view media count
	 */
	public function user_add( $viewer_id, $key_id, $hash_id = '0', $media_id = 0, $attachment_id = 0, $value = 1 ) {
		global $wpdb;

		return $wpdb->insert(
			$this->table_name(),
			array(
				'viewer_id' => $viewer_id,
				'key_id' => $key_id,
				'hash_id' => $hash_id,
				'media_id' => $media_id,
				'attachment_id' => $attachment_id,
				'value' => $value,
			),
			array(
				'%d',
				'%s',
				'%s',
				'%d',
				'%d',
				'%d',
			)
		);
	}

	/**
	 * Get the current user has already view the media or not
	 */
	public function user_get( $viewer_id, $key_id ) {
		global $wpdb;

		$table_name = $this->table_name();

		return $wpdb->get_row(
			$wpdb->prepare( 
				"SELECT * FROM $table_name WHERE viewer_id = %d AND key_id = %s",
				$viewer_id,
				$key_id
			)
		);
	}

	/**
	 * Get the media view details via $attachment_id
	 */
	public function get_details( $key_id ) {
		global $wpdb;

		$table_name = $this->table_name();

		return $wpdb->get_results(
			$wpdb->prepare( 
				"SELECT * FROM $table_name WHERE key_id = %s",
				$key_id
			)
		);
	}

	/**
	 * Update the current user has view media count
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
	 * Delete the current user has view media count
	 */
	public function user_delete( $id ) {
		global $wpdb;
		$wpdb->delete( $this->table_name(), array( 'id' => $id ), array( '%d' ) );
	}

	/**
	* Here this will work only for Image and Video 
	* This function wont work if it's document because docuemnt has a seperate table
	* 
	* get the value of the media from the bp_media buddyboss table
	*/
	public function get_bb_media_details( $media_id ) {
		global $wpdb;
		global $bp;

		return $wpdb->get_row(
			$wpdb->prepare( 
				"SELECT * FROM {$bp->media->table_name} WHERE id = %d",
				$media_id
			)
		);
	}

	/**
	* Here this will work only for Image and Video 
	* This function wont work if it's document because docuemnt has a seperate table
	* 
	* get the value of the media from the bp_media buddyboss table
	*/
	public function get_bb_media_attachment_id( $media_id ) {

		$media_details = $this->get_bb_media_details( $media_id );

		/**
		 * if not empty
		 */
		if ( ! empty( $media_details->attachment_id ) ) {
			return $media_details->attachment_id;
		}

		return false;
	}
}
