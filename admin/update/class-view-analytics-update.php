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

		$activity_table_name = $bp->activity->table_name;
		
		$per_page = 20;

		$key = '_pinned_comment_update_1_0_4';

		$update_running = get_option( $key, false );
		if ( empty( $update_running ) ) {
			$results = $wpdb->get_results( "SELECT id FROM $activity_table_name WHERE `type` = 'activity_comment'", ARRAY_N );
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

			$results = $wpdb->get_results( "SELECT id FROM $activity_table_name WHERE `type` = 'activity_comment' ORDER BY `id` DESC LIMIT $per_page OFFSET $offset", ARRAY_N );

			/**
			 * Check if this is empty or not
			 */
			if ( ! empty( $results ) ) {
				$activity_meta_table_name = $bp->activity->table_name_meta;
				foreach( $results as $result ) {
					if( ! empty( $result[0] ) ) {
						$activity_id = $result[0];
						$pinned_comment = bp_activity_get_meta( $activity_id, '_pinned_comment', true );
						if ( empty( $pinned_comment ) ) {
							bp_activity_update_meta( $activity_id, '_pinned_comment', 0 );
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