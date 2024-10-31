<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Manage plugin configuration (plugin-wide ) in the database.
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPDA_Config_DB {

	// Prefix for WP option name that stores settings
	const EPDA_CONFIG_NAME = 'epda_config';

	private $cached_settings = null;

	/**
	 * Get settings from the WP Options table.
	 * If settings are missing then use defaults.
	 *
	 * For Global config returns array - default is specs config
	 * For non-Global config returns array of arrays - default is array with one specs config as the first element
	 *
	 * @param bool $return_error
	 * @return array|WP_Error return current HD configuration
	 */
	public function get_config( $return_error=false ) {

		// retrieve config if already cached
		if ( is_array( $this->cached_settings ) ) {
			return $this->cached_settings;
		}

		// retrieve Plugin config
		$config = get_option( self::EPDA_CONFIG_NAME, [] );
		if ( empty( $config ) || ! is_array( $config ) ) {

			// return WP_Error if specified by parameter
			if ( $return_error ) {
				return new WP_Error( 'DB231', esc_html__( "Did not find configuration. Try to deactivate and reactivate Arrow Down plugin to see if this fixes the issue.", 'scroll-down-arrow' ) );
			}

			// return default config
			return EPDA_Config_Specs::get_default_da_config();
		}

		// cached the config for future use
		$this->cached_settings = $config;

		return $config;
	}

	/**
	 * Return specific value from the plugin settings values. Values are automatically trimmed.
	 *
	 * @param $setting_name
	 *
	 * @param string $default
	 * @return string with value or empty string if this settings not found
	 */
	public function get_value( $setting_name, $default='' ) {

		if ( empty( $setting_name ) ) {
			return $default;
		}

		$da_config = $this->get_config();
		if ( isset( $da_config[$setting_name] ) ) {
			return $da_config[$setting_name];
		}

		$default_settings = EPDA_Config_Specs::get_defaults();

		return isset( $default_settings[$setting_name] ) ? $default_settings[$setting_name] : $default;
	}

	/**
	 * Set specific value in DA Configuration
	 *
	 * @param $key
	 * @param $value
	 * @return array|WP_Error
	 */
	public function set_value( $key, $value ) {

		$da_config = $this->get_config( true );
		if ( is_wp_error( $da_config ) ) {
			return $da_config;
		}

		$da_config[$key] = $value;

		return $this->update_configuration( $da_config );
    }

	/**
	 * Update DA Configuration. Use default if config missing.
	 *
	 * @param array $config contains DA configuration or empty if adding default configuration
	 *
	 * @return array|WP_Error configuration that was updated
	 */
	public function update_configuration( $config ) {

		if ( ! is_array( $config ) || empty( $config ) ) {
			return new WP_Error( 'update_config', 'Configuration is empty' );
		}

		$fields_specification = EPDA_Config_Specs::get_fields_specification();
		$input_filter = new EPDA_Input_Filter();

		// first sanitize and validate input
		$sanitized_config = $input_filter->validate_and_sanitize_specs( $config, $fields_specification );
		if ( is_wp_error( $sanitized_config ) ) {
			EPDA_Logging::add_log( 'Failed to sanitize Plugin settings', $sanitized_config );
			return $sanitized_config;
		}

		// use defaults for missing configuration
		$sanitized_config = wp_parse_args( $sanitized_config, EPDA_Config_Specs::get_default_da_config() );

		update_option( self::EPDA_CONFIG_NAME, $sanitized_config );
		wp_cache_flush();

		return $sanitized_config;
	}
}