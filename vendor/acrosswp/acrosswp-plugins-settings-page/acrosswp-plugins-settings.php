<?php
/**
 * BuddyBoss Compatibility Integration Class.
 *
 * @since BuddyBoss 1.1.5
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Check if the class does not exits then only allow the file to add
 */
if( ! class_exists( 'AcrossWP_Sub_Menu' ) ) {
	/**
	 * Fired during plugin licences.
	 *
	 * This class defines all code necessary to run during the plugin's licences and update.
	 *
	 * @since      0.0.1
	 * @package    AcrossWP_Sub_Menu
	 * @subpackage AcrossWP_Sub_Menu/includes
	 * @author     AcrossWP <contact@acrosswp.com>
	 */
	abstract class AcrossWP_Sub_Menu {

		/**
		 * The single instance of the class AcrossWP_Main_Menu.
		 *
		 * @var AcrossWP_Main_Menu
		 * @since 0.0.1
		 */
		public $main_menu;

		/**
		 * Initialize the collections used to maintain the actions and filters.
		 *
		 * @since    0.0.1
		 */
		public function __construct() {
			$this->main_menu = AcrossWP_Main_Menu::instance();

			/**
			 * Load the sub menu and there fields
			 */
			$this->loading();
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
		 * Adds the plugin license page to the admin menu.
		 *
		 * @return void
		 */
		abstract function loading();
	}	
}