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
class View_Analytics_Public_Forum_Count {

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

		$this->common = View_Analytics_Forum_Common::instance();

	}

	/**
	 * This function is run when someone visit the member profile page
	 */
	public function home_content() {

		return;

		$viewer_id = get_current_user_id();

		/**
		 * Check if both are not empty
		 */
		if ( ! empty( $viewer_id ) ) {
			$this->update_view_count( $viewer_id );
		}
	}

	/**
	 * Update Media view count
	 */
	public function update_view_count( $viewer_id ) {

		if ( $this->common->view_count_enable() ) {
			$key_id = get_the_ID();
			$author_id = get_the_author_meta( 'ID' );

			$components = $this->common->get_components( $key_id );

			$views = $this->common->table->user_get( $key_id, $viewer_id );

			if( empty( $views ) ) {
				$this->common->table->user_add( $key_id, $author_id, $viewer_id, $components, 1 );
			} else {
				$id = $views->id;
				$view_count = $views->value;
				$view_count++;

				$this->common->table->user_update( $id, $view_count, $key_id, $author_id, $viewer_id, $components, 1 );
			}
		}
	}
}
