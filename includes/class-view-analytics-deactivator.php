<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Fired during plugin deactivation
 *
 * @link       https://acrosswp.com
 * @since      1.0.0
 *
 * @package    View_Analytics_Deactivator
 * @subpackage View_Analytics_Deactivator/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    View_Analytics_Deactivator
 * @subpackage View_Analytics_Deactivator/includes
 * @author     AcrossWP <contact@acrosswp.com>
 */
class View_Analytics_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {

		require_once( VIEW_ANALYTICS_PLUGIN_PATH . 'vendor/woocommerce/action-scheduler/action-scheduler.php' );

		as_unschedule_all_actions( '_view_analytics_update_xprofile_avatar' );
	}

}
