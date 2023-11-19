<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Register all actions and filters for the plugin
 *
 * @link       https://acrosswp.com
 * @since      1.0.0
 *
 * @package    View_Analytics
 * @subpackage View_Analytics/includes
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
class View_Analytics_Common {

	/**
	 * The single instance of the class.
	 *
	 * @var View_Analytics_Loader
	 * @since 1.0.0
	 */
	protected static $_instance = null;

	/**
	 * The ID of this media setting view.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	public $table;

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

        $this->table = View_Analytics_Media_Table::instance();

	}

	/**
	 * Main View_Analytics_Loader Instance.
	 *
	 * Ensures only one instance of WooCommerce is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see View_Analytics_Loader()
	 * @return View_Analytics_Loader - Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

    
	/**
     * Return the View Analytics Media Count Key
     */
    public function get_filter_post_value( $key, $filter = FILTER_VALIDATE_INT ) {
		return filter_input( INPUT_POST,  $key, $filter );
    }

	/**
     * Return if BuddyBoss is active or not
     */
    public function is_buddyboss() {
		return defined( 'BP_PLATFORM_VERSION' );
    }

	/**
	 * Check if the current user is allow to view the Media View List
	 */
	public function can_current_user_view_list( $group_id = false ) {

		$current_user_id = get_current_user_id();

		if ( empty( $current_user_id ) ) {
			return false;
		}

		/**
         * If user is site admin
         */
        if( current_user_can('administrator') ) {
            return true;
        }

		if( 
			! empty( $group_id ) 
			&& (
				groups_is_user_admin( $current_user_id, $group_id ) 
				|| groups_is_user_mod( $current_user_id, $group_id ) 
			)
		) {
			return true;
		}

		return false;
	}
}
