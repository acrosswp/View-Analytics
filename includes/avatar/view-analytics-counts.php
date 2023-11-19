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
class View_Analytics_Public_Avatar_Count {

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
	 * The instance of the Profile View Table
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $table;

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

		$this->common = View_Analytics_Avatar_Common::instance();

	}

	/**
	 * This function is run when someone update Profile avatar
	 */
	public function xprofile_avatar_uploaded( $user_id ) {

		/**
		 * Add function to update count
		 */

		$current_user_id = get_current_user_id();

		if( ! empty( $current_user_id ) ) {
			$this->update_view_count( $user_id, $current_user_id, 'xprofile' ,'avatar' );
		}

	}

	/**
	 * This function is run when someone update Profile avatar
	 */
	public function xprofile_cover_image_uploaded( $user_id ) {

		/**
		 * Add function to update count
		 */

		$current_user_id = get_current_user_id();

		if( ! empty( $current_user_id ) ) {
			$this->update_view_count( $user_id, $current_user_id, 'xprofile' ,'cover' );
		}

	}

	/**
	 * This function is run when someone update Group avatar
	 */
	public function groups_avatar_uploaded( $group_id ) {

		/**
		 * Add function to update count
		 */

		$current_user_id = get_current_user_id();

		if( ! empty( $current_user_id ) ) {
			$this->update_view_count( $group_id, $current_user_id, 'group' ,'avatar' );
		}

	}

	/**
	 * This function is run when someone update Group avatar
	 */
	public function groups_cover_image_uploaded( $group_id ) {

		/**
		 * Add function to update count
		 */

		$current_user_id = get_current_user_id();

		if( ! empty( $current_user_id ) ) {
			$this->update_view_count( $group_id, $current_user_id, 'group' ,'cover' );
		}

	}
	

	/**
	 * Update Avatar Update view count
	 */
	public function update_view_count( $key_id, $user_id = false, $type = 'xprofile', $action = 'avatar' ) {

		$user_id = empty( $user_id ) ? $key_id : $user_id;

		if ( $this->common->view_count_enable() ) {

			$this->table = View_Analytics_Avatar_Table::instance();
		
			$this->table->user_add( $key_id, $user_id, $type, $action );
		}
	}

	/**
	 * Update Avatar Update view count
	 */
	public function doing_update_view_count( $key_id, $user_id = false, $type = 'xprofile', $action = 'avatar' ) {

		$user_id = empty( $user_id ) ? $key_id : $user_id;

		$this->table = View_Analytics_Avatar_Table::instance();
		
		$view = $this->table->user_get( $key_id, $type, $action );
		
		if ( empty( $view ) ) {
			$this->table->user_add( $key_id, $user_id, $type, $action );
		}
	}
}
