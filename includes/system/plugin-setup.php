<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Activate the plugin
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
*/

/**
 * Activate this plugin i.e. setup tables, data etc.
 * NOT invoked on plugin updates
 *
 * @param bool $network_wide - If the plugin is being network-activated
 */
function epda_activate_plugin( $network_wide=false ) {
	global $wpdb;

	if ( is_multisite() && $network_wide ) {
		foreach ( $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs LIMIT 100" ) as $blog_id ) {
			switch_to_blog( $blog_id );
			epda_activate_plugin_do();
			restore_current_blog();
		}
	} else {
		epda_activate_plugin_do();
	}
}
register_activation_hook( EPDA_Scroll_Down_Arrow::$plugin_file, 'epda_activate_plugin' );

function epda_activate_plugin_do() {

	// true if the plugin was activated for the first time since installation
	$plugin_version = get_option( 'epda_version' );
	if ( empty( $plugin_version ) ) {

		// prepare Down Arrow config
		$config = epda_get_instance()->da_config_obj->get_config();
		epda_get_instance()->da_config_obj->update_configuration( $config );

		EPDA_Utilities::save_wp_option( 'epda_version', EPDA_Scroll_Down_Arrow::$version );
	}

	// check config
	$result = epda_get_instance()->da_config_obj->get_config( true );
	if ( is_wp_error( $result ) ) {
		// prepare Down Arrow config
		$config = epda_get_instance()->da_config_obj->get_config();
		epda_get_instance()->da_config_obj->update_configuration( $config );

	}

	set_transient( '_epda_plugin_activated', true, 3600 );

}

/**
 * User deactivates this plugin so refresh the permalinks
 */
function epda_deactivation() {
}
register_deactivation_hook( EPDA_Scroll_Down_Arrow::$plugin_file, 'epda_deactivation' );
