<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://acrosswp.com
 * @since      1.0.0
 *
 * @package    View_Analytics
 * @subpackage View_Analytics/public/partials
 */

/**
 * Register all actions and filters for the plugin.
 *
 * Maintain a list of all hooks that are registered throughout
 * the plugin, and register them with the WordPress API. Call the
 * run function to execute the list of actions and filters.
 *
 * @package    View_Analytics
 * @subpackage View_Analytics/includes
 * @author     AcrossWP <contact@acrosswp.com>
 */
class View_Analytics_Profile_Count_View {

    /**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * The ID of this profile setting view.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $common;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		$this->common = View_Analytics_Profile_Common::instance();
	}


	public function navigation() {

		/**
		 * Check if the curret user has access to view the Profile View Tab
		 */
		if ( 
			$this->common->view_count_show_view_count()
			&& 
			$this->common->can_current_user_view_list()
		) {
			bp_core_new_nav_item(
				array(
					'name'                => __( 'Profile View', 'view-analytics' ),
					'slug'                => 'profile-view',
					'screen_function'     => array( $this, 'view_manage' )
				)
			);
		}
	}

	public function view_manage() {
		add_action( 'bp_template_content', array( $this, 'content' ) );
		bp_core_load_template( 'template_content' );
	}

	function content() {
		$author_id = get_current_user_id();
		$view_details = $this->common->table->get_details( $author_id );
		if ( empty( $view_details ) ) {
			echo __( 'No one has view your Profile', 'view-analytics' );
		} else { ?>
			<ul class="notification-list bb-nouveau-list bs-item-list list-view">
				<?php
				global $wpdb;
				$mysql_time = $wpdb->get_var( 'select CURRENT_TIMESTAMP()' );
				$user_lists = unserialize( $view_details['users_list'] );

				foreach( $user_lists as $viewer_id ) {
					$link = bp_core_get_user_domain( $viewer_id );
					$log_view = $this->common->table->get_user_log_view( $viewer_id, $author_id );
					?>
					<li class="bs-item-wrap">
						<div class="notification-avatar">
							<a href="<?php echo $link; ?>" class="">
								<?php
								echo bp_core_fetch_avatar(
									array(
										'item_id' => $viewer_id,
										'object'  => 'user',
									)
								);
								?>
							</a>
						</div>

						<div class="notification-content">
							<span>
								<a href="<?php echo $link; ?>"><?php echo $this->common->get_view_body_message( $viewer_id, $log_view['user_view_count'] ); ?></a>
							</span>
							<span class="posted"><?php echo $this->common->get_view_time_message( $log_view['action_date'], $mysql_time ); ?></span>
						</div>
					</li>
					<?php
				}
				?>
			</ul><?php
		}
	}
}
