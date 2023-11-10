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
class View_Analytics_Common {

	/**
	 * The single instance of the class.
	 *
	 * @var View_Analytics_Loader
	 * @since 1.0.0
	 */
	protected static $_instance = null;

	/**
	 * The ID of this media setting view.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	public $table;

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
     * Return the View Analytics Media Count Ket
     */
    public function media_settings() {
        return 'view-analytics-media-settings';
    }


    /**
     * Return the View Analytics Media Count Ket
     */
    public function media_view_count_key() {
        return '_View_Analytics_Media_Table_count_enable';
    }


    /**
     * Return the View Analytics Media Count Ket
     */
    public function media_view_count_enable() {
        return get_option( $this->media_view_count_key(), true );
    }

	/**
     * Return the View Analytics Media Count Ket
     */
    public function lightbox_media_ajax_action_key() {
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
		$action_arr = $this->lightbox_media_ajax_action_key();
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
		$action_arr = $this->lightbox_media_ajax_action_key();
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
			$attachment_id =  View_Analytics_Media_Table::instance()->get_bb_media_attachment_id( $media_id );
		} else {
			$attachment_id = $this->get_filter_post_value( $attachment_id_key );
		}

		return $attachment_id;
    }

	/**
     * Return the View Analytics Media Count Ket
     */
    public function lightbox_all_media_ajax_action() {

		$action_arr = $this->lightbox_media_ajax_action_key();
		$main_key = array();

		foreach( $action_arr as $key => $value ) {
			$main_key[] = $key;
		}

		return $main_key;
    }

	/**
     * Return the View Analytics Media Count Ket
     */
    public function is_media_lightbox_ajax() {

		$action_arr = $this->lightbox_all_media_ajax_action();
        
		if ( isset( $_REQUEST ) && isset( $_REQUEST['action'] ) && in_array( $_REQUEST['action'], $action_arr, true ) ) {
            return $_REQUEST['action'];
        }
		return false;
    }

	/**
     * Return the View Analytics Media Count Ket
     */
    public function get_filter_post_value( $key, $filter = FILTER_VALIDATE_INT ) {
		return filter_input( INPUT_POST,  $key, $filter );

    }

	/**
	 * Get the media view details via $attachment_id
	 */
	public function media_get_count( $attachment_id ) {
		$media_details = $this->table->media_get_details( $attachment_id );
		if ( empty( $media_details ) ) {
			return 0;
		} else {
			return count( $media_details );
		}
	}

	/**
	 * Check if the current user is allow to view the Media View List
	 */
	public function can_current_user_media_view_list( $attachment_id ) {
		$user_id = get_current_user_id();

		if ( empty( $user_id ) ) {
			return false;
		}

		/**
         * If user is site admin
         */
        if( current_user_can('administrator') ) {
            return true;
        }

		if( $user_id == get_post_field( 'post_author', $attachment_id ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Show the message about when the user has view the Media
	 */
	public function get_media_view_time_message( $action_date, $mysql_time = false ) {

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
}
