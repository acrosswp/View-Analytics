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
class View_Analytics_Public_Display {

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
	 * The ID of this media setting view.
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

        $this->common = View_Analytics_Common::instance();

	}

    /**
     * Show the view count into the Frountend
     * Hook: bp_before_activity_activity_content
     */
    public function show_view_count() {

		if ( $this->common->media_view_count_enable() ) {

			$ajax_action = $this->common->is_media_lightbox_ajax();

			if ( empty( $ajax_action ) ) {
				return;
			}

			$attachment_id = $this->common->get_lightbox_attachment_id( $ajax_action );
			$counts = $this->common->media_get_count( $attachment_id );

			if ( empty( $attachment_id ) ) {
				return;
			}

			$view = _n( 'View', 'Views', $counts, 'view-analytics' );
			$counts = apply_filters( 'view_analytics_view_count_content', array( 'count' => $counts, 'text' => $view ), $attachment_id );

			if( $this->common->can_current_user_media_view_list( $attachment_id ) ) {
				echo "<div id='view_list' class='view-analytics-media-views'><span>" . implode( ' ', $counts ) . '</span> </div>';
				echo "<input class='current-media-view' type='hidden' value='" . $attachment_id . "'>";
			} else {
				echo "<div class='view-analytics-media-views'><span>" . implode( ' ', $counts ) . '</span></div>';
			}
		}
    }

	/**
	 * Run the Pinn Post comment
	 */
	public function who_view_media_modal() {
		?>
		<div id="view-analytics-view-confirmation-modal" class="view-analytics-view-confirmation-modal bb-action-popup" style="display: none;">
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
