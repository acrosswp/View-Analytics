<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

/**
 * Fired during plugin license activations
 *
 * @link       https://acrosswp.com
 * @since      0.0.1
 *
 * @package    Post_Anonymously
 * @subpackage Post_Anonymously/includes
 */

/**
 * Fired during plugin licenses.
 *
 * This class defines all code necessary to run during the plugin's licenses and update.
 *
 * @since      0.0.1
 * @package    AcrossWP_Main_Menu_Licenses
 * @subpackage AcrossWP_Main_Menu_Licenses/includes
 * @author     AcrossWP <contact@acrosswp.com>
 */
class AcrossWP_Plugin_Update_Checker_Github {

    /**
	 * The single instance of the class.
	 *
	 * @var Post_Anonymously_Loader
	 * @since 0.0.1
	 */
	protected static $_instance = null;

	/**
	 * Load the licenses for the plugins
	 *
	 * @since 0.0.1
	 */
	protected $packages = array();

	/**
	 * Initialize the collections used to maintain the actions and filters.
	 *
	 * @since    0.0.1
	 */
	public function __construct() {

		/**
		 * Action to do update for the plugins
		 */
		add_action( 'init', array( $this, 'plugin_updater' ) );
	}

	/**
	 * Get the package list
	 */
	public function get_packages() {
		return apply_filters( 'acrosswp_plugins_update_checker_github', $this->packages );
	}

	/**
	 * Update plugin if the licenses is valid
	 */
	public function plugin_updater() {

		/**
		 * Check if the $this->get_packages() is empty or not
		 */
		if( ! empty( $this->get_packages() ) ) {
			foreach ( $this->get_packages() as $package ) {
				$github_repo = $package['repo'];
				$file_path = $package['file_path'];
				$plugin_name_slug = $package['plugin_name_slug'];
				$release_branch = $package['release_branch'];
				
				$myUpdateChecker = PucFactory::buildUpdateChecker(
					$github_repo,
					$file_path,
					$plugin_name_slug
				);
				
				//Set the branch that contains the stable release.
				$myUpdateChecker->setBranch( $release_branch );
			}
		}
	}
}