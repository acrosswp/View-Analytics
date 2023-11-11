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
class View_Analytics_Admin {

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
	private $media_section_id;

	/**
	 * The ID of this profile setting view.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $profile_section_id;


	/**
	 * The Instance of this media common class.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $media_common;

	/**
	 * The Instance of this profile common class.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $profile_common;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->media_common = View_Analytics_Media_Common::instance();
		$this->profile_common = View_Analytics_Profile_Common::instance();

		$this->media_section_id = $this->media_common->settings();
		$this->profile_section_id = $this->profile_common->profile_settings();
		
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Add Settings link to plugins area.
	 *
	 * @since    1.0.0
	 *
	 * @param array  $links Links array in which we would prepend our link.
	 * @param string $file  Current plugin basename.
	 * @return array Processed links.
	 */
	public function modify_plugin_action_links( $links, $file ) {

		// Return normal links if not BuddyPress.
		if ( VIEW_ANALYTICS_PLUGIN_BASENAME !== $file ) {
			return $links;
		}

		// Add a few links to the existing links array.
		return array_merge(
			$links,
			array(
				'media_settings'      => '<a href="' . esc_url( bp_get_admin_url( 'admin.php?page=bp-settings&tab=bp-media#view-analytics-media-settings' ) ) . '">' . esc_html__( 'Media Settings', 'view-analytics' ) . '</a>',
				'profile_settings'      => '<a href="' . esc_url( bp_get_admin_url( 'admin.php?page=bp-settings&tab=bp-xprofile#view-analytics-profile-settings' ) ) . '">' . esc_html__( 'Profile Settings', 'view-analytics' ) . '</a>',
				'about'         => '<a href="' . esc_url( bp_get_admin_url( '?page=acrosswp' ) ) . '">' . esc_html__( 'About', 'view-analytics' ) . '</a>',
			)
		);
	}


	/**
	 * Register the Setting in BuddyBoss General settings Area
	 *
	 * @since    1.0.0
	 */
	public function media_register_fields( $setting ) {

        // Main General Settings Section
	    $setting->add_section( 
            $this->media_section_id,
            __( 'View Analytics', 'view-analytics' )
        );

	    $args          = array();
	    $setting->add_field( $this->media_common->view_count_key(), __( 'View Media Count', 'view-analytics' ), array( $this, 'view_media_view_count' ), 'intval', $args );
    }

	/**
	 * Allow pinned activity posts.
	 *
	 * @since BuddyBoss 1.0.0
	 */
	public function view_media_view_count() {
		$id = $this->media_common->view_count_key();
		$value = $this->media_common->view_count_enable();
		?>
		<input id="<?php echo $id; ?>" name="<?php echo $id; ?>" type="checkbox" value="1" <?php checked( $value ); ?> />
		<label for="<?php echo $id; ?>"><?php esc_html_e( 'Enable Media View Count', 'view-analytics' ); ?></label>
		<?php
	}

	/**
	 * Register the Setting in BuddyBoss General settings Area
	 *
	 * @since    1.0.0
	 */
	public function profile_register_fields( $setting ) {

        // Main General Settings Section
	    $setting->add_section( 
            $this->profile_section_id,
            __( 'View Analytics', 'view-analytics' )
        );

	    $args          = array();
	    $setting->add_field( $this->profile_common->profile_view_count_key(), __( 'View Profile Count', 'view-analytics' ), array( $this, 'view_profile_view_count' ), 'intval', $args );
    }

	/**
	 * Allow pinned activity posts.
	 *
	 * @since BuddyBoss 1.0.0
	 */
	public function view_profile_view_count() {
		$id = $this->profile_common->profile_view_count_key();
		$value = $this->profile_common->profile_view_count_enable();
		?>
		<input id="<?php echo $id; ?>" name="<?php echo $id; ?>" type="checkbox" value="1" <?php checked( $value ); ?> />
		<label for="<?php echo $id; ?>"><?php esc_html_e( 'Enable Profile View Count', 'view-analytics' ); ?></label>
		<?php
	}

}
