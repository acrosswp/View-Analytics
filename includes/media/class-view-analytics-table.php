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
     * Return the View Analytics Media Count Ket
     */
    public function table_name_log() {
		global $wpdb;
		return $wpdb->prefix . 'awp_va_media_view_log';
    }

	/**
	 * Add the current user has view media count
	 */
	public function user_add( $viewer_id, $key_id, $hash_id = '0', $media_id = 0, $attachment_id = 0, $media_owner_id = 0, $media_type = 'photo', $components = array(), $value = 1 ) {
		global $wpdb;

		$mime_type = get_post_mime_type( $attachment_id );

		$add = $wpdb->insert(
			$this->table_name(),
			array(
				'blog_id' => get_current_blog_id(),
				'viewer_id' => $viewer_id,
				'key_id' => $key_id,
				'hash_id' => $hash_id,
				'media_id' => $media_id,
				'attachment_id' => $attachment_id,
				'user_id' => $media_owner_id,
				'type' => $media_type,
				'value' => $value,
				'mime_type' => $mime_type,
				'locale' => get_user_locale(),
			),
			array(
				'%d',
				'%d',
				'%s',
				'%s',
				'%d',
				'%d',
				'%d',
				'%s',
				'%d',
				'%s',
				'%s',
			)
		);

		if ( $add ) {
			$this->add_log( $wpdb->insert_id, $media_owner_id, $viewer_id, $key_id, $media_type, $mime_type, $components );
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
			),
			ARRAY_A
		);
	}

	/**
	 * Update the current user has view media count
	 */
	public function user_update( $id, $value, $details = false, $components = array(), $mysql_time = false ) {
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
			$this->add_log( $id, $details->user_id, $details->viewer_id, $details->key_id, $details->type, $details->mime_type, $components );
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
	 * Get the Media type count
	*/
	public function get_bb_media_type_count( $type ) {
		global $wpdb;

		$table_name = $this->table_name();

		$sql = $wpdb->prepare( 
			"SELECT count(1) as count FROM {$table_name} WHERE type = %s",
			$type
		);

		$result = $wpdb->get_row( $sql, ARRAY_A );

		return empty( $result['count'] ) ? 0 : absint( $result['count'] );
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
				"SELECT * FROM {$bp->media->table_name} WHERE id = %d",
				$media_id
			),
			ARRAY_A
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
				"SELECT * FROM {$bp->document->table_name} WHERE id = %d",
				$document_id
			),
			ARRAY_A
		);
	}

	/**
	 * Add value in Log table
	 */
	public function add_log( $match_id, $media_owner_id, $viewer_id, $key_id, $type, $mime_type, $components ) {
		global $wpdb;

		return $wpdb->insert(
			$this->table_name_log(),
			array( 
				'blog_id' => get_current_blog_id(),
				'match_id' => $match_id,
				'user_id' => $media_owner_id,
				'viewer_id' => $viewer_id,
				'key_id' => $key_id,
				'type' => $type,
				'mime_type' => $mime_type,
				'url' => $components['url'],
				'site_components' => $components['site_components'],
				'components' => $components['components'],
				'object' => $components['object'],
				'primitive' => $components['primitive'],
				'variable' => $components['variable'],
				'locale' => get_user_locale(),
			),
			array(
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
				'%s',
				'%s',
				'%s',
			)
		);
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
		if ( ! empty( $details['user_id'] ) ) {
			return $details['user_id'];
		}

		return false;
	}


	/**
	* Here this will work only for Image and Video 
	* This function wont work if it's document because docuemnt has a seperate table
	* 
	* get the value of the media from the bp_media buddyboss table
	*/
	public function get_bb_post_id( $media_id ) {
		global $wpdb;
		global $bp;

		$posts = $wpdb->get_row(
			$wpdb->prepare( 
				"SELECT t.post_id
				FROM {$wpdb->postmeta} t
			   	WHERE 
					FIND_IN_SET(%s, t.meta_value) > 0 
					AND t.meta_key = 'bp_media_ids'
				",
				$media_id
			),
			ARRAY_A
		);

		if ( empty( $posts['post_id'] ) ) {
			return false;
		}

		return $posts['post_id'];
	}
}