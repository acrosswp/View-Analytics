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
class View_Analytics_Forum_Common extends View_Analytics_Common {

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
        $this->table = View_Analytics_Forum_Table::instance();
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
     * Return the Forum Analytics Media Count Key
     */
    public function view_count_key() {
        return '_view_analytics_forum_table_count_enable';
    }

	/**
	 * Check if the current user is allow to view the Media View List
	 */
	public function can_current_user_view_list( $group_id = false ) {
		return $this->can_current_user_view_list_current_user();
	}

	/**
	 * Show the message about when the user has view the Media
	 */
	public function get_view_body_message( $user_id, $view_count ) {
		$displayname = bp_core_get_user_displayname( $user_id );
		$view = _n( 'time', 'times', $view_count, 'view-analytics' );
		return sprintf( __( '%s saw your forum %s %s.', 'view-analytics' ), $displayname, $view_count, $view );

	}

	/**
	 * Show the message about when the user has view the Media
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

		/**
	 * Get the components of the current Group and Profile
	 * For Media view it is getting overwrittin in the Media Common file
	 */
	public function get_components( $post_id, $bbp_root_slug = '' ) {

		global $wp;
		$current_url = sanitize_text_field( $wp->request );

		$single_components = '';
		$single_object = '';
		$single_variable = explode( '?', esc_url_raw( $current_url ) );

		$primitive = explode( 'page', esc_url_raw( $current_url ) );
		$single_primitive = empty( $primitive[1] ) ? '' : 'page' . $primitive[1];

		if( $bbp_root_slug != $post_id ) {
			$forum_id = get_post_meta( $post_id, '_bbp_forum_id', true );
			if( ! empty( $forum_id ) ) {
				$single_components = $forum_id;

				$single_object = get_post_meta( $post_id, '_bbp_topic_id', true );
			} else {
				$single_components = $post_id;
			}
		} else {
			$single_components = $post_id;
		}

		return array(
			'url' => $current_url,
			'components' => $single_components,
			'object' => $single_object,
			'primitive' => $single_primitive,
			'variable' => empty( $single_variable[1] ) ? '' : $single_variable[1],
		);
	}
}
