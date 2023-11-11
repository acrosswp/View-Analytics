<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://acrosswp.com
 * @since      1.0.0
 *
 * @package    View_Analytics
 * @subpackage View_Analytics/public/partials
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
class View_Analytics_Public_Profile_Count {

    /**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * The ID of this media setting view.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $common;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		$this->common = View_Analytics_Profile_Common::instance();

	}

    /**
     * Count the number of users has view the media
     */
    public function media_view_count_login_user() {
		$this->view_count_verification( 'bp_nouveau_media' );
    }

	/**
     * Count the number of users has view the video
     */
    public function video_view_count_login_user() {
		
		$this->view_count_verification( 'bp_nouveau_video' );
    }


	/**
     * Count the number of users has view the video
     */
    public function document_view_count_login_user() {
		$this->view_count_verification( 'bp_nouveau_media' );
    }

	/**
	 * Verifying the nonce and then adding the media count
	 */
	public function view_count_verification( $key ) {
		// Nonce check!
	    if ( $this->check_nonce( $key ) ) {
			/**
			 * Check if the attachment_id exits or not
			 */
			$check_variable = $this->check_variable();
			if ( ! empty( $check_variable ) ) {
				$this->update_media_view_count( $check_variable['media_id'], $check_variable['attachment_id'] );
			}
        }
	}

	/**
	 * Verifying the nonce and then adding the media count
	 */
	public function check_variable() {
	
		$media_id = $this->common->get_filter_post_value( 'id' );
		$attachment_id = $this->common->get_filter_post_value( 'attachment_id' );

		/**
		 * Check if the attachment_id exits or not
		 */
		if ( ! empty( $media_id ) && ! empty( $attachment_id ) ) {
			return array(
				'media_id' => $media_id,
				'attachment_id' => $attachment_id,
			);
		}

		$action = $this->common->get_filter_post_value( 'action', FILTER_SANITIZE_STRING );
		if( 'video_get_activity' == $action ) {
			$media_id = $this->common->get_filter_post_value( 'video_id' );

			if ( ! empty( $media_id ) ) {
			
				/**
				 * Here this will work only for Image and Video 
				 * This function wont work if it's document because docuemnt has a seperate table
				 */
				$attachment_id = View_Analytics_Profile_Table::instance()->get_bb_media_attachment_id( $media_id );

				/**
				 * if not empty
				 */
				if ( ! empty( $attachment_id ) ) {
					return array(
						'media_id' => $media_id,
						'attachment_id' => $attachment_id,
					);
				}
			}
		}

		return false;
	}

	/**
	 * Verifying the nonce and then adding the media count
	 */
	public function check_nonce( $key ) {
		// Nonce check!
	    $nonce = bb_filter_input_string( INPUT_POST, 'nonce' );
	    if ( wp_verify_nonce( $nonce, $key ) ) {
			return true;
        }

		return false;
	}

	/**
	 * Update Media view count
	 */
	public function update_media_view_count( $media_id, $attachment_id ) {

		if ( $this->common->media_view_count_enable() ) {
			$current_user_id = get_current_user_id();
			$media_view = View_Analytics_Profile_Table::instance()->user_media_get( $current_user_id, $attachment_id );
	
			/**
			 * Check if empty
			 */
			if ( empty( $media_view ) ) {
				View_Analytics_Profile_Table::instance()->user_media_add( $current_user_id, $media_id, $attachment_id, 1 );
			} else {
				$id = $media_view->id;
				$view_count = $media_view->value;
				$view_count++;
	
				View_Analytics_Profile_Table::instance()->user_media_update( $id, $view_count );
			}
		}
	}
}
