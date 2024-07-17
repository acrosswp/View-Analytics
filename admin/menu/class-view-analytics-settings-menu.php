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
	 * Add the setting for media
	 */
	public function media_settings() {
		return apply_filters(
			'view-analytics-admin-media-setting',
			array(
				array(
					'type'  => 'toggle',
					'title' => __( 'Media Analytics', 'view-analytics' ),
					'label' => __( 'Enable Media Analytics', 'view-analytics' ),
					'id'    => 'main',
					'default' => $this->media_common->get_key_default_value( 'main' ),
				),
				array(
					'type'  => 'toggle',
					'description' => __( 'Show Media View Count in Public.', 'view-analytics' ),
					'id'    => 'show_view_count',
					'default' => $this->media_common->get_key_default_value( 'show_view_count' ),
				),
				array(
					'type'  => 'toggle',
					'description' => __( 'Show Media View User Lists when someone click on the views button.', 'view-analytics' ),
					'id'    => 'show_view_user_list',
					'default' => $this->media_common->get_key_default_value( 'show_view_user_list' ),
				),
			)
		);
	}

	/**
	 * Add the setting for media
	 */
	public function profile_settings() {
		return apply_filters(
			'view-analytics-admin-profile-setting',
			array(
				array(
					'type'  => 'toggle',
					'title' => __( 'Profile Analytics', 'view-analytics' ),
					'label' => __( 'Enable Profile Analytics', 'view-analytics' ),
					'id'    => 'main',
					'default' => $this->profile_common->get_key_default_value( 'main' ),
				),
				array(
					'type'  => 'toggle',
					'description' => __( 'Show list of user who has view the Member Profile', 'view-analytics' ),
					'id'    => 'show_view_count',
					'default' => $this->profile_common->get_key_default_value( 'show_view_count' ),
				),
			)
		);
	}

	/**
	 * Add the setting for media
	 */
	public function avatar_settings() {
		return apply_filters(
			'view-analytics-admin-avatar-setting',
			array(
				array(
					'type'  => 'toggle',
					'title' => __( 'Avatar Analytics', 'view-analytics' ),
					'label' => __( 'Enable Avatar Analytics', 'view-analytics' ),
					'id'    => 'main',
					'default' => $this->avatar_common->get_key_default_value( 'main' ),
				),
				array(
					'type'  => 'toggle',
					'description' => __( 'Show Profile Avatar update Count in Public', 'view-analytics' ),
					'id'    => 'show_view_count_profile_avatar',
					'default' => $this->avatar_common->get_key_default_value( 'show_view_count_profile_avatar' ),
				),
				array(
					'type'  => 'toggle',
					'description' => __( 'Show Profile Cover Image update Count in Public', 'view-analytics' ),
					'id'    => 'show_view_count_profile_cover',
					'default' => $this->avatar_common->get_key_default_value( 'show_view_count_profile_cover' ),
				),
				array(
					'type'  => 'toggle',
					'description' => __( 'Show Group Avatar update Count in Public', 'view-analytics' ),
					'id'    => 'show_view_count_group_avatar',
					'default' => $this->avatar_common->get_key_default_value( 'show_view_count_group_avatar' ),
				),
				array(
					'type'  => 'toggle',
					'description' => __( 'Show Group Cover Image update Count in Public', 'view-analytics' ),
					'id'    => 'show_view_count_group_cover',
					'default' => $this->avatar_common->get_key_default_value( 'show_view_count_group_cover' ),
				),
			)
		);
	}

	/**
	 * Adds the plugin license page to the admin menu.
	 *
	 * @return void
	 */
	public function setting_menu(){

		$this->media_common = View_Analytics_Media_Common::instance();
		$this->profile_common = View_Analyticsfile_Common::instance();
		$this->avatar_common = View_Analytics_Avatar_Common::instance();

		wpify_custom_fields()->create_options_page( array(
			'type'        => 'normal',
			'parent_slug' => 'view-analytics',
			'page_title'  => __( 'Settings', 'view-analytics' ),
			'menu_title'  => __( 'Settings', 'view-analytics' ),
			'capability'  => 'manage_options',
			'menu_slug'   => 'view-analytics-settings',	
			'items'       => array(
				array(
				   'id'              => $this->media_common->view_count_key(),
				   'type'            => 'group',
				   'title'           => 'Group',
				   'items'           => array(
					  array(
						 'type'            => 'text',
						 'title'           => 'Text in group 1',
						 'id'              => 'some_example_text_1',
					  ),
					  array(
						 'type'            => 'text',
						 'title'           => 'Text in group 2',
						 'id'              => 'some_example_text_2',
					  ),
				   )
				),
			),
		) );
	}
}