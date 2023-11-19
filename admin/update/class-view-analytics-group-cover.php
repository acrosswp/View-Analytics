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
class View_Analytics_Update_Group_Cover extends AcrossWP_Update_Component {

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
	public function __construct( $plugin_name, $version, $key ) {
		parent::__construct( $plugin_name, $version );

		$this->set_key( $key );

		$this->update();
	}

	/**
	 * get the table name
	 */
	public function table_name() {
		global $wpdb;
		return $wpdb->prefix . 'bp_groups';
	}

	/**
	 * get the table name
	 */
	public function get_all_result() {

		global $wpdb;
		$users_table_name = $this->table_name();
		return $wpdb->get_results( "SELECT id FROM $users_table_name", ARRAY_N );
	}

		/**
	 * get the table name
	 */
	public function get_result( $per_page, $offset ) {
		global $wpdb;
		$users_table_name = $this->table_name();
		return $wpdb->get_results( "SELECT id FROM $users_table_name ORDER BY `id` DESC LIMIT $per_page OFFSET $offset", ARRAY_N );
	}

	public function update_result( $results ) {

		$this->table = View_Analytics_Avatar_Table::instance();

		add_filter( 'bb_get_default_profile_group_cover', '__return_false' );

		$public_avatar_count = new View_Analytics_Public_Avatar_Count( $this->plugin_name, $this->version_compare );

		$user_id = get_current_user_id();

		foreach( $results as $result ) {
			if( ! empty( $result[0] ) ) {
				if ( ! empty( $result[0] ) ) {
					$group_id = $result[0];
					$url        = bp_attachments_get_attachment(
						'url',
						array(
							'object_dir' => 'groups',
							'item_id'    => $group_id,
						)
					);

					if ( ! empty( $url ) ) {
						$public_avatar_count->doing_update_view_count( $group_id, $user_id, 'group' ,'cover' );
					}
				}
			}
		}
	}
}