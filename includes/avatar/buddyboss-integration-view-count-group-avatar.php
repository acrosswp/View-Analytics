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
class View_Analytics_BuddyBoss_Integration_Avatar_Group_Avatar_User_List extends View_Analytics_BuddyBoss_Integration {

	public function __construct() {

		parent::__construct();

		$this->actions = 'bp_admin_setting_groups_register_fields';

		$this->section = array(
			'id' => $this->common->avatar_group_section(),
			'label' => __( 'Group Avatar Analytics Access', 'view-analytics-pro' )
		);

		$this->field = array(
			'id'	=> 'view-analytics-pro-access-control-avatar-group-avatar-view-user-lists',
			'label'	=> __( 'Show Group Avatar Image tab', 'view-analytics-pro' ),
			'args'	=> array()
		);

		$this->content = array(
			'label'	=> __( 'Select which members should have access to view group count, based on:', 'view-analytics-pro' ),
			'component'	=> array(
				'component' => 'group',
				'notices'   => array(
					'disable_avatar_group_avatar_view_count' => array(
						'is_disabled' => View_Analytics_Avatar_Common::instance()->get_view_setting_active( 'show_view_count_group_avatar' ),
						'message'     => __( 'Enable Group User List who has view the Group.', 'view-analytics-pro' ),
						'type'        => 'info',
					),
				),
			)
		);

		$this->filters = View_Analytics_Avatar_Common::instance()->create_hooks_key( 'show_view_count_group_avatar' );

		$this->message_filter = 'view-analytics-admin-avatar-setting';
		$this->message_filter_id = 'show_view_count_group_avatar';
		$this->message_filter_link = 'admin.php?page=bp-settings&tab=bp-groups';

		$this->run();
	}
}
