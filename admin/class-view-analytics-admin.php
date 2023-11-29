<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://acrosswp.com
 * @since      1.0.0
 *
 * @package    View_Analytics
 * @subpackage View_Analytics/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    View_Analytics
 * @subpackage View_Analytics/admin
 * @author     AcrossWP <contact@acrosswp.com>
 */
class View_Analytics_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The ID of this media setting view.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $media_common;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in View_Analytics_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The View_Analytics_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( 'google-chart', 'https://cdn.jsdelivr.net/npm/chart.js', array( 'jquery' ), '', false );
		wp_enqueue_script( $this->plugin_name . '-backend', VIEW_ANALYTICS_PLUGIN_URL . 'assets/dist/js/backend-script.js', array( 'jquery', 'google-chart' ), $this->version, false );

	}

	/**
	 * Register the Localize Script for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function wp_localize_script() {

		$this->media_common = View_Analytics_Media_Common::instance();
		

		wp_localize_script( $this->plugin_name . '-backend', 'view_analytics_media_view',
			array( 
				'all_media_type' => $this->media_common->all_media_type_for_chart(),
				'all_media_view_type' => $this->media_common->get_all_media_user_view_type_count(),
			)
		);
	}

	/**
	 * Add Settings link to plugins area.
	 *
	 * @since    1.0.0
	 *
	 * @param array  $links Links array in which we would prepend our link.
	 * @param string $file  Current plugin basename.
	 * @return array Processed links.
	 */
	public function modify_plugin_action_links( $links, $file ) {

		// Return normal links if not BuddyPress.
		if ( VIEW_ANALYTICS_PLUGIN_BASENAME !== $file ) {
			return $links;
		}

		// Add a few links to the existing links array.
		return array_merge(
			$links,
			array(
				'media_settings'      => '<a href="' . esc_url( admin_url( 'admin.php?page=view-analytics-settings' ) ) . '">' . esc_html__( 'Settings', 'view-analytics' ) . '</a>',
				'about'         => '<a href="' . esc_url( admin_url( '?page=view-analytics' ) ) . '">' . esc_html__( 'About', 'view-analytics' ) . '</a>',
			)
		);
	}
}
