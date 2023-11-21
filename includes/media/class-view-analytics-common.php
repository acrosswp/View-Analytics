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
class View_Analytics_Media_Common extends View_Analytics_Common {

	/**
	 * The single instance of the class.
	 *
	 * @var View_Analytics_Loader
	 * @since 1.0.0
	 */
	protected static $_instance = null;

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

		parent::__construct();
		$this->table = View_Analytics_Media_Table::instance();
	}

	/**
	 * Main View_Analytics_Loader Instance.
	 *
	 * Ensures only one instance of WooCommerce is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see View_Analytics_Loader()
	 * @return View_Analytics_Loader - Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

    /**
     * Return the View Analytics Media Count Key
     */
    public function view_count_key() {
        return '_view_analytics_media_table_count_enable';
    }

	/**
     * Return the View Analytics Media Count Key
     */
    public function view_count_enable() {
        return get_option( $this->view_count_key(), true );
    }

	/**
     * Return the View Analytics Media Count Key
     */
    public function lightbox_ajax_action_key() {
        return array( 
			'media_get_media_description' => 
				array( 
					'media_id_key' => 'id',
					'attachment_id_key' => 'attachment_id' 
				),
			'media_get_activity' => 
				array( 
					'media_id_key' => 'id',
					'attachment_id_key' => 'attachment_id' 
				),
			'document_get_document_description' => 
				array( 
					'media_id_key' => 'id',
					'attachment_id_key' => 'attachment_id' 
				),
			'document_get_activity' => 
				array( 
					'media_id_key' => 'id',
					'attachment_id_key' => 'attachment_id' 
				),
			'video_get_video_description' => 
				array( 
					'media_id_key' => 'id',
					'attachment_id_key' => 'attachment_id' 
				),
			'video_get_activity' => 
				array( 
					'media_id_key' => 'video_id',
					'attachment_id_key' => false 
				),
		);
    }

	/**
     * Return the lightbox attchement id key
     */
    public function get_lightbox_attachment_id_key( $action ) {
		$action_arr = $this->lightbox_ajax_action_key();
		$attachment_id_key = false;

		foreach( $action_arr as $key => $value ) {
			if ( $action == $key ) {
				$attachment_id_key = $value['attachment_id_key'];
				break;
			}
		}

		return $attachment_id_key;
    }

		/**
     * Return the lightbox media id key
     */
    public function get_lightbox_media_id_key( $action ) {
		$action_arr = $this->lightbox_ajax_action_key();
		$media_id_key = false;

		foreach( $action_arr as $key => $value ) {
			if ( $action == $key ) {
				$media_id_key = $value['media_id_key'];
				break;
			}
		}

		return $media_id_key;
    }

	/**
     * Return the lightbox attchement id
     */
    public function get_lightbox_attachment_id( $action ) {
		$attachment_id = false;

		$attachment_id_key = $this->get_lightbox_attachment_id_key( $action );

		/**
		 * Check the attachement id is empty
		 * With this also check if this is video activity ajax action or else move to else statment
		 */
		if ( empty( $attachment_id_key ) && 'video_get_activity' == $action ) {
			$media_id_key = $this->get_lightbox_media_id_key( $action );
			$media_id = $this->get_filter_post_value( $media_id_key );
			$attachment_id =  $this->table->get_bb_media_attachment_id( $media_id );
		} else {
			$attachment_id = $this->get_filter_post_value( $attachment_id_key );
		}

		return $attachment_id;
    }

	/**
     * Return the View Analytics Media Count Key
     */
    public function lightbox_all_ajax_action() {

		$action_arr = $this->lightbox_ajax_action_key();
		$main_key = array();

		foreach( $action_arr as $key => $value ) {
			$main_key[] = $key;
		}

		return $main_key;
    }

	/**
     * Return the View Analytics Media Count Key
     */
    public function is_lightbox_ajax() {

		$action_arr = $this->lightbox_all_ajax_action();
        
		if ( isset( $_REQUEST ) && isset( $_REQUEST['action'] ) && in_array( $_REQUEST['action'], $action_arr, true ) ) {
            return $_REQUEST['action'];
        }
		return false;
    }

	/**
	 * Get the media view details via $attachment_id
	 */
	public function get_count( $key_id ) {

		$media_details = $this->table->log_table->get_details( $key_id );
		if ( empty( $media_details ) ) {
			return 0;
		} else {
			return count( $media_details );
		}
	}

	/**
	 * Show the message about when the user has view the Media
	 */
	public function get_view_time_message( $action_date, $mysql_time = false ) {

		/**
		 * If current time is empty
		 */
		if ( empty( $mysql_time ) ) {
			global $wpdb;
			$mysql_time = $wpdb->get_var( 'select CURRENT_TIMESTAMP()' );
		}

		$view_time = human_time_diff( strtotime( $action_date ), strtotime( $mysql_time ) );

		return sprintf( __( 'viewed this %s ago.', 'view-analytics' ), $view_time );

	}

	/**
	 * Get all the Media view
	 */
	public function get_all_media_type() {
		return $this->table->log_table->get_details( $action );
	}

	/**
	 * Get all the Media view
	 */
	public function get_all_media_view( $action ) {
		return $this->table->log_table->get_details( $action );
	}

	/**
	 * Get all the Media view
	 */
	public function get_all_media_view_type_count() {

		$type_count = array();

		$media_view_logs = $this->get_all_media_view( 'media_view' );

		foreach( $media_view_logs as $media_view_log ) {

			if( empty( $media_view_log->type ) ) {
				$media_view_log->type = 'not-define';
			}

			if ( empty( $type_count[ $media_view_log->type ] ) ) {
				$count = 1;
			} else {
				$count = $type_count[ $media_view_log->type ];
				$count++;
			}
			$type_count[ $media_view_log->type ] = $count;
		}

		$media_types_name = array();
		$media_count = array();
		foreach( $type_count as $key => $value ) {
			$media_types_name[] = ucfirst( $key );
			$media_count[] = $value;
		}

		return array(
			'label' => __( 'All Media View Type', 'view-analytics' ),
			'media_label' => $media_types_name,
			'count' => $media_count,
		);
		return $type_count;
	}

	/**
	 * Return array for chart to show all media type
	 */
	public function all_media_type_for_chart() {
		$media_types_counts = array();

		/**
		 * For Media table where it contain all photo and video
		 */
		$media_types = $this->table->get_bb_media_type_from_bb();

		$media_types[] = 'photo';
		$media_types[] = 'video';
		
		$media_types = array_unique( $media_types );
		foreach( $media_types as $media_type  ) {
			$media_types_counts[ $media_type ] = $this->table->get_bb_media_type_count_from_bb( 'media', $media_type );
		}

		/**
		 * For Document table where it contain all document
		 */
		$media_types_counts['document'] = $this->table->get_bb_media_type_count_from_bb( 'document' );

		$media_types_name = array();
		$media_count = array();
		foreach( $media_types_counts as $key => $value ) {
			$media_types_name[] = ucfirst( $key );
			$media_count[] = $value;
		}

		return array(
			'label' => __( 'All Media Type', 'view-analytics' ),
			'media_label' => $media_types_name,
			'count' => $media_count,
		);
	}
}
