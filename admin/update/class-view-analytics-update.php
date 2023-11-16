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
class View_Analytics_Update {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	public $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	public $version;

	/**
	 * The View_Analytics_Avatar_Table instance
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $name    The string used to uniquely identify this plugin.
	 */
	protected $table;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name		= $plugin_name;
		$this->version_compare	= $version;

		/**
		 * Called all the action and filter inside this functions
		 */
		$this->hooks();
	}

	/**
	 * This contain all the action and filter that are using for updating the plugins
	 */
	public function hooks() {

		add_action( 'acrosswp_plugin_update_' . $this->plugin_name, array( $this, 'plugin_update' ) );
	}

	/**
	 * Main Plugin Update 
	 */
	public function plugin_update( $acrosswp_plugin_update ) {

		/**
		 * Main Update
		 */
		$this->version_1_0_4( $acrosswp_plugin_update );
	}

	/**
	 * Update to version 1.0.0
	 */
	public function version_1_0_4( $acrosswp_plugin_update ) {

		global $wpdb;
		$bp = buddypress();

		/**
		 * Stop the latest version update in DB
		 */
		$acrosswp_plugin_update->update_is_running();

		$users_table_name = $wpdb->users;
		
		$per_page = 10;

		$key = '_view_analytics_update_1_0_4';

		$update_running = get_option( $key, false );
		if ( empty( $update_running ) ) {
			$results = $wpdb->get_results( "SELECT ID FROM $users_table_name", ARRAY_N );
			$count_result = count( $results );
			
			$total_page = $count_result <= $per_page ? 1 : ceil( $count_result/$per_page );
			$current_page = 0;

			$update_running = array(
				'current_page' => $current_page,
				'count_result' => $count_result,
				'total_page' => $total_page,
			);

			update_option( $key, $update_running );
		
		} elseif( isset( $update_running['current_page'] ) ) {
			$current_page = $update_running['current_page'];
			$total_page = $update_running['total_page'];
			$offset = $current_page * $per_page;
			$current_page++;

			$results = $wpdb->get_results( "SELECT ID FROM $users_table_name ORDER BY `ID` DESC LIMIT $per_page OFFSET $offset", ARRAY_N );

			/**
			 * Check if this is empty or not
			 */
			if ( ! empty( $results ) ) {
				$this->table = View_Analytics_Avatar_Table::instance();
				add_filter( 'bp_core_default_avatar_user', '__return_false' );

				$public_avatar_count = new View_Analytics_Public_Avatar_Count( $this->plugin_name, $this->version_compare );

				foreach( $results as $result ) {
					if( ! empty( $result[0] ) ) {
						if ( ! empty( $result[0] ) ) {
							$user_id = $result[0];
							$url = bp_get_displayed_user_avatar( 
								array(
									'item_id' => $user_id,
									'html' => false,
								)
							);

							$public_avatar_count->update_view_count( $user_id );
						}
					}
				}
			}

			if( $current_page == $total_page ) {
				$update_running = 'completed';
			} else {
				$update_running['current_page'] = $current_page;
			}

			update_option( $key, $update_running );
		}

		if( 'completed' == $update_running ) {
			/**
			 * Allow the latest version update in DB
			 */
			$acrosswp_plugin_update->update_is_completed();
		}
	}
}