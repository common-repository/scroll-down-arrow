<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Various utility functions
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPDA_KB_Core_Utilities {

	const KB_POST_TYPE_PREFIX = 'ep'.'kb_post_type_';
	const KB_CATEGORY_TAXONOMY_SUFFIX = '_category';
	const KB_TAG_TAXONOMY_SUFFIX = '_tag';
	const DEFAULT_KB_ID = 1;

	/**
	 * Is this KB post type?
	 *
	 * @param $post_type
	 * @return bool
	 */
	public static function is_kb_post_type( $post_type ) {
		if ( empty( $post_type ) || ! is_string( $post_type ) ) {
			return false;
		}
		// we are only interested in KB articles
		return strncmp( $post_type, self::KB_POST_TYPE_PREFIX, strlen( self::KB_POST_TYPE_PREFIX ) ) == 0;
	}
}