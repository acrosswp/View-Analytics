<?php
/**
 * BuddyBoss Document Component Class.
 *
 * @package BuddyBoss\Document\Loader
 * @since   BuddyBoss 1.4.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

function profile_new_nav_item() {

global $bp;

bp_core_new_nav_item(
array(
	'name'                => 'Extra Tab',
	'slug'                => 'extra_tab',
	'default_subnav_slug' => 'extra_sub_tab', // We add this submenu item below 
	'screen_function'     => 'view_manage_tab_main'
)
);
}

add_action( 'bp_setup_nav', 'profile_new_nav_item', 10 );

function view_manage_tab_main() {
add_action( 'bp_template_content', 'bp_template_content_main_function' );
bp_core_load_template( 'template_content' );
}

function bp_template_content_main_function() {
if ( ! is_user_logged_in() ) {
	wp_login_form( array( 'echo' => true ) );
}
}

function profile_new_subnav_item() {
global $bp;

bp_core_new_subnav_item( array(
	'name'            => 'Extra Sub Tab',
	'slug'            => 'extra_sub_tab',
	'parent_url'      => $bp->loggedin_user->domain . $bp->bp_nav[ 'extra_tab' ][ 'slug' ] . '/',
	'parent_slug'     => $bp->bp_nav[ 'extra_tab' ][ 'slug' ],
	'position'        => 10,
	'screen_function' => 'view_manage_sub_tab_main'
) );
}

add_action( 'bp_setup_nav', 'profile_new_subnav_item', 10 );

function view_manage_sub_tab_main() {
add_action( 'bp_template_content', 'bp_template_content_sub_function' );
bp_core_load_template( 'template_content' );
}

function bp_template_content_sub_function() {
if ( is_user_logged_in() ) {
	//Add shortcode to display content in sub tab
} else {
	wp_login_form( array( 'echo' => true ) );
}
}