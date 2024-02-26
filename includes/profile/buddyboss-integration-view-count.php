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
class View_Analytics_BuddyBoss_Integration_Profile_User_List extends View_Analytics_BuddyBoss_Integration {

	public function __construct() {

		parent::__construct();

		$this->actions = 'bp_admin_setting_xprofile_register_fields';

		$this->section = array(
			'id' => $this->common->profile_section(),
			'label' => __( 'Profile Analytics Access', 'view-analytics-pro' )
		);

		$this->field = array(
			'id'	=> 'view-analytics-pro-access-control-profile-view-user-lists',
			'label'	=> __( 'Show User Lists', 'view-analytics-pro' ),
			'args'	=> array()
		);

		$this->content = array(
			'label'	=> __( 'Select which members should have access to view profile count, based on:', 'view-analytics-pro' ),
			'component'	=> array(
				'component' => 'xprofile',
				'notices'   => array(
					'disable_profile_view_count' => array(
						'is_disabled' => View_Analyticsfile_Common::instance()->get_view_setting_active( 'show_view_count' ),
						'message'     => __( 'Enable Profile User List who has view the Profile.', 'view-analytics-pro' ),
						'type'        => 'info',
					),
				),
			)
		);

		$this->filters = View_Analyticsfile_Common::instance()->create_hooks_key( 'show_view_count' );


		$this->message_filter = 'view-analytics-admin-profile-setting';
		$this->message_filter_id = 'show_view_count';
		$this->message_filter_link = 'admin.php?page=bp-settings&tab=bp-xprofile';

		$this->run();
	}
}
