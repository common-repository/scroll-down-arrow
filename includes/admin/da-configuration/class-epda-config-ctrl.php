<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Handle user submission from Down Arrow
 */
class EPDA_Config_Ctrl {

	public function __construct() {
		add_action( 'wp_ajax_epda_save_da_settings', array( $this, 'save_da_settings' ) );
		add_action( 'wp_ajax_nopriv_epda_save_da_settings', array( 'EPDA_Utilities', 'user_not_logged_in' ) );

		add_action( 'wp_ajax_epda_search_locations', array( $this, 'search_locations' ) );
		add_action( 'wp_ajax_nopriv_epda_search_locations', array( 'EPDA_Utilities', 'user_not_logged_in' ) );
	}

	/**
	 * User updated Down Arrow Settings
	 */
	public function save_da_settings() {

		// die if nonce invalid or user does not have correct permission
		EPDA_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die();

		// retrieve configs
		$da_config = epda_get_instance()->da_config_obj->get_config( true );
		if ( is_wp_error( $da_config ) ) {
			EPDA_Utilities::ajax_show_error_die( EPDA_Utilities::report_generic_error( 31 ) );
		}

		// retrieve and apply changes for configs
		$da_config = self::get_updated_global_config_from_input( $da_config );

		// Update configs
		$updated_da_config = epda_get_instance()->da_config_obj->update_configuration( $da_config );
		if ( is_wp_error( $updated_da_config ) ) {
			EPDA_Logging::add_log( 'Error occurred when saving Global configuration. (35)' );
			EPDA_Utilities::ajax_show_error_die( EPDA_Utilities::report_generic_error( 35, $updated_da_config ) );
		}

		wp_die( wp_json_encode( array(
			'status'    => 'success',
			'message'   => esc_html__( 'Configuration Saved', 'scroll-down-arrow' )
		) ) );
	}

	/**
	 * Retrieve updated Global config from request
	 *
	 * @param $da_config
	 * @return array|null
	 */
	private static function get_updated_global_config_from_input( $da_config ) {

		$da_config['arrow_css_id']              = EPDA_Utilities::post( 'arrow_css_id' );
		$da_config['arrow_css_class']           = EPDA_Utilities::post( 'arrow_css_class' );
		$da_config['arrow_type']                = EPDA_Utilities::post( 'arrow_type' );
		$da_config['animation_type']            = EPDA_Utilities::post( 'animation_type' );
		$da_config['size']                      = EPDA_Utilities::post( 'size' );
		$da_config['color']                     = EPDA_Utilities::post( 'color' );
		$da_config['duration_time']             = EPDA_Utilities::post( 'duration_time' );
		$da_config['bouncing_speed']            = EPDA_Utilities::post( 'bouncing_speed' );
		$da_config['move_to_id']                = EPDA_Utilities::post( 'move_to_id' );
		$da_config['disappear_after_scroll']    = EPDA_Utilities::post( 'disappear_after_scroll' );
		$da_config['enable_bouncing']           = EPDA_Utilities::post( 'enable_bouncing' );
		$da_config['enable_duration']           = EPDA_Utilities::post( 'enable_duration' );
		$da_config['location_pages_list']       = EPDA_Utilities::post( 'location_pages_list', [] );
		$da_config['location_posts_list']       = EPDA_Utilities::post( 'location_posts_list', [] );
		$da_config['location_cpts_list']        = EPDA_Utilities::post( 'location_cpts_list', [] );

		return $da_config;
	}

	/**
	 * Perform search for certain type of Locations
	 */
	public function search_locations() {

		// die if nonce invalid or user does not have correct permission
		EPDA_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die();

		// retrieve post type
		$locations_type = EPDA_Utilities::post( 'locations_type' );
		if ( empty( $locations_type ) ) {
			EPDA_Utilities::ajax_show_error_die( EPDA_Utilities::report_generic_error( 78 ) );
		}

		// retrieve search value
		$search_value = EPDA_Utilities::post( 'search_value' );

		// retrieve excluded Location ids
		$excluded_ids = EPDA_Utilities::post( 'excluded_ids', [] );
		if ( ! is_array( $excluded_ids ) ) {
			$excluded_ids = array();
		}

		$da_config = epda_get_instance()->da_config_obj->get_config( true );
		if ( is_wp_error( $da_config ) ) {
			EPDA_Utilities::ajax_show_error_die( EPDA_Utilities::report_generic_error( 77 ) );
		}

		$config_page_handler = new EPDA_Config_Page();

		wp_die( wp_json_encode( array(
			'status'        => 'success',
			'message'       => 'success',
			'locations'     => $config_page_handler->get_available_locations_list( $locations_type, $search_value, $excluded_ids ),
		) ) );
	}
}
