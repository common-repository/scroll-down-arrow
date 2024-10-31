<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Setup WordPress menu for this plugin
 */

/**
 *  Register plugin menus
 */
function epda_add_plugin_menus() {
	add_options_page( esc_html__( 'Scroll Down Arrow', 'scroll-down-arrow' ), esc_html__( 'Scroll Down Arrow', 'scroll-down-arrow' ), 'manage_options', 'epda-down-arrow-config', array( new EPDA_Config_Page(), 'display_page' ) );
}
add_action( 'admin_menu', 'epda_add_plugin_menus', 10 );
