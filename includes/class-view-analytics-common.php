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
        return get_option( $this->media_view_count_key(), false );
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
}
