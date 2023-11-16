<?php
/**
 * Acrosss Plugin Update.
 *
 * @package AcrossWP_Plugin_Update\Updater
 * @since Acrosss Plugin Update 1.0.0
 */

  
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;


if( ! class_exists( 'AcrossWP_Plugin_Update' ) ) {
	/**
	 * The Updater-specific functionality of the plugin.
	 *
	 * Defines the plugin name, version, and two examples hooks for how to
	 * enqueue the admin-specific stylesheet and JavaScript.
	 *
	 * @package    AcrossWP_Plugin_Update
	 * @subpackage AcrossWP_Plugin_Update/Updater
	 * @author     AcrossWP <contact@acrosswp.com>
	 */
	class AcrossWP_Plugin_Update {

		/**
		 * The ID of this plugin.
		 *
		 * @since    1.0.0
		 * @access   private
		 * @var      string    $plugin_name    The ID of this plugin.
		 */
		private $plugin_name;

		/**
		 * The DB version slug of this plugin.
		 *
		 * @since    1.0.0
		 * @access   private
		 * @var      string    $plugin_name    The ID of this plugin.
		 */
		private $plugin_name_db_version;

		/**
		 * The version of this plugin.
		 *
		 * @since    1.0.0
		 * @access   private
		 * @var      string    $version    The current version of this plugin.
		 */
		private $version;

		/**
		 * Initialize the class and set its properties.
		 *
		 * @since    1.0.0
		 * @param      string    $plugin_name       The name of this plugin.
		 * @param      string    $version    The version of this plugin.
		 */
		public function __construct( $plugin_name, $version ) {

			$this->plugin_name = $plugin_name;
			$this->plugin_updating = '_' . $this->plugin_name . '_updating';
			$this->plugin_name_db_version = '_' . $this->plugin_name . '_db_version';
			$this->version = $version;


			add_action( 'bp_admin_init', array( $this, 'setup_updater' ) );
		}


		/**
		 * If the Update is running in the background then user this to stop the version update
		 *
		 * If the Update is running in the background then user this to stop the version update
		 *
		 * @return bool True if this is a fresh BP install, otherwise false.
		 * @since AcrossWP Plugin Update 1.0.0
		 */
		public function update_is_running() {
			
			add_filter( 'acrosswp_plugin_updating_' . $this->plugin_name, '__return_true' );

			update_option( $this->plugin_updating, true );
		}

		/**
		 * If the update is completed then use this function
		 *
		 * If the update is completed then use this function
		 *
		 * @return bool True if this is a fresh BP install, otherwise false.
		 * @since AcrossWP Plugin Update 1.0.0
		 */
		public function update_is_completed() {
			
			add_filter( 'acrosswp_plugin_updating_' . $this->plugin_name, '__return_false' );

			update_option( $this->plugin_updating, false );
		}

		/**
		 * Is this a fresh installation of AcrossWP Plugin Update?
		 *
		 * If there is no raw DB version, we infer that this is the first installation.
		 *
		 * @return bool True if this is a fresh BP install, otherwise false.
		 * @since AcrossWP Plugin Update 1.0.0
		 */
		public function is_install() {
			return ! $this->get_db_version_raw();
		}

		/**
		 * Check if the plugin is still updating
		 * 
		 * @since AcrossWP Plugin Update 1.0.0
		 */
		public function is_plugin_updating() {

			return (bool) apply_filters( 'acrosswp_plugin_updating_' . $this->plugin_name, get_option( $this->plugin_updating, false ) );
		}


		/**
		 * Get the DB version of AcrossWP Plugin Update
		 * 
		 * @since AcrossWP Plugin Update 1.0.0
		 */
		public function get_db_version_raw() {
			return get_option( $this->plugin_name_db_version, '0.0.1' );
		}

		/**
		 * Update the BP version stored in the database to the current version.
		 *
		 * @since AcrossWP Plugin Update 1.0.0
		 */
		function version_bump() {
			update_option( $this->plugin_name_db_version, $this->version );
		}

		/**
		 * Set up the AcrossWP Plugin Update updater.
		 *
		 * @since AcrossWP Plugin Update 1.0.0
		 */
		function setup_updater() {
			// Are we running an outdated version of AcrossWP Plugin Update?
			if ( ! $this->is_update() ) {
				return;
			}

			$this->version_updater();
		}

		/**
		 * Is this a AcrossWP Plugin Update update?
		 *
		 * Determined by comparing the registered AcrossWP Plugin Update version to the version
		 * number stored in the database. If the registered version is greater, it's
		 * an update.
		 *
		 * @return bool True if update, otherwise false.
		 * @since AcrossWP Plugin Update 1.0.0
		 */
		function is_update() {

			// Get current DB version.
			$current_db = $this->get_db_version_raw();

			// Get the raw database version.
			$current_live = $this->version;


			$is_update = false;
			if ( version_compare( $current_live, $current_db ) ) {
				$is_update = true;
			}

			// Return the product of version comparison.
			return $is_update;
		}


		/**
		 * Initialize an update or installation of AcrossWP Plugin Update.
		 *
		 * AcrossWP Plugin Update's version updater looks at what the current database version is,
		 * and runs whatever other code is needed - either the "update" or "install"
		 * code.
		 *
		 * This is most often used when the data schema changes, but should also be used
		 * to correct issues with AcrossWP Plugin Update metadata silently on software update.
		 *
		 * @since AcrossWP Plugin Update 1.0.0
		 */
		function version_updater() {

			// Get current DB version.
			$current_db = $this->get_db_version_raw();

			// Get the raw database version.
			$current_live = $this->version;


			do_action( 'acrosswp_plugin_update_' . $this->plugin_name, $this );

			/**
			 * Update the version
			 */
			if( empty( $this->is_plugin_updating() ) ) {
				$this->version_bump();
			}
		}
	}
}