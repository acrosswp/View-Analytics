<?php
/**
 * View Analytics.
 *
 * @package View_Analytics\Updater
 * @since View Analytics 1.0.0
 */

  
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * The Updater-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    View_Analytics
 * @subpackage View_Analytics/Updater
 * @author     AcrossWP <contact@acrosswp.com>
 */
class View_Analytics_Update_Xprofile_Cover extends AcrossWP_Update_Component {

	/**
	 * The View_Analytics_Avatar_Table instance
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $name    The string used to uniquely identify this plugin.
	 */
	protected $table;

	/**
	 * The single instance of the class.
	 *
	 * @var View_Analytics_Loader
	 * @since 1.0.0
	 */
	protected static $_instance = null;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version, $key ) {
		parent::__construct( $plugin_name, $version, $key );
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
	public static function instance( $plugin_name, $version, $key ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $plugin_name, $version, $key );
		}
		return self::$_instance;
	}


	/**
	 * get the table name
	 */
	public function table_name() {
		global $wpdb;
		return $wpdb->users;
	}

	/**
	 * get the table name
	 */
	public function get_all_result() {

		global $wpdb;
		$users_table_name = $this->table_name();
		return $wpdb->get_results( "SELECT ID FROM $users_table_name", ARRAY_N );
	}

		/**
	 * get the table name
	 */
	public function get_result( $per_page, $offset ) {
		global $wpdb;
		$users_table_name = $this->table_name();
		return $wpdb->get_results( "SELECT ID FROM $users_table_name ORDER BY `ID` DESC LIMIT $per_page OFFSET $offset", ARRAY_N );
	}

	public function update_result( $results ) {
		$this->table = View_Analytics_Avatar_Table::instance();

		add_filter( 'bb_get_buddyboss_profile_cover', '__return_false' );

		$public_avatar_count = new View_Analytics_Public_Avatar_Count( $this->plugin_name, $this->version_compare );

		foreach( $results as $result ) {
			if( ! empty( $result[0] ) ) {
				if ( ! empty( $result[0] ) ) {
					$user_id = $result[0];
					$url        = bp_attachments_get_attachment(
						'url',
						array(
							'object_dir' => 'members',
							'item_id'    => $user_id,
						)
					);
					if ( ! empty( $url ) ) {
						$public_avatar_count->update_view_count( $user_id, $user_id, 'xprofile' ,'cover' );
					}
				}
			}
		}
	}
}