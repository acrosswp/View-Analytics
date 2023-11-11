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
class View_Analytics_Profile_Common extends View_Analytics_Common {

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
        $this->table = View_Analytics_Profile_Table::instance();
	}

	/**
     * Return the View Analytics Media Count Key
     */
    public function profile_settings() {
        return 'view-analytics-profile-settings';
    }

	/**
     * Return the Profile Analytics Media Count Key
     */
    public function profile_view_count_key() {
        return '_view_analytics_profile_table_count_enable';
    }

	/**
     * Return the View Analytics Profile Count Key
     */
    public function profile_view_count_enable() {
        return get_option( $this->profile_view_count_key(), true );
    }
}
