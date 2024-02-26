<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://acrosswp.com
 * @since      1.0.0
 *
 * @package    View_Analytics
 * @subpackage View_Analytics/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    View_Analytics
 * @subpackage View_Analytics/admin
 * @author     AcrossWP <contact@acrosswp.com>
 */
class View_Analytics_Admin_Media_Menu {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The ID of this media setting view.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $media_common;
	
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		
		$this->plugin_name = $plugin_name;
		$this->version = $version;

		$this->media_common = View_Analytics_Media_Common::instance();
	}

	/**
	 * Load the menu into the Admin Dashboard area
	 */
	public function menu() {

		$hook = add_submenu_page(
			'view-analytics',
			__( 'Media Analytics', 'view-analytics' ),
			__( 'Media Analytics', 'view-analytics' ),
			'manage_options',
			'view-analytics-media',
			array( $this, 'about_view_analytics' )
		);

	}

	/**
	 * Show the content on the main menu
	 */
	function about_view_analytics() {

		global $wpdb;

		/**
		 * Media view table
		 */
		require_once VIEW_ANALYTICS_PLUGIN_PATH . 'admin/menu/media/class-view-analytics-menu-table.php';

		$table = new View_Analytics_List_Media_Table();
		$table->prepare_items();

		$message = '';
		if ( 'delete' === $table->current_action() ) {
			$count = is_array( $_REQUEST['id'] ) ? count( $_REQUEST['id'] ) : 1;
			$message = '<div class="updated below-h2" id="message"><p>' . sprintf( __( 'Items deleted: %d', 'view-analytics' ), $count ) .'</p></div>';
		}
		?>
		<div class="wrap">
			<div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
			<?php echo $message; ?>
			<?php $table->views(); ?>
			<form id="persons-table" method="GET">
				<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
				<input type="hidden" name="paged" value="<?php echo $_REQUEST['paged'] ?>"/>
				<input type="hidden" name="media_type" value="<?php echo $_REQUEST['media_type'] ?>"/>
				<?php $table->display() ?>
			</form>
		</div>
		<?php
	}

	/**
	 * View All Media that is been view
	 */
	function view_all_media_view() {
		?>
		<h4>All Media and View Stack</h4>
		<div class="chart-container" style="width: 400px;"><canvas id="all-media-view-type"></canvas></div>
		<?php
	}
}