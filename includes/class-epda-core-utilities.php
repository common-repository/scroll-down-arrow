<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Utility functions just for this add-on
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPDA_Core_Utilities {

	/**
	 * Remove non-numeric characters from number
	 * @param $number
	 * @return mixed
	 */
	public static function filter_number( $number ) {
		return empty( $number ) ? 0 : preg_replace("/[^0-9,.]/", "", $number );
	}
}
