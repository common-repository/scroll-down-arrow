<?php  if ( ! defined( 'ABSPATH' ) ) exit;

spl_autoload_register(array( 'EPDA_Autoloader', 'autoload'));

/**
 * A class which contains the autoload function, that the spl_autoload_register
 * will use to autoload PHP classes.
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 */
class EPDA_Autoloader {

	public static function autoload( $class ) {
		static $classes = null;

		if ( $classes === null ) {
			$classes = array(

				// CORE
				'epda_utilities'                 =>  'includes/class-epda-utilities.php',
				'epda_core_utilities'            =>  'includes/class-epda-core-utilities.php',
				'epda_html_elements'             =>  'includes/class-epda-html-elements.php',
				'epda_html_admin'                =>  'includes/class-epda-html-admin.php',
				'epda_input_filter'              =>  'includes/class-epda-input-filter.php',
				'epda_html_forms'                =>  'includes/class-epda-html-forms.php',

				// SYSTEM
				'epda_logging'                   =>  'includes/system/class-epda-logging.php',
				'epda_kb_core_utilities'         =>  'includes/system/class-epda-kb-core-utilities.php',

				// DA CONFIGURATION
				'epda_config_specs'              =>  'includes/admin/da-configuration/class-epda-config-specs.php',
				'epda_config_db'                 =>  'includes/admin/da-configuration/class-epda-config-db.php',
				'epda_config_page'               =>  'includes/admin/da-configuration/class-epda-config-page.php',
				'epda_config_ctrl'               =>  'includes/admin/da-configuration/class-epda-config-ctrl.php',

				// FEATURES
				'epda_arrow_view'                =>  'includes/features/class-epda-arrow-view.php',

			);
		}

		$cn = strtolower( $class );
		if ( isset( $classes[ $cn ] ) ) {
			/** @noinspection PhpIncludeInspection */
			include_once( plugin_dir_path( EPDA_Scroll_Down_Arrow::$plugin_file ) . $classes[ $cn ] );
		}
	}
}
