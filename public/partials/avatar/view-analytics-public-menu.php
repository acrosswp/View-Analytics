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
class View_Analytics_Avatar_Count_View {

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
	 * The ID of this group setting view.
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

		$this->common = View_Analytics_Avatar_Common::instance();
	}

	/**
	 * Show menu option on the profile Page
	 */
	public function profile_navigation() {

		/**
		 * Check if the curret user has access to view the Profile View Tab
		 */
		if ( $this->common->can_current_user_view_list() ) {
			bp_core_new_nav_item(
				array(
					'name'                => __( 'Profile Avatar Update', 'view-analytics' ),
					'slug'                => 'profile-avatar-update',
					'screen_function'     => array( $this, 'xprofile_avatar_view_manage' )
				)
			);

			bp_core_new_nav_item(
				array(
					'name'                => __( 'Profile Cover Update', 'view-analytics' ),
					'slug'                => 'profile-cover-update',
					'screen_function'     => array( $this, 'xprofile_cover_view_manage' )
				)
			);
		}
	}


	/**
	 * Show menu option on the Group Page
	 */
	public function group_navigation() {

		$current_group = groups_get_current_group();

		/**
		 * Check if the curret user has access to view the Profile View Tab
		 */
		if ( ! empty( $current_group ) && $this->common->can_current_user_view_list( $current_group->id ) ) {

			$group_link = bp_get_group_permalink( $current_group );

			bp_core_new_subnav_item( array(
				'name' => __( 'Group Avatar Update', 'view-analytics' ),
				'slug' => 'group-avatar-update',
				'parent_slug' => bp_get_current_group_slug(),
				'parent_url' => $group_link,
				'position' => 100,
				'screen_function' => array( $this, 'group_avatar_view_manage' ),
				'user_has_access' => $this->common->can_current_user_view_list() // Only the logged in user can access this on his/her profile
			) );

			bp_core_new_subnav_item( array(
				'name' => __( 'Group Cover Image Update', 'view-analytics' ),
				'slug' => 'group-cover-update',
				'parent_slug' => bp_get_current_group_slug(),
				'parent_url' => $group_link,
				'position' => 100,
				'screen_function' => array( $this, 'group_cover_view_manage' ),
				'user_has_access' => $this->common->can_current_user_view_list() // Only the logged in user can access this on his/her profile
			) );
		}
	}

	public function xprofile_avatar_view_manage() {
		add_action( 'bp_template_content', array( $this, 'xprofile_avatar_content' ) );
		bp_core_load_template( 'template_content' );
	}

	function xprofile_avatar_content() {
		$user_id = get_current_user_id();
		$message = __( 'Profile Avatar is not yet updated', 'view-analytics' );
		$this->content( $user_id, 'xprofile', 'avatar', $message );
	}

	public function xprofile_cover_view_manage() {
		add_action( 'bp_template_content', array( $this, 'xprofile_cover_content' ) );
		bp_core_load_template( 'template_content' );
	}

	function xprofile_cover_content() {
		$user_id = get_current_user_id();
		$message = __( 'Profile Cover Image is not yet updated', 'view-analytics' );
		$this->content( $user_id, 'xprofile', 'cover', $message );
	}

	public function group_avatar_view_manage() {
		add_action( 'bp_template_content', array( $this, 'group_avatar_content' ) );
		bp_core_load_template( 'template_content' );
	}

	function group_avatar_content() {
		$group_id = bp_get_group_id();
		$message = __( 'No one has update this Group Avatar', 'view-analytics' );
		$this->content( $group_id, 'group', 'avatar', $message );
	}

	public function group_cover_view_manage() {
		add_action( 'bp_template_content', array( $this, 'group_cover_content' ) );
		bp_core_load_template( 'template_content' );
	}

	function group_cover_content() {
		$group_id = bp_get_group_id();
		$message = __( 'No one has update this Group Cover Image', 'view-analytics' );
		$this->content( $group_id, 'group', 'cover', $message );
	}


	public function content( $key_id, $type, $action, $message ) {
		$view_details = $this->common->table->get_details( $key_id, $type, $action );

		if ( empty( $view_details ) ) {
			echo $message;
		} else { ?>
			<ul class="notification-list bb-nouveau-list bs-item-list list-view">
				<?php
				global $wpdb;
				$mysql_time = $wpdb->get_var( 'select CURRENT_TIMESTAMP()' );
				foreach( $view_details as $view_detail ) {
					$link = bp_core_get_user_domain( $view_detail->user_id );
					?>
					<li class="bs-item-wrap">
						<div class="notification-avatar">
							<a href="<?php echo $link; ?>" class="">
								<?php
								echo bp_core_fetch_avatar(
									array(
										'item_id' => $view_detail->user_id,
										'object'  => 'user',
									)
								);
								?>
							</a>
						</div>

						<div class="notification-content">
							<span>
								<a href="<?php echo $link; ?>"><?php echo $this->common->get_view_body_message( $view_detail->user_id ); ?></a>
							</span>
							<span class="posted"><?php echo $this->common->get_view_time_message( $view_detail->action_date, $mysql_time ); ?></span>
						</div>
					</li>
					<?php
				}
				?>
			</ul><?php
		}
	}
}
