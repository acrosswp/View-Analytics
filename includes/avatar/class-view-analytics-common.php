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
class View_Analytics_Avatar_Common extends View_Analytics_Common {

    /**
	 * The single instance of the class.
	 *
	 * @var View_Analytics_Loader
	 * @since 1.0.0
	 */
	protected static $_instance = null;

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

		parent::__construct();
        $this->table = View_Analytics_Avatar_Table::instance();
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
     * Return the Avatar Analytics Media Count Key
     */
    public function view_count_key() {
        return '_view_analytics_avatar_table_count_enable';
    }

	/**
     * Return the View Analytics Avatar Count Key
     */
    public function view_count_enable() {
        return get_option( $this->view_count_key(), true );
    }

	/**
	 * Check if the current user is allow to view the Media View List
	 */
	public function can_current_user_view_list() {
		$user_id = get_current_user_id();

		if ( empty( $user_id ) ) {
			return false;
		}

		/**
         * If user is site admin
         */
        if( current_user_can('administrator') ) {
            return true;
        }

		if( $user_id == bp_displayed_user_id() ) {
			return true;
		}

		return false;
	}

	/**
	 * Show the message about when the user has update the Profile Picture
	 */
	public function get_view_body_message( $user_id, $view_count ) {
		$displayname = bp_core_get_user_displayname( $user_id );
		$view = _n( 'time', 'times', $view_count, 'view-analytics' );
		return sprintf( __( '%s saw your avatar %s %s.', 'view-analytics' ), $displayname, $view_count, $view );

	}

	/**
	 * Show the message about when the user has update the Profile Picture
	 */
	public function get_view_time_message( $action_date, $mysql_time = false ) {

		/**
		 * If current time is empty
		 */
		if ( empty( $mysql_time ) ) {
			global $wpdb;
			$mysql_time = $wpdb->get_var( 'select CURRENT_TIMESTAMP()' );
		}

		$view_time = human_time_diff( strtotime( $action_date ), strtotime( $mysql_time ) );

		return sprintf( __( 'first viewed %s ago.', 'view-analytics' ), $view_time );

	}
}
