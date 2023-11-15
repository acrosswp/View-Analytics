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
class View_Analytics_Public_Group_Count {

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

		$this->common = View_Analytics_Group_Common::instance();

	}

	/**
	 * This function is run when someone visit the member profile page
	 */
	public function home_content() {

		$group_id = bp_group_id();
		$current_user_id = get_current_user_id();

		/**
		 * Check if both are not empty
		 */
		if ( ! empty( $current_user_id ) && ! empty( $group_id ) ) {
			$this->update_view_count( $group_id, $current_user_id );
		}
	}
	

	/**
	 * Update Media view count
	 */
	public function update_view_count( $group_id, $viewer_id ) {

		if ( $this->common->view_count_enable() ) {

			$this->table = View_Analytics_Group_Table::instance();

			$profile_view = $this->table->user_group_get( $group_id, $viewer_id );
	
			/**
			 * Check if empty
			 */
			if ( empty( $profile_view ) ) {
				$this->table->user_group_add( $group_id, $viewer_id, 1 );
			} else {
				$id = $profile_view->id;
				$view_count = $profile_view->value;
				$view_count++;
	
				$this->table->user_group_update( $id, $view_count );
			}
		}
	}
}
