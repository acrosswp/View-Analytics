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
	 * This function is run when someone visit the member profile page
	 */
	public function home_content( $item_id, $type, $avatar_data ) {

		/**
		 * Add function to update count
		 */

		$current_user_id = get_current_user_id();

		/**
		 * Check if both are not empty
		 */
		if ( ! empty( $current_user_id ) ) {
			$this->update_view_count( $current_user_id );
		}
	}
	

	/**
	 * Update Avatar Update view count
	 */
	public function update_view_count( $key_id, $user_id = false ) {

		$user_id = empty( $user_id ) ? $key_id : $user_id;

		if ( $this->common->view_count_enable() ) {

			$this->table = View_Analytics_Avatar_Table::instance();

			$view = $this->table->user_get( $key_id );

			/**
			 * Check if empty
			 */
			if ( empty( $view ) ) {
				$this->table->user_add( $key_id, $user_id );
			} else {
				$id = $view->id;
				$view_count = $view->value;
				$view_count++;
	
				$this->table->user_update( $id, $view_count );
			}
		}
	}
}
