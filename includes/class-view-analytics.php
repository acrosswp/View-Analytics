<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://acrosswp.com
 * @since      1.0.0
 *
 * @package    View_Analytics
 * @subpackage View_Analytics/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    View_Analytics
 * @subpackage View_Analytics/includes
 * @author     AcrossWP <contact@acrosswp.com>
 */
final class View_Analytics {
	
	/**
	 * The single instance of the class.
	 *
	 * @var View_Analytics
	 * @since 1.0.0
	 */
	protected static $_instance = null;

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      View_Analytics_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The common that's responsible for common function in Media of BuddyBoss and BuddyPress
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      View_Analytics_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $common;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $name    The string used to uniquely identify this plugin.
	 */
	protected $name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'view-analytics';

		$this->define_constants();

		if ( defined( 'VIEW_ANALYTICS_VERSION' ) ) {
			$this->version = VIEW_ANALYTICS_VERSION;
		} else {
			$this->version = '1.0.0';
		}

		$this->load_dependencies();

		$this->set_locale();

		$this->load_hooks();

	}

	/**
	 * Main View_Analytics Instance.
	 *
	 * Ensures only one instance of WooCommerce is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see View_Analytics()
	 * @return View_Analytics - Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Define WCE Constants
	 */
	private function define_constants() {

		$this->define( 'VIEW_ANALYTICS_PLUGIN_FILE', VIEW_ANALYTICS_FILES );
		$this->define( 'VIEW_ANALYTICS_PLUGIN_BASENAME', plugin_basename( VIEW_ANALYTICS_FILES ) );
		$this->define( 'VIEW_ANALYTICS_PLUGIN_PATH', plugin_dir_path( VIEW_ANALYTICS_FILES ) );
		$this->define( 'VIEW_ANALYTICS_PLUGIN_URL', plugin_dir_url( VIEW_ANALYTICS_FILES ) );
		$this->define( 'VIEW_ANALYTICS_PLUGIN_NAME_SLUG', $this->plugin_name );
		$this->define( 'VIEW_ANALYTICS_PLUGIN_NAME', 'View Analytics' );
		
		if( ! function_exists( 'get_plugin_data' ) ){
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}
		$data = get_plugin_data( VIEW_ANALYTICS_PLUGIN_FILE );
		$version = $data['Version'];
		$this->define( 'VIEW_ANALYTICS_VERSION', $version );

		$this->define( 'VIEW_ANALYTICS_PLUGIN_URL', $version );
	}

	/**
	 * Define constant if not already set
	 * @param  string $name
	 * @param  string|bool $value
	 */
	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	/**
	 * Register all the hook once all the active plugins are loaded
	 *
	 * Uses the plugins_loaded to load all the hooks and filters
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	public function load_hooks() {

		/**
		 * Check if plugin can be loaded safely or not
		 * 
		 * @since    1.0.0
		 */
		if( apply_filters( 'view-analytics-load', true ) ) {
			$this->define_admin_hooks();
			$this->define_public_hooks();
		}

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - View_Analytics_Loader. Orchestrates the hooks of the plugin.
	 * - View_Analytics_i18n. Defines internationalization functionality.
	 * - View_Analytics_Admin. Defines all hooks for the admin area.
	 * - View_Analytics_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * Add composer file
		 */
		require_once( VIEW_ANALYTICS_PLUGIN_PATH . 'vendor/autoload.php' );

		/**
		 * The file is reponsiable of updating the plugins zip
		 * of the plugin.
		 */
		require_once VIEW_ANALYTICS_PLUGIN_PATH . 'admin/licenses-update/plugin-update-checker/main.php';


		if ( class_exists( 'AcrossWP_BuddyPress_BuddyBoss_Platform_Dependency' ) ) {
			new AcrossWP_BuddyPress_BuddyBoss_Platform_Dependency( $this->get_plugin_name(), VIEW_ANALYTICS_FILES );
		}

		/**
		 * Check if the class does not exits then only allow the file to add
		 */
		if( class_exists( 'AcrossWP_Plugin_Update' ) ) {

			/**
			 * The class responsible for defining all actions that occur in the admin area.
			 */
			require_once VIEW_ANALYTICS_PLUGIN_PATH . 'admin/update/class-view-analytics-update.php';

			$plugin_update = new View_Analytics_Update( $this->get_plugin_name(), $this->get_version() );

			$acrosswp_plugin_update = new AcrossWP_Plugin_Update( $this->get_plugin_name(), $this->get_version() );
		}

		/**
		 * All the functions are included in this file
		 */
		require_once( VIEW_ANALYTICS_PLUGIN_PATH . 'includes/class-view-analytics-common.php' );

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once VIEW_ANALYTICS_PLUGIN_PATH . 'includes/class-view-analytics-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once VIEW_ANALYTICS_PLUGIN_PATH . 'includes/class-view-analytics-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once VIEW_ANALYTICS_PLUGIN_PATH . 'admin/class-view-analytics-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once VIEW_ANALYTICS_PLUGIN_PATH . 'admin/class-view-analytics-admin-setting-menu.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once VIEW_ANALYTICS_PLUGIN_PATH . 'public/class-view-analytics-public.php';

		/**
		 * Load all the Media view file
		 */
		$this->load_media_view();

		/**
		 * Load all the Profile view file
		 */
		$this->load_profile_view();

		/**
		 * Load all the Group view file
		 */
		$this->load_group_view();

		/**
		 * Load all the Avatar view file
		 */
		$this->load_avatar_view();

		$this->loader = View_Analytics_Loader::instance();

		$this->common = View_Analytics_Common::instance();

	}

	/**
	 * Load all the File releaste to Media
	 */
	private function load_media_view() {

		/**
		 * Contain all the value to edit/delete/remove the table row
		 */
		require_once( VIEW_ANALYTICS_PLUGIN_PATH . 'includes/media/class-view-analytics-media-table.php' );


		/**
		 * All the functions are included in this file
		 */
		require_once( VIEW_ANALYTICS_PLUGIN_PATH . 'includes/media/class-view-analytics-media-common.php' );

		/**
		 * The class responsible for defining all actions that are releate to recoring the view count in table
		 */
		require_once VIEW_ANALYTICS_PLUGIN_PATH . 'public/partials/media/view-analytics-public-media-counts.php';

		/**
		 * The class responsible for for rest api to view who has view the media
		 */
		require_once VIEW_ANALYTICS_PLUGIN_PATH . 'public/partials/media/class-view-analytics-media-rest-api.php';
	}

	/**
	 * Load all the File releaste to Profile
	 */
	private function load_profile_view() {

		/**
		 * Contain all the value to edit/delete/remove the table row
		 */
		require_once( VIEW_ANALYTICS_PLUGIN_PATH . 'includes/profile/class-view-analytics-profile-table.php' );

		/**
		 * All the functions are included in this file
		 */
		require_once( VIEW_ANALYTICS_PLUGIN_PATH . 'includes/profile/class-view-analytics-profile-common.php' );


		/**
		 * The class responsible for defining all actions that are releate to recoring the view count in table
		 */
		require_once VIEW_ANALYTICS_PLUGIN_PATH . 'public/partials/profile/view-analytics-public-profile-counts.php';


		/**
		 * The class responsible for defining all actions that are releate to recoring the view count in table
		 */
		require_once VIEW_ANALYTICS_PLUGIN_PATH . 'public/partials/profile/view-analytics-public-profile-menu.php';
	}


	/**
	 * Load all the File releaste to Group
	 */
	private function load_group_view() {

		/**
		 * Contain all the value to edit/delete/remove the table row
		 */
		require_once( VIEW_ANALYTICS_PLUGIN_PATH . 'includes/group/class-view-analytics-group-table.php' );

		/**
		 * All the functions are included in this file
		 */
		require_once( VIEW_ANALYTICS_PLUGIN_PATH . 'includes/group/class-view-analytics-group-common.php' );


		/**
		 * The class responsible for defining all actions that are releate to recoring the view count in table
		 */
		require_once VIEW_ANALYTICS_PLUGIN_PATH . 'public/partials/group/view-analytics-public-group-counts.php';


		/**
		 * The class responsible for defining all actions that are releate to recoring the view count in table
		 */
		require_once VIEW_ANALYTICS_PLUGIN_PATH . 'public/partials/group/view-analytics-public-group-menu.php';
	}

	/**
	 * Load all the File releaste to Group
	 */
	private function load_avatar_view() {

		/**
		 * Contain all the value to edit/delete/remove the table row
		 */
		require_once( VIEW_ANALYTICS_PLUGIN_PATH . 'includes/avatar/class-view-analytics-avatar-table.php' );

		/**
		 * All the functions are included in this file
		 */
		require_once( VIEW_ANALYTICS_PLUGIN_PATH . 'includes/avatar/class-view-analytics-avatar-common.php' );


		/**
		 * The class responsible for defining all actions that are releate to recoring the view count in table
		 */
		require_once VIEW_ANALYTICS_PLUGIN_PATH . 'public/partials/avatar/view-analytics-public-avatar-counts.php';


		// /**
		//  * The class responsible for defining all actions that are releate to recoring the view count in table
		//  */
		// require_once VIEW_ANALYTICS_PLUGIN_PATH . 'public/partials/group/view-analytics-public-group-menu.php';
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the View_Analytics_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$i18n = new View_Analytics_i18n();

		$this->loader->add_action( 'plugins_loaded', $i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		if( class_exists( 'AcrossWP_Plugin_Update_Checker_Github' ) ) {
			new AcrossWP_Plugin_Update_Checker_Github();
		}

		if( class_exists( 'View_Analytics_Admin_Setting_Menu' ) ) {
			View_Analytics_Admin_Setting_Menu::instance();
		}
		
		$admin = new View_Analytics_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'plugin_action_links', $admin, 'modify_plugin_action_links', 10, 2 );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		/**
		 * All class that are release to Pulic Frountend
		 */
		$public = new View_Analytics_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $public, 'enqueue_styles' );
		
		$this->loader->add_action( 'wp_enqueue_scripts', $public, 'enqueue_scripts' );

		$this->loader->add_action( 'wp_enqueue_scripts', $public, 'media_user_view_html' );

		/**
		 * Load the localize Script
		 */
		$this->loader->add_action( 'wp_enqueue_scripts', $public, 'wp_localize_script' );

		/**
		 * Load the Media REST API
		 */
		$rest_api = new View_Analytics_Media_Rest_Controller( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'rest_api_init', $rest_api, 'register_routes', 1000 );

		/**
		 * All class that are release to Public Media Count
		 */
		$public_media_count = new View_Analytics_Public_Media_Count( $this->get_plugin_name(), $this->get_version() );


		/**
		 * All class that are release to Public Profile Count
		 */
		$public_profile_count = new View_Analytics_Public_Profile_Count( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'bp_before_member_home_content', $public_profile_count, 'home_content', 1000 );


		/**
		 * All class that are release to Public Profile Count View
		 */
		$public_profile_view = new View_Analytics_Profile_Count_View( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'bp_setup_nav', $public_profile_view, 'navigation', 1000 );

		/**
		 * All class that are release to Public Group Count
		 */
		$public_group_count = new View_Analytics_Public_Group_Count( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'bp_before_group_home_content', $public_group_count, 'home_content', 1000 );

		/**
		 * All class that are release to Public Group Count View
		 */
		$public_group_view = new View_Analytics_Group_Count_View( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'bp_setup_nav', $public_group_view, 'navigation', 1000 );

		/**
		 * All class that are release to Public Group Count
		 */
		$public_avatar_count = new View_Analytics_Public_Avatar_Count( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'xprofile_avatar_uploaded', $public_avatar_count, 'home_content', 1000, 3 );

		/**
		 * if BuddyBoss is loading
		 */
		if ( $this->common->is_buddyboss() ) {

			/**
			 * Show Media View Count
			 */
			$this->loader->add_action( 'bp_before_activity_activity_content', $public, 'buddyboss_show_view_count', 1000 );

			/**
			 * Load popup template into the Activity Area
			 */
			$this->loader->add_action( 'bp_after_directory_activity_list', $public, 'buddyboss_who_view_media_modal', 1000 );
			$this->loader->add_action( 'bp_after_member_activity_content', $public, 'buddyboss_who_view_media_modal', 1000 );
			$this->loader->add_action( 'bp_after_group_activity_content', $public, 'buddyboss_who_view_media_modal', 1000 );
			$this->loader->add_action( 'bp_after_single_activity_content', $public, 'buddyboss_who_view_media_modal', 1000 );
		} else {

			/**
			 * Show Media View Count
			 */
			$this->loader->add_action( 'get_template_part_attachments/single/view', $public, 'buddypress_show_view_count', 10000, 3 );

			/**
			 * Load popup template into the Activity Area
			 */
			$this->loader->add_action( 'wp_head', $public, 'buddypress_who_view_media_modal', 1000 );

		}

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    View_Analytics_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
