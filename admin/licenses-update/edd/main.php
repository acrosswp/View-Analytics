<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Add EDD licences into the AcrossWP EDD licences menu
 */
function view_analytics_edd_plugins_licenses( $licenses ) {

    $licenses[1000] = array(
        'id' 		=> 705,
        'key' 		=> VIEW_ANALYTICS_PLUGIN_NAME_SLUG,
        'version'	=> VIEW_ANALYTICS_VERSION,
        'name' 		=> VIEW_ANALYTICS_PLUGIN_NAME
    );

    return $licenses;
}
add_filter( 'acrosswp_edd_plugins_licenses', 'view_analytics_edd_plugins_licenses', 100, 1 );