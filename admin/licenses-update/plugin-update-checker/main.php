<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Add Github Plugin update checker into the AcrossWP Github Plugin Update Checker
 */
function view_analytics_plugins_update_checker_github( $packages ) {

    $packages[1000] = array(
        'repo' 		        => 'https://github.com/acrosswp/view-analytics',
        'file_path' 		=> VIEW_ANALYTICS_FILES,
        'plugin_name_slug'	=> VIEW_ANALYTICS_PLUGIN_NAME_SLUG,
        'release_branch' 	=> 'main'
    );

    return $packages;
}
add_filter( 'acrosswp_plugins_update_checker_github', 'view_analytics_plugins_update_checker_github', 100, 1 );
