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
class View_Analytics_Media_Count_View {

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

		$this->common = View_Analytics_Media_Common::instance();
	}

		/**
	 * Run the view media user list
	 */
	public function media_user_view_html() {
		?>
		<script type="text/html" id="tmpl-view-analytics-media-view-user">
			<li class="view-user">
				<div class="view-user-avatar">
					<img alt="" src="{{data.user_avatar_url}}" class="avatar avatar-32 photo" height="32" width="32" loading="lazy" decoding="async"> 
				</div>
				<div class="view-desc">
					<a href="{{data.user_profile_url}}">{{data.user_display_name}}</a> {{data.message}}
				</div>
			</li>
		</script>
		<?php
	}

	/**
     * Show the view count into the Frountend
     * Hook: bp_before_activity_activity_content
     */
    public function buddypress_show_view_count() {

		if ( $this->common->view_count_enable() ) {

			$medium = bp_attachments_get_queried_object();

			if ( empty( $medium ) ) {
				return;
			}

			$this->show_view_count( $medium->id );
		}
    }

	/**
     * Show the view count into the Frountend
     * Hook: bp_before_activity_activity_content
     */
    public function buddyboss_show_view_count() {

		if ( $this->common->view_count_show_view_count() ) {

			$ajax_action = $this->common->is_lightbox_ajax();

			if ( empty( $ajax_action ) ) {
				return;
			}

			$attachment_id = $this->common->get_lightbox_attachment_id( $ajax_action );
			
			if ( empty( $attachment_id ) ) {
				return;
			}

			$this->show_view_count( $attachment_id );
		}
    }

	/**
	 * Show view count HTML
	 */
	public function show_view_count( $key_id ) {
		
		$details	= $this->common->table->get_details( $key_id );
		$counts		= empty( $details['user_count'] ) ? 0 : absint( $details['user_count'] );
		$author_id	= empty( $details['author_id'] ) ? 0 : absint( $details['author_id'] );

		$view = _n( 'View', 'Views', $counts, 'view-analytics' );
		$counts = apply_filters( 'view_analytics_view_count_content', array( 'count' => $counts, 'text' => $view ), $key_id );

		if ( $this->common->view_count_show_user_list( $author_id ) ) {
			/**
			 * Load popup template into the Activity Area
			 */
			$this->buddyboss_who_view_media_modal();

			echo "<div id='view_list' class='view-analytics-media-views'><span current-media-view='". $key_id ."'>" . implode( ' ', $counts ) . '</span> </div>';
		} else {
			echo "<div class='view-analytics-media-views'><span>" . implode( ' ', $counts ) . '</span></div>';
		}
	}

	/**
	 * Run the Pinn Post comment
	 */
	public function buddypress_who_view_media_modal() {
		add_thickbox();
		?>
		<div id="view-analytics-view-confirmation-modal" class="buddypress-view-confirmation-modal bb-action-popup" style="display: none;">
			<div class="bb-action-popup-content">
				<ul class="media-view-list"></ul>
			</div>
		</div>
		<a href="#TB_inline?&width=450&height=550&inlineId=view-analytics-view-confirmation-modal" name="<?php esc_html_e( 'People Who viewed This', 'view-analytics' ); ?>" style="display: none;" class="thickbox hidden hide view-confirmation-modal-tigger">Show Popup</a>
		<?php
	}

	/**
	 * Run the Pinn Post comment
	 */
	public function buddyboss_who_view_media_modal() {
		?>
		<div id="view-analytics-view-confirmation-modal" class="buddyboss-view-confirmation-modal bb-action-popup" style="display: none;">
			<transition name="modal">
				<div class="modal-mask bb-white bbm-model-wrap bbm-uploader-model-wrap">
					<div class="modal-wrapper">
						<div class="modal-container">
							<header class="bb-model-header">
								<h4><?php esc_html_e( 'People Who viewed This', 'view-analytics' ); ?></h4>
								<a class="bb-close-action-popup bb-model-close-button" id="bp-confirmation-model-close" href="#">
									<span class="bb-icon-l bb-icon-times"></span>
								</a>
							</header>
							<div class="bb-action-popup-content">
								<ul class="media-view-list"></ul>
							</div>
						</div>
					</div>
				</div>
			</transition>
		</div>
		<?php
	}
}
