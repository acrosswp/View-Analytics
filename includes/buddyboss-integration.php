<?php
/**
 * BuddyBoss Compatibility Integration Class.
 *
 * @since AcrossWP 1.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Setup the bp compatibility class.
 *
 * @since AcrossWP 1.0.0
 */
abstract class View_Analytics_BuddyBoss_Integration {

	/**
	 * The action name where this setting should be register
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	public $actions;

	/**
	 * The action name where this setting should be register
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	public $filters;

	/**
	 * The class instance
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The class instance.
	 */
	public $section;

	/**
	 * The class instance
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The class instance.
	 */
	public $field;

	/**
	 * The class instance
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The class instance.
	 */
	public $content;

	/**
	 * The class instance
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The class instance.
	 */
	public $common;
	
	public function __construct() {

		/**
		 * Instance of View_Analytics_Common
		 */
		$this->common = View_Analytics_Common::instance();

	}

	public function run() {

		if ( is_admin() ) {
			$this->backend();
		}

		$this->add_filter();
	}

	/**
	 * Add hook releate to backend here 
	 */
	public function backend() {

		add_action( $this->actions, array( $this, 'add_section' ), 100, 1 );

		add_filter( 'pre_update_option_' . $this->section['id'], array( $this, 'settings_save' ), 10, 2 );


		/**
		 * Alter the message into the backend
		 */
		if( ! empty( $this->message_filter ) ) {
			add_filter( $this->message_filter, array( $this, 'message' ), 1000 );
		}
	}

	/**
	 * Update the message into the backend of across settings
	 */
	public function message( $fields ) {

		foreach( $fields as $index => $field ) {
			/**
			 * Only edit the message of the particular id
			 */
			if( $this->message_filter_id == $field['id'] ) {
				$fields[ $index ]['description'] .= sprintf( __( ' You can also control the access via <a href="%s#%s">Access Control</a>. Notes: This setting should always be enable if you want to control via Access Control.' ), admin_url( $this->message_filter_link ), $this->section['id'] );
			}
		}

		return $fields;
	}

	/**
	 * Add filter to alter the value
	 */
	public function add_filter() {

		add_filter( $this->filters, array( $this, 'callback' ) );
	}

	/**
	 * Show media count to the public or not
	 */
	public function callback( $show ) {
		return $this->has_access( $show, $this->field['id'] );
	}

	/**
	 * Function will return true if user has the access else false
	 *
	 * @param boolean $value return true if user has the access else false
	 *
	 * @since 1.0.0
	 *
	 * @return boolean $has_access whether user has the access or not
	 */
	public function has_access( $value, $key ) {

		if ( empty( $value ) ) {
			return $value;
		}

		$create_media_settings = $this->access_control_settings( $key );
		$has_access            = false;

		if ( empty( $create_media_settings ) || ( isset( $create_media_settings['access-control-type'] ) && empty( $create_media_settings['access-control-type'] ) ) ) {
			$has_access = $value;
		} elseif ( is_array( $create_media_settings ) && isset( $create_media_settings['access-control-type'] ) && ! empty( $create_media_settings['access-control-type'] ) ) {

			$access_controls        = BB_Access_Control::bb_get_access_control_lists();
			$option_access_controls = $create_media_settings['access-control-type'];
			$can_accept             = bb_access_control_has_access( bp_loggedin_user_id(), $access_controls, $option_access_controls, $create_media_settings );

			if ( $can_accept ) {
				$has_access = true;
			}
		}

		return $has_access;
	}

	/**
	 * This function is use to add section into the media
	 */
	function add_section( $setting ){

		// Main General Settings Section
	    $setting->add_section( 
            $this->section['id'],
            $this->section['label']
        );

		$setting->add_field( 
            $this->field['id'],
            $this->field['label'],
			array( $this, 'content' ),
			'',
			$this->field['args']
        );
	}

	/**
	 * Setting to show the content
	 */
	function content() {
		
		bb_platform_pro()->access_control->bb_admin_print_access_control_setting( 
			$this->field['id'], 
			$this->field['id'], 
			'', 
			$this->content['label'], 
			$this->access_control_settings(), 
			false, 
			'', 
			$this->content['component']
		);
	}

	/**
	 * Function will return the create activity field settings data.
	 *
	 * @since 1.1.0
	 *
	 * @return array upload document settings data.
	 */
	public function access_control_settings() {
		
		$default = array(
			'access-control-type'           => '',
			'plugin-access-control-type'    => '',
			'gamipress-access-control-type' => '',
			'access-control-options'        => array(),
		);

		return bp_get_option( $this->field['id'], $default );
	}

	/**
	 * Do not save the settings if user didn't selected the proper settings.
	 *
	 * @param array $new_value selected value.
	 * @param array $old_value old value.
	 *
	 * @since 1.1.0
	 *
	 * @return string
	 */
	function settings_save( $new_value, $old_value ) {

		if ( isset( $new_value ) && isset( $new_value['access-control-type'] ) && empty( $new_value['access-control-type'] ) ) {
			$new_value = '';
		} elseif ( isset( $new_value ) && isset( $new_value['access-control-type'] ) && ! empty( $new_value['access-control-type'] ) && 'gamipress' === $new_value['access-control-type'] && ( empty( $new_value['gamipress-access-control-type'] ) || ! array_key_exists( 'access-control-options', $new_value ) ) ) {
			$new_value = '';
		} elseif ( isset( $new_value ) && isset( $new_value['access-control-type'] ) && ! empty( $new_value['access-control-type'] ) && 'membership' === $new_value['access-control-type'] && ( empty( $new_value['plugin-access-control-type'] ) || ! array_key_exists( 'access-control-options', $new_value ) ) ) {
			$new_value = '';
		} elseif ( isset( $new_value ) && isset( $new_value['access-control-type'] ) && ! empty( $new_value['access-control-type'] ) && ! array_key_exists( 'access-control-options', $new_value ) ) {
			$new_value = '';
		}
		return $new_value;
	}

	/**
	 * Empty Callback function for the display notices only.
	 *
	 * @since 1.1.0
	 */
	public function styling() {
		?>
		<style type="text/css">
			#messages_access_control_block + p.submit {
				display: none;
			}
		</style>
		<?php
	}
}
