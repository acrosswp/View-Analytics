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
class View_Analytics_Admin_Setting_Menu {

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
	 * The ID of this profile setting view.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $profile_common;

	/**
	 * The ID of this group setting view.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $group_common;

	/**
	 * The ID of this avatar setting view.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $avatar_common;
	
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
	 * Adds the plugin license page to the admin menu.
	 *
	 * @return void
	 */
	public function setting_menu(){

		$this->media_common = View_Analytics_Media_Common::instance();
		$this->profile_common = View_Analytics_Profile_Common::instance();
		$this->group_common = View_Analytics_Group_Common::instance();
		$this->avatar_common = View_Analytics_Avatar_Common::instance();
		$this->forum_common = View_Analytics_Forum_Common::instance();

		wpify_custom_fields()->create_options_page( array(
			'type'        => 'normal',
			'parent_slug' => 'view-analytics',
			'page_title'  => __( 'Settings', 'view-analytics' ),
			'menu_title'  => __( 'Settings', 'view-analytics' ),
			'capability'  => 'manage_options',
			'menu_slug'   => 'view-analytics-settings',
			'items'      => array(
				array(
					'type'  => 'checkbox',
					'title' => __( 'View Analytics', 'view-analytics' ),
					'label' => __( 'Enable Media View Count', 'view-analytics' ),
					'id'    => $this->media_common->view_count_key(),
				),
				array(
					'type'  => 'checkbox',
					'title' => __( 'View Profile Count', 'view-analytics' ),
					'label' => __( 'Enable Profile View Count', 'view-analytics' ),
					'id'    => $this->profile_common->view_count_key(),				
				),
				array(
					'type'  => 'checkbox',
					'title' => __( 'View Group Count', 'view-analytics' ),
					'label' => __( 'Enable Group View Count', 'view-analytics' ),
					'id'    => $this->group_common->view_count_key(),
				),
				array(
					'type'  => 'checkbox',
					'title' => __( 'View Avatar Count', 'view-analytics' ),
					'label' => __( 'Enable Avatar View Count', 'view-analytics' ),
					'id'    => $this->avatar_common->view_count_key(),
				),
				array(
					'type'  => 'checkbox',
					'title' => __( 'View Forum/Topic/Reply Count', 'view-analytics' ),
					'label' => __( 'Enable Forum/Topic/Reply View Count', 'view-analytics' ),
					'id'    => $this->forum_common->view_count_key(),
				),
			),
		) );
	}
}