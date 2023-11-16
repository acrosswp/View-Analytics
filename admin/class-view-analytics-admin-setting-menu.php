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
  * Check if class exits or not
  */
 if ( class_exists( 'AcrossWP_Sub_Menu' ) ) {

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
	class View_Analytics_Admin_Setting_Menu extends AcrossWP_Sub_Menu {

		/**
		 * The single instance of the class.
		 *
		 * @var AcrossWP_Sub_Menu
		 * @since 0.0.1
		 */
		protected static $_instance = null;

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
		 * Initialize the class and set its properties.
		 *
		 * @since    1.0.0
		 * @param      string    $plugin_name       The name of this plugin.
		 * @param      string    $version    The version of this plugin.
		 */
		public function __construct() {
			parent::__construct();

			// add_action( 'admin_head', function() {
			// 	remove_submenu_page( 'acrosswp', 'view-analytics' );
			// } );
		}

		/**
		 * Main Post_Anonymously_Loader Instance.
		 *
		 * Ensures only one instance of WooCommerce is loaded or can be loaded.
		 *
		 * @since 0.0.1
		 * @static
		 * @see Post_Anonymously_Loader()
		 * @return Post_Anonymously_Loader - Main instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Adds the plugin license page to the admin menu.
		 *
		 * @return void
		 */
		public function loading(){

			$this->media_common = View_Analytics_Media_Common::instance();
			$this->profile_common = View_Analytics_Profile_Common::instance();
			$this->group_common = View_Analytics_Group_Common::instance();

			wpify_custom_fields()->create_options_page( array(
				'type'        => 'normal',
				'parent_slug' => 'acrosswp',
				'page_title'  => __( 'View Analytics', 'view-analytics' ),
				'menu_title'  => __( 'View Analytics', 'view-analytics' ),
				'capability'  => 'manage_options',
				'menu_slug'   => 'view-analytics',
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
				),
			) );
		}
	}
 }
