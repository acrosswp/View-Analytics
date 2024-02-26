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
class View_Analytics_BuddyBoss_Integration_Media_User_List extends View_Analytics_BuddyBoss_Integration {

	public function __construct() {

		parent::__construct();

		$this->actions = 'bp_admin_setting_media_register_fields';

		$this->section = array(
			'id' => $this->common->media_section(),
			'label' => __( 'Media Analytics Access', 'view-analytics-pro' )
		);

		$this->field = array(
			'id'	=> 'view-analytics-pro-access-control-media-view-user-lists',
			'label'	=> __( 'Show User Lists', 'view-analytics-pro' ),
			'args'	=> array()
		);

		$this->content = array(
			'label'	=> __( 'Select which members should have access to view user list of media view, based on:', 'view-analytics-pro' ),
			'component'	=> array(
				'component' => 'media',
				'notices'   => array(
					'disable_media_view_count' => array(
						'is_disabled' => View_Analytics_Media_Common::instance()->get_view_setting_active( 'show_view_user_list' ),
						'message'     => __( 'Enable Media User List who has view the Media.', 'view-analytics-pro' ),
						'type'        => 'info',
					),
				),
			)
		);

		$this->filters = View_Analytics_Media_Common::instance()->create_hooks_key( 'show_view_user_list' );


		$this->message_filter = 'view-analytics-admin-media-setting';
		$this->message_filter_id = 'show_view_user_list';
		$this->message_filter_link = 'admin.php?page=bp-settings&tab=bp-media';

		$this->run();
	}
}
