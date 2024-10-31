<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Setup links and information on Plugins WordPress page
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 */


/**
 * Adds various links for plugin on the Plugins page displayed on the left
 *
 * @param   array $links contains current links for this plugin
 * @return  array returns an array of links
 */
function epda_add_plugin_action_links ( $links ) {
	$my_links = array(
		esc_html__( 'Configuration', 'scroll-down-arrow' ) => '<a href="' . admin_url( 'options-general.php?page=epda-down-arrow-config' ) . '">' . esc_html__( 'Configuration', 'scroll-down-arrow' ) . '</a>',
		esc_html__( 'Documentation', 'scroll-down-arrow' ) => '<a href="https://www.echoknowledgebase.com/documentation/how-to-use-scroll-down-arrow/" target="_blank">' . esc_html__( 'Docs', 'scroll-down-arrow' ) . '</a>',
		esc_html__( 'Support', 'scroll-down-arrow' )       => '<a href="https://www.echoknowledgebase.com/contact-us/?inquiry-type=technical" target="_blank">' . esc_html__( 'Support', 'scroll-down-arrow' ) . '</a>'
	);

	return array_merge( $my_links, $links );
}
add_filter( 'plugin_action_links_' . plugin_basename( EPDA_Scroll_Down_Arrow::$plugin_file ), 'epda_add_plugin_action_links' , 10, 2 );

