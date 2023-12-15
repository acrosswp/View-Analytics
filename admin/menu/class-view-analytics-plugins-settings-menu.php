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
class View_Analytics_Admin_Plugins_Setting_Menu {

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
	public $common;
	
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

		$this->common = View_Analytics_Media_Common::instance();

		wpify_custom_fields()->create_options_page( array(
			'type'        => 'normal',
			'parent_slug' => 'view-analytics',
			'page_title'  => __( 'Plugins Settings', 'view-analytics' ),
			'menu_title'  => __( 'Plugins Settings', 'view-analytics' ),
			'capability'  => 'manage_options',
			'menu_slug'   => 'view-analytics-plugins-settings',
			'items'      => array(
				array(
					'id'              => $this->common->view_count_key(),
					'type'            => 'group',
					'title'           => __( 'View Avatar Count', 'view-analytics' ),
					'items'           => array(
						array(
							'type'  => 'toggle',
							'title' => __( 'Delete Tables', 'view-analytics' ),
							'label' => __( 'Delete All table on Plugin Deactivations', 'view-analytics' ),
							'id'    => 'delete-tables',
							'default' => false,
						),
					),
				)
			),	
		) );
	}
}