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
	 * The single instance of the class.
	 *
	 * @var View_Analytics_Log_Table
	 * @since 1.0.0
	 */
	public $log_table = null;

	/**
	 * The single instance of the class.
	 *
	 * @since 1.0.0
	 */
	public $log_table_key = 'media_view';

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
        $this->log_table = View_Analytics_Log_Table::instance();
	}

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
	public function user_add( $viewer_id, $key_id, $hash_id = '0', $media_id = 0, $attachment_id = 0, $media_owner_id = 0, $media_type = 'photo', $value = 1 ) {
		global $wpdb;

		$add = $wpdb->insert(
			$this->table_name(),
			array(
				'viewer_id' => $viewer_id,
				'key_id' => $key_id,
				'hash_id' => $hash_id,
				'media_id' => $media_id,
				'attachment_id' => $attachment_id,
				'user_id' => $media_owner_id,
				'type' => $media_type,
				'value' => $value,
			),
			array(
				'%d',
				'%s',
				'%s',
				'%d',
				'%d',
				'%d',
				'%s',
				'%d',
			)
		);

		if ( $add ) {
			$this->log_table->user_add( $this->log_table_key, $key_id, $media_owner_id, $viewer_id, $type );
		}

		return $add;
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
	public function user_update( $id, $value, $details = false ,$mysql_time = false ) {
		global $wpdb;

		if ( empty( $mysql_time ) ) {
			$mysql_time = $wpdb->get_var( 'select CURRENT_TIMESTAMP()' );
		}

		$update = $wpdb->update(
			$this->table_name(),
			array(
				'last_date' => $mysql_time,
				'value' => $value,
				'is_new' => 1,
			),
			array( 
				'id' => $id 
			),
			array( '%s','%d','%d' ),
			array( '%d' )
		);

		if ( 
			$update 
			&& ! empty( $details->key_id ) 
			&& ! empty( $details->user_id ) 
			&& ! empty( $details->viewer_id ) 
			&& ! empty( $details->type ) 
			) {
			$this->log_table->user_add( $this->log_table_key, $details->key_id, $details->user_id, $details->viewer_id, $details->type );
		}

		return $update;
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
	public function get_bb_media_type_count_from_bb( $table = 'media', $type = '' ) {
		global $wpdb;
		global $bp;

		
		$sql = $wpdb->prepare( 
			"SELECT id FROM {$bp->media->table_name} WHERE type = %s",
			$type
		);

		if ( 'document' == $table ) {
			$sql = $wpdb->prepare( 
				"SELECT id FROM {$bp->document->table_name}",
				$type
			);
		}

		$result = $wpdb->get_results( $sql, ARRAY_A );
		return empty( $result ) ? 0 : count( $result );
	}

	/**
	* Here this will work only for Image and Video 
	* This function wont work if it's document because docuemnt has a seperate table
	* 
	* get the value of the media from the bp_media buddyboss table
	*/
	public function get_bb_media_type_from_bb() {
		global $wpdb;
		global $bp;

		$type = array();
		$sql = "SELECT DISTINCT type FROM {$bp->media->table_name}";

		$media_types = $wpdb->get_results( $sql, ARRAY_A );

		if( ! empty( $media_types ) ) {
			foreach( $media_types as $media_type ) {

				if( ! in_array( $media_type['type'], $type ) ) {
					$type[] = $media_type['type'];
				}
			}
		}
		return $type;
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
				"SELECT * FROM {$bp->media->table_name} WHERE attachment_id = %d",
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
	public function get_bb_document_details( $document_id ) {
		global $wpdb;
		global $bp;

		return $wpdb->get_row(
			$wpdb->prepare( 
				"SELECT * FROM {$bp->document->table_name} WHERE attachment_id = %d",
				$document_id
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

	/**
	* Here this will work only for Image and Video 
	* This function wont work if it's document because docuemnt has a seperate table
	* 
	* get the value of the media from the bp_media buddyboss table
	*/
	public function get_bb_media_owner_id( $media_id, $type = 'media' ) {

		if( in_array( $type, array( 'media', 'photo', 'video' ) ) ) {
			$details = $this->get_bb_media_details( $media_id );
		} else {
			$details = $this->get_bb_document_details( $media_id );
		}

		/**
		 * if not empty
		 */
		if ( ! empty( $details->user_id ) ) {
			return $details->user_id;
		}

		return false;
	}
}
