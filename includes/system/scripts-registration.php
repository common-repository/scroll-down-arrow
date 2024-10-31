<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**  Register JS and CSS files  */

/**
 * FRONT-END pages using our plugin features
 */
function epda_load_public_resources() {
	epda_register_public_resources();
	epda_enqueue_public_resources();
}
add_action( 'epda_enqueue_arrow_resources', 'epda_load_public_resources' );

/**
 * Register for FRONT-END pages using our plugin features
 */
function epda_register_public_resources() {

	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

	wp_register_style( 'epda-public-styles', EPDA_Scroll_Down_Arrow::$plugin_url . 'css/public-styles' . $suffix . '.css', array(), EPDA_Scroll_Down_Arrow::$version );
	wp_register_script( 'epda-public-scripts', EPDA_Scroll_Down_Arrow::$plugin_url . 'js/public-scripts' . $suffix . '.js', array( 'jquery' ), EPDA_Scroll_Down_Arrow::$version );
	wp_localize_script( 'epda-public-scripts', 'epda_vars', array(
		'msg_try_again'         => esc_html__( 'Please try again later.', 'scroll-down-arrow' ),
		'error_occurred'        => esc_html__( 'Error occurred (16)', 'scroll-down-arrow' ),
		'not_saved'             => esc_html__( 'Error occurred - configuration NOT saved.', 'scroll-down-arrow' ),
		'unknown_error'         => esc_html__( 'unknown error (17)', 'scroll-down-arrow' ),
		'reload_try_again'      => esc_html__( 'Please reload the page and try again.', 'scroll-down-arrow' ),
		'save_config'           => esc_html__( 'Saving configuration', 'scroll-down-arrow' ),
		'input_required'        => esc_html__( 'Input is required', 'scroll-down-arrow' ),
	));
}

/**
 * Queue for FRONT-END pages using our plugin features
 */
function epda_enqueue_public_resources() {
	wp_enqueue_style( 'epda-public-styles' );
}

function epda_enqueue_help_dialog() {
	wp_enqueue_script( 'epda-public-scripts' );
}
add_action( 'epda_enqueue_help_dialog_scripts', 'epda_enqueue_help_dialog' );

/**
 * ADMIN-PLUGIN MENU PAGES (Plugin settings, reports, lists etc.)
 */
function epda_load_admin_plugin_pages_resources( $hook ) {
	global $pagenow;

	if ( $pagenow != 'options-general.php' ) {
		return;
	}

	// if SCRIPT_DEBUG is off then use minified scripts
	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_style( 'epda-admin-plugin-pages-styles', EPDA_Scroll_Down_Arrow::$plugin_url . 'css/admin-plugin-pages' . $suffix . '.css', array(), EPDA_Scroll_Down_Arrow::$version );
	wp_enqueue_style( 'epda-admin-icon-styles', EPDA_Scroll_Down_Arrow::$plugin_url . 'css/admin-icon' . $suffix . '.css', array(), EPDA_Scroll_Down_Arrow::$version );
	wp_enqueue_script( 'epda-admin-plugin-pages-scripts', EPDA_Scroll_Down_Arrow::$plugin_url . 'js/admin-plugin-pages' . $suffix . '.js',
					array('jquery', 'jquery-ui-core','jquery-ui-dialog','jquery-effects-core','jquery-effects-bounce', 'jquery-ui-sortable'), EPDA_Scroll_Down_Arrow::$version );

	wp_localize_script( 'epda-admin-plugin-pages-scripts', 'epda_vars', array(
					'msg_try_again'         => esc_html__( 'Please try again later.', 'scroll-down-arrow' ),
					'error_occurred'        => esc_html__( 'Error occurred (11)', 'scroll-down-arrow' ),
					'not_saved'             => esc_html__( 'Error occurred - configuration NOT saved (12).', 'scroll-down-arrow' ),
					'unknown_error'         => esc_html__( 'unknown error (13)', 'scroll-down-arrow' ),
					'reload_try_again'      => esc_html__( 'Please reload the page and try again.', 'scroll-down-arrow' ),
					'save_config'           => esc_html__( 'Saving configuration', 'scroll-down-arrow' ),
					'input_required'        => esc_html__( 'Input is required', 'scroll-down-arrow' ),
				));

	wp_enqueue_style( 'wp-jquery-ui-dialog' );

	// used by WordPress color picker  ( wpColorPicker() )
	wp_localize_script( 'wp-color-picker', 'wpColorPickerL10n',
		array(
			'clear'            =>   esc_html__( 'Reset', 'scroll-down-arrow' ),
			'clearAriaLabel'   =>   esc_html__( 'Reset color', 'scroll-down-arrow' ),
			'defaultString'    =>   esc_html__( 'Default', 'scroll-down-arrow' ),
			'defaultAriaLabel' =>   esc_html__( 'Select default color', 'scroll-down-arrow' ),
			'pick'             =>   '',
			'defaultLabel'     =>   esc_html__( 'Color value', 'scroll-down-arrow' ),
		));
	wp_enqueue_script( 'wp-color-picker' );
}
add_action( 'admin_enqueue_scripts', 'epda_load_admin_plugin_pages_resources', 101 );

// Down Arrow Configuration page
function epda_load_admin_config_script() {
	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
	wp_enqueue_script( 'epda-admin-config-scripts', EPDA_Scroll_Down_Arrow::$plugin_url . 'js/admin-plugin-config' . $suffix . '.js', array( 'jquery'), EPDA_Scroll_Down_Arrow::$version );
	wp_localize_script( 'epda-admin-config-scripts', 'epda_vars', [
		'nonce' => wp_create_nonce( "_wpnonce_epda_ajax_action" ),
	] );
}
