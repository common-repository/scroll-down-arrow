<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Handles settings specifications.
 */
class EPDA_Config_Specs {
	
	const DEFAULT_ID = 1;
	const HOME_PAGE = 0;

	public static function get_defaults() {
		return array(
			'label'               => esc_html__( 'Label', 'scroll-down-arrow' ),
			'type'                => EPDA_Input_Filter::TEXT,
			'mandatory'           => true,
			'max'                 => '20',
			'min'                 => '0',
			'options'             => array(),
			'internal'            => false,
			'default'             => '',
			'is_pro'              => false,
			'location_pages_list' => [],
			'location_posts_list' => [],
			'location_cpts_list'  => []
		);
	}

	/**
	 * Defines data needed for display, initialization and validation/sanitation of settings
	 *
	 * ALL FIELDS ARE MANDATORY by default ( otherwise use 'mandatory' => false )
	 *
	 * @return array with settings specification
	 */
	public static function get_fields_specification() {

		return array(
			'location_pages_list'                       => array(
				'name'       => 'location_pages_list',
				'type'        => EPDA_Input_Filter::INTERNAL_ARRAY,
				'internal'    => true,
				'default'     => array()
			),
			'location_posts_list'                       => array(
				'name'       => 'location_posts_list',
				'type'        => EPDA_Input_Filter::INTERNAL_ARRAY,
				'internal'    => true,
				'default'     => array()
			),
			'location_cpts_list'                        => array(
				'name'       => 'location_cpts_list',
				'type'        => EPDA_Input_Filter::INTERNAL_ARRAY,
				'internal'    => true,
				'default'     => array()
			),
			'arrow_css_id' => array (
				'label'         => esc_html__( 'CSS ID', 'scroll-down-arrow' ),
				'name'     		=> 'arrow_css_id',
				'type'			=> EPDA_Input_Filter::TEXT,
				'max' 			=> '40',
				'mandatory'     => false,
				'default' 		=> ''
			),
			'arrow_css_class' => array (
				'label'         => esc_html__( 'CSS Class', 'scroll-down-arrow' ),
				'name'      	=> 'arrow_css_class',
				'type'			=> EPDA_Input_Filter::TEXT,
				'max' 			=> '40',
				'mandatory'     => false,
				'default' 		=> ''
			),
			'arrow_type' => array (
				'label'         => esc_html__( 'Arrow Type', 'scroll-down-arrow' ),
				'name'      	=> 'arrow_type',
				'type'			=> EPDA_Input_Filter::SELECTION,
				'options'       => array(
					'epda-icon-arrow-regular' => 'Regular',
					'epda-icon-arrow-circle'  => 'With Circle',
					'epda-icon-arrow-sharp'   => 'Sharp Edges',
					'epda-icon-arrow-soft'    => 'Rounded Edges',
					'epda-icon-arrow-point'   => 'With Tail'
				),
				'default' 		=> 'epda-icon-arrow-regular'
			),
			'animation_type' => array (
				'label'         => esc_html__( 'Animation Type', 'scroll-down-arrow' ),
				'name'      	=> 'animation_type',
				'type'			=> EPDA_Input_Filter::SELECTION,
				'options'       => array(
					'bounce-effect-1' => 'Bounce 1',
					'bounce-effect-2' => 'Bounce 2',
					'bounce-effect-3' => 'Bounce 3',
					'pulse'           => 'Pulse',
					'slide_down'      => 'Slide Down',
					'fade_down'       => 'Fade Down'
				),
				'default' 		=> 'bounce-effect-1'
			),
			'size' => array (
				'label'         => esc_html__( 'Arrow Size (px)', 'scroll-down-arrow' ),
				'name'     		=> 'size',
				'type'			=> EPDA_Input_Filter::NUMBER,
				'max' 			=> '800',
				'default' 		=> '80'
			),
			'color' => array (
				'label'         => esc_html__( 'Arrow Color', 'scroll-down-arrow' ),
				'name'      	=> 'color',
				'type'			=> EPDA_Input_Filter::COLOR_HEX,
				'max' 			=> '10',
				'default' 		=> '1776C0'
			),
			'enable_duration' => array (
				'label'         => esc_html__( 'Duration', 'scroll-down-arrow' ),
				'name'      	=> 'enable_duration',
				'type'			=> EPDA_Input_Filter::SELECTION,
				'options'   => array(
					'off' => esc_html__( 'Disabled', 'scroll-down-arrow' ),
					'on'  => esc_html__( 'Enabled', 'scroll-down-arrow' ),
				),
				'default'   => 'off'
			),
			'duration_time' => array (
				'label'         => esc_html__( 'Duration Time ( Seconds )', 'scroll-down-arrow' ),
				'name'     		=> 'duration_time',
				'type'			=> EPDA_Input_Filter::NUMBER,
				'max' 			=> '10',
				'default' 		=> '5'
			),
			'enable_bouncing' => array (
				'label'         => esc_html__( 'Bouncing', 'scroll-down-arrow' ),
				'name'     		=> 'enable_bouncing',
				'type'			=> EPDA_Input_Filter::SELECTION,
				'options'   => array(
					'off' => esc_html__( 'Disabled', 'scroll-down-arrow' ),
					'on'  => esc_html__( 'Enabled ', 'scroll-down-arrow' ),
				),
				'default'   => 'on'
			),
			'bouncing_speed' => array (
				'label'         => esc_html__( 'Bouncing Speed', 'scroll-down-arrow' ),
				'name'     		=> 'bouncing_speed',
				'type'			=> EPDA_Input_Filter::SELECTION,
				'options'   => array(
					'3' => esc_html__( 'Slow', 'scroll-down-arrow' ),
					'2' => esc_html__( 'Moderate', 'scroll-down-arrow' ),
					'1'  => esc_html__( 'Fast ', 'scroll-down-arrow' ),
				),
				'default' 		=> '2'
			),
			'move_to_id' => array (
				'label'         => esc_html__( 'Click Moves to ID', 'scroll-down-arrow' ),
				'name'      	=> 'move_to_id',
				'type'			=> EPDA_Input_Filter::TEXT,
				'max' 			=> '40',
				'mandatory'     => false,
				'default' 		=> ''
			),
			'disappear_after_scroll' => array (
				'label'         => esc_html__( 'Disappear After Scrolling', 'scroll-down-arrow' ),
				'name'     		=> 'disappear_after_scroll',
				'type'			=> EPDA_Input_Filter::SELECTION,
				'options'   => array(
					'off' => esc_html__( 'Disable', 'scroll-down-arrow' ),
					'on'  => esc_html__( 'Enable ', 'scroll-down-arrow' ),
				),
				'default'   => 'off'
			),
		);
	}

	/**
	 * Get Plugin default configuration
	 *
	 * @return array contains default setting values
	 */
	public static function get_default_da_config() {

		$setting_specs = self::get_fields_specification();

		$default_configuration = array();
		foreach( $setting_specs as $key => $spec ) {
			$default = isset( $spec['default'] ) ? $spec['default'] : '';
			$default_configuration += array( $key => $default );
		}

		return $default_configuration;
	}
}