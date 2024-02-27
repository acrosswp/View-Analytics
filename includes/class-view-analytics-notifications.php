<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://acrosswp.com
 * @since      1.0.0
 *
 * @package    View_Analytics_Nofications
 * @subpackage View_Analytics_Nofications/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    View_Analytics_Nofications
 * @subpackage View_Analytics_Nofications/includes
 * @author     AcrossWP <contact@acrosswp.com>
 */
class View_Analytics_Nofications {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      View_Analytics_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

    /**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The Custom ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The Custom ID of this plugin.
	 */
	private $plugin_name_action;

	/**
	 * The Custom ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The Custom ID of this plugin.
	 */
	private $plugin_name_message;

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
	public function __construct( $plugin_name ) {

		$this->loader = View_Analytics_Loader::instance();
		$this->common = View_Analytics_Media_Common::instance();

        $this->plugin_name = $plugin_name;
		$this->plugin_name_action = 'media_action';
		$this->plugin_name_message = $plugin_name . '-media_message';

		$this->loader->add_action( $this->common->create_hooks_key( 'users_view_media' ), $this, 'add_notification', 99, 6 );

	}

    /**
	 * This hooks to comment creation and saves the comment id
	 * 
	 * @since 1.0.0
	 */
	function add_notification( $id, $key_id, $current_user_id, $author_id , $old_session_count, $session_count ) {

		/**
		 * Check if the notifications is active 
		 * Check if the activity does not below to the login user and if so then do not send the notifications
		 */
		if ( bp_is_active( 'notifications' ) && $author_id !== $current_user_id ) {

			add_action( 'bp_notification_after_save', array( $this, 'send_email' ), 100 );

			bp_notifications_add_notification( array(
				'user_id'           => $author_id,
				'item_id'           => $key_id,
				'secondary_item_id' => $current_user_id,
				'component_name'    => $this->plugin_name,
				'component_action'  => $this->plugin_name_action,
				'date_notified'     => bp_core_current_time(),
			) );

			remove_action( 'bp_notification_after_save', array( $this, 'send_email' ), 100 );
		}
	}

	/**
	 * Send like notification to the user
	 */
	function send_email( $notification ) {

		$author_id = absint( $notification->user_id );

		if ( true === bb_is_notification_enabled( $author_id, $this->plugin_name_action ) ) {

			$user_id = absint( $notification->secondary_item_id );
		
			$name = bp_core_get_user_displayname( $user_id );
			$user_url = esc_url( bp_core_get_user_domain( $user_id ) );

			$key_id	= $notification->item_id;

			$media_details = $this->common->table->get_details( $key_id );
			$media_id = 0;
			if ( ! empty( $media_details ) ) {
				$media_id = $media_details['media_id'];
			}
			$media_url = bp_media_get_preview_image_url( $media_id, $key_id, 'bb-media-activity-image', true, $user_id );

			$args                          = array(
				'tokens' => array(
					'poster.name'   => $name,
					'poster_like.url'  	=> $user_url,
					'media.url'  => $media_url,
				),
			);

			$unsubscribe_args              = array(
				'user_id'           => $author_id,
				'notification_type' => $this->plugin_name_message,
			);

			$args['tokens']['unsubscribe'] = esc_url( bp_email_get_unsubscribe_link( $unsubscribe_args ) );

			// Send notification email.
			bp_send_email( $this->plugin_name_message, $author_id, $args );
		}
	}
}