<?php
/**
 * BuddyBoss Compatibility Integration Class.
 *
 * @since BuddyBoss 1.1.5
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Setup the bp compatibility class.
 *
 * @since BuddyBoss 1.1.5
 */
class View_Analytics_BuddyBoss_Integration extends BP_Integration {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	public function __construct( $plugin_name ) {

		$this->plugin_name = $plugin_name;

		$this->start(
			$this->plugin_name,
			__( 'View Analytics', 'view-analytics' ),
			$this->plugin_name,
			array(
				'required_plugin' => array(),
			)
		);
	}

	/**
	 * Register admin integration tab
	 */
	public function setup_admin_integration_tab() {

		/**
		 * The class responsible for loading the dependency main class
		 * core plugin.
		 */
		require_once VIEW_ANALYTICS_PLUGIN_PATH . 'admin/integration/buddyboss-addon-integration-tab.php';

		new View_Analytics_Admin_Integration_Tab(
			"{$this->id}",
			$this->name,
			array(
				'root_path'       => VIEW_ANALYTICS_PLUGIN_PATH . 'admin/integration',
				'root_url'        => VIEW_ANALYTICS_PLUGIN_URL . 'admin/integration',
				'required_plugin' => $this->required_plugin,
			)
		);
	}
}
