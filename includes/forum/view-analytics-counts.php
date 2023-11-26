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
	 * 
	 * Need Improvment
	 */
	public function home_content() {

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

			$post_id = get_the_ID();
			$bbp_root_slug = get_option( '_bbp_root_slug_custom_slug', false );
			if ( 
				is_singular( bbp_get_forum_post_type() )
				|| is_singular( bbp_get_topic_post_type() )
				|| $post_id == $bbp_root_slug
			) {
				$author_id = get_the_author_meta( 'ID' );

				$components = $this->common->get_components( $post_id, $bbp_root_slug );

				$views = $this->common->table->user_get( $post_id, $viewer_id );

				if( empty( $views ) ) {
					$this->common->table->user_add( $post_id, $author_id, $viewer_id, $components, 1 );
				} else {
					$id = $views->id;
					$view_count = $views->value;
					$view_count++;

					$this->common->table->user_update( $id, $view_count, $post_id, $author_id, $viewer_id, $components, 1 );
				}
			}
		}
	}
}
