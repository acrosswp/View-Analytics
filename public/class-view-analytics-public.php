<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://acrosswp.com
 * @since      1.0.0
 *
 * @package    View_Analytics
 * @subpackage View_Analytics/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    View_Analytics
 * @subpackage View_Analytics/public
 * @author     AcrossWP <contact@acrosswp.com>
 */
class View_Analytics_Public {

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
	private $common;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		$this->common = View_Analytics_Common::instance();

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in View_Analytics_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The View_Analytics_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, VIEW_ANALYTICS_PLUGIN_URL . 'assets/dist/css/frontend-style.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in View_Analytics_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The View_Analytics_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, VIEW_ANALYTICS_PLUGIN_URL . 'assets/dist/js/frontend-script.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Register the Localize Script for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function wp_localize_script() {

		wp_localize_script( $this->plugin_name, 'view_analytics_object',
			array( 
				'attachment_view_endpoint' => rest_url( 'view-analytics/v1/attachment/' ),
				'nonce' => wp_create_nonce( "wp_rest" ),
			)
		);
	}

	/**
	 * Run the Pinn Post comment
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
