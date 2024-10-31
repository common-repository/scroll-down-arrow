<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Various utility functions
 */
class EPDA_Utilities {

	static $wp_options_cache = array();
	static $postmeta = array();

	const ADMIN_CAPABILITY = 'manage_options';


	/**************************************************************************************************************************
	 *
	 *                     STRING OPERATIONS
	 *
	 **************************************************************************************************************************/

	/**
	 * PHP substr() function returns FALSE if the input string is empty. This method
	 * returns empty string if input is empty or if error occurs.
	 *
	 * @param $string
	 * @param $start
	 * @param null $length
	 *
	 * @return string
	 */
	public static function substr( $string, $start, $length=null ) {
		$result = substr( $string, $start, $length );
		return empty( $result ) ? '' : $result;
	}

	/**************************************************************************************************************************
	 *
	 *                     NUMBER OPERATIONS
	 *
	 **************************************************************************************************************************/

	/**
	 * Determine if value is positive integer ( > 0 )
	 * @param int $number is checked
	 * @return bool
	 */
	public static function is_positive_int( $number ) {

		// no invalid format
		if ( empty( $number ) || ! is_numeric( $number ) ) {
			return false;
		}

		// no non-digit characters
		$numbers_only = preg_replace('/\D/', "", $number );
		if ( empty( $numbers_only ) || $numbers_only != $number ) {
			return false;
		}

		// only positive
		return $numbers_only > 0;
	}


	/**************************************************************************************************************************
	 *
	 *                     AJAX NOTICES
	 *
	 *************************************************************************************************************************/

	/**
	 * wp_die with an error message if nonce invalid or user does not have correct permission
	 *
	 * @param string $context - leave empty if only admin can access this
	 */
	public static function ajax_verify_nonce_and_admin_permission_or_error_die( $context='' ) {

		// check wpnonce
		$wp_nonce = sanitize_text_field( $_POST['_wpnonce_epda_ajax_action'] );
		if ( empty( $wp_nonce ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $wp_nonce ) ), '_wpnonce_epda_ajax_action' ) ) {
			self::ajax_show_error_die( esc_html__( 'Login or refresh this page to edit this page', 'scroll-down-arrow' ) . ' (E01)'  );
		}

		// without context only admins can make changes
		if ( empty( $context ) ) {
			if ( ! current_user_can( self::ADMIN_CAPABILITY ) ) {
				self::ajax_show_error_die( esc_html__( 'Login or refresh this page', 'scroll-down-arrow' ) . ' (E02)'  );
			}
			return;
		}

		// ensure user has correct permission
		/* if ( ! EPDA_Admin_UI_Access::is_user_access_to_context_allowed( $context ) ) {
			self::ajax_show_error_die(__( 'You do not have permission', 'scroll-down-arrow' ) . ' (E02)');
		} */
	}

	/**
	 * AJAX: Used on response back to JS. will call wp_die()
	 *
	 * @param string $message
	 * @param string $title
	 * @param string $type
	 */
	public static function ajax_show_info_die( $message='', $title='', $type='success' ) {
		if ( defined('DOING_AJAX') ) {
			wp_die( wp_json_encode( array( 'message' => EPDA_HTML_Forms::notification_box_bottom( $message, $title, $type ) ) ) );
		}
	}

	/**
	 * AJAX: Used on response back to JS. will call wp_die()
	 *
	 * @param $message
	 * @param string $title
	 * @param string $error_code
	 */
	public static function ajax_show_error_die( $message, $title = '', $error_code = '' ) {
		if ( defined('DOING_AJAX') ) {
			wp_die( wp_json_encode( array( 'error' => true, 'message' => EPDA_HTML_Forms::notification_box_bottom( $message, $title, 'error' ), 'error_code' => $error_code ) ) );
		}
	}

	public static function user_not_logged_in() {
		if ( defined('DOING_AJAX') ) {
			self::ajax_show_error_die( '<p>' . esc_html__( 'You are not logged in. Refresh your page and log in.', 'scroll-down-arrow' ) . '</p>', esc_html__( 'Cannot save your changes', 'scroll-down-arrow' ) );
		}
	}

	/**
	 * Common way to show support link
	 * @return string
	 */
	public static function contact_us_for_support() {

		// show only for admins and editors
		if ( ! function_exists( 'wp_get_current_user' ) ) {
			include( ABSPATH . "wp-includes/pluggable.php" );
		}

		$user = wp_get_current_user();
		if ( empty( $user ) || empty( $user->roles ) ) {
			return '';
		}

		if ( ! in_array( 'administrator', $user->roles ) ) {
			return '';
		}

		return ' ' . esc_html__( 'Please contact us for help', 'scroll-down-arrow' ) . ' ' .
		       '<a href="https://www.echoknowledgebase.com/technical-support/" target="_blank" rel="noopener noreferrer">' . esc_html__( 'here', 'scroll-down-arrow' ) . '</a>.';
	}

	/**
	 * Common way to show feedback link
	 * @return string
	 */
	public static function contact_us_for_feedback() {
		return ' ' .  esc_html__( "We'd love to hear your feedback!", 'scroll-down-arrow' ) . ' ' .
		       '<a href="https://www.echoknowledgebase.com/feature-request/" target="_blank" rel="noopener noreferrer">' . esc_html__( 'click here', 'scroll-down-arrow' ) . '</a>';
	}

	/**
	 * Get string for generic error, optional specific error number, and Contact us link
	 *
	 * For example: EPDA_Utilities::ajax_show_error_die( EPDA_Utilities::report_generic_error( 411 ) );
	 *
	 * @param int $error_number
	 * @param string $message
	 * @param bool $contact_us
	 * @return string
	 */
	public static function report_generic_error( $error_number=0, $message='', $contact_us=true ) {

		if ( empty( $message ) ) {
			$message = esc_html__( 'Error occurred', 'scroll-down-arrow' );
		} else if ( is_wp_error( $message ) ) {
			/** @var WP_Error $message */
			$message = $message->get_error_message();
		} else if ( ! is_string( $message ) ) {
			$message = esc_html__( 'Error occurred', 'scroll-down-arrow' );
		}

		return $message .
				( empty( $error_number ) ? '' : ' (' . $error_number . '). ' ) .
				( empty( $contact_us ) ? '' : self::contact_us_for_support() );
	}


	/**************************************************************************************************************************
	 *
	 *                     SECURITY
	 *
	 *************************************************************************************************************************/

	/**
	 * Return digits only.
	 *
	 * @param $number
	 * @param int $default
	 * @return int|$default
	 */
	public static function sanitize_int( $number, $default=0 ) {

		if ( $number === null || ! is_numeric($number) ) {
			return $default;
		}

		// intval returns 0 on error so handle 0 here first
		if ( $number == 0 ) {
			return 0;
		}

		$number = intval($number);

		return empty($number) ? $default : (int) $number;
	}

	/**
	 * Return text, space, "-" and "_" only.
	 *
	 * @param $text
	 * @param String $default
	 * @return String|$default
	 */
	public static function sanitize_english_text( $text, $default='' ) {

		if ( empty($text) || ! is_string($text) ) {
			return $default;
		}

		$text = preg_replace('/[^A-Za-z0-9 \-_]/', '', $text);

		return empty($text) ? $default : $text;
	}

	/**
	 * Retrieve ID or return error. Used for IDs.
	 *
	 * @param mixed $id is either $id number or array with 'id' index
	 *
	 * @return int|WP_Error
	 */
	public static function sanitize_get_id( $id ) {

		if ( empty( $id) || is_wp_error($id) ) {
			EPDA_Logging::add_log( 'Error occurred (01)' );
			return new WP_Error('E001', 'invalid ID' );
		}

		if ( is_array( $id) ) {
			if ( ! isset( $id['id']) ) {
				EPDA_Logging::add_log( 'Error occurred (02)' );
				return new WP_Error('E002', 'invalid ID' );
			}

			$id_value = $id['id'];
			if ( ! self::is_positive_int( $id_value ) ) {
				EPDA_Logging::add_log( 'Error occurred (03)', $id_value );
				return new WP_Error('E003', 'invalid ID' );
			}

			return (int) $id_value;
		}

		if ( ! self::is_positive_int( $id ) ) {
			EPDA_Logging::add_log( 'Error occurred (04)', $id );
			return new WP_Error('E004', 'invalid ID' );
		}

		return (int) $id;
	}

    /**
     * Sanitize array full of ints.
     *
     * @param $array_values
     * @param string $default
     * @return array|string
     */
	public static function sanitize_int_array( $array_values, $default='' ) {
	    if ( ! is_array($array_values) ) {
	        return $default;
        }

        $sanitized_array = array();
        foreach( $array_values as $value ) {
	        $sanitized_array[] = self::sanitize_int( $value );
        }

        return $sanitized_array;
    }

	/**
	 * Decode and sanitize form fields.
	 *
	 * @param $form
	 * @param $all_fields_specs
	 * @return array
	 */
	public static function retrieve_and_sanitize_form( $form, $all_fields_specs ) {
		if ( empty( $form ) ) {
			return array();
		}

		// first urldecode()
		if ( is_string( $form ) ) {
			parse_str( $form, $submitted_fields );
		} else {
			$submitted_fields = $form;
		}

		// now sanitize each field
		$sanitized_fields = array();
		foreach( $submitted_fields as $submitted_key => $submitted_value ) {

			$field_type = empty($all_fields_specs[$submitted_key]['type']) ? '' : $all_fields_specs[$submitted_key]['type'];

			if ( $field_type == EPDA_Input_Filter::WP_EDITOR ) {
				$sanitized_fields[$submitted_key] = wp_kses( $submitted_value, self::get_extended_html_tags() );

			} elseif ( $field_type == EPDA_Input_Filter::TEXT && ! empty($all_fields_specs[$submitted_key]['allowed_tags']) ) {
				// text input with allowed tags 
				$sanitized_fields[$submitted_key] = wp_kses( $submitted_value, $all_fields_specs[$submitted_key]['allowed_tags'] );

			} else {
				$sanitized_fields[$submitted_key] = sanitize_text_field( $submitted_value );
			}

		}

		return $sanitized_fields;
	}

	/**
	 * Return ints and comma only.
	 *
	 * @param $text
	 * @param String $default
	 * @return String|$default
	 */
	public static function sanitize_comma_separated_ints( $text, $default='' ) {

		if ( empty( $text ) || ! is_string( $text ) ) {
			return $default;
		}

		$text = preg_replace( '/[^0-9 \,_]/', '', $text );

		return empty( $text ) ? $default : $text;
	}

	/**
	 * Retrieve value from POST or GET
	 *
	 * @param $key
	 * @param string $default
	 * @param string $value_type How to treat and sanitize value. Values: text, url
	 * @param int $max_length
	 * @return array|string - empty if not found
	 */
	public static function post( $key, $default = '', $value_type = 'text', $max_length = 0 ) {

		if ( empty( $_POST['_wpnonce_epda_ajax_action'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( sanitize_text_field( wp_unslash( $_POST['_wpnonce_epda_ajax_action'] ) ) ) ), '_wpnonce_epda_ajax_action' ) ) {
			self::ajax_show_error_die( esc_html__( 'Login or refresh this page to edit this page', 'scroll-down-arrow' ) . ' (E01)'  );
		}

		// phpcs:disable WordPress.Security.NonceVerification.Recommended -- Verified below.
		// phpcs:disable WordPress.Security.NonceVerification.Missing -- Verified below.
		if ( empty( $_POST[$key] ) ) {
			return $default;
		}

		// die if nonce invalid or user does not have correct permission
		self::ajax_verify_nonce_and_admin_permission_or_error_die();

		if ( is_object( $_POST[ $key] ) ) {
			return $default;
		}

		if ( is_array( $_POST[$key] ) ) {
			return array_map( 'sanitize_text_field', $_POST[$key] );
		}

		if ( $value_type == 'text-area' ) {
			$value = sanitize_textarea_field( stripslashes( $_POST[$key] ) );  // do not strip line breaks
		} else if ( $value_type == 'email' ) {
			$value = sanitize_email( $_POST[$key] );  // strips out all characters that are not allowable in an email
		} else if ( $value_type == 'url' ) {
			$value = sanitize_url( urldecode( $_POST[$key] ) );
		} else if ( $value_type == 'wp_editor' ) {
			$value = wp_kses( $_POST[$key], self::get_extended_html_tags() );
		} else {
			$value = sanitize_text_field( stripslashes( $_POST[$key] ) );
		}

		// optionally limit the value by length
		if ( ! empty( $max_length ) ) {
			$value = self::substr( $value, 0, $max_length );
		}

		return $value;
	}


	/**************************************************************************************************************************
	 *
	 *                     GET/SAVE/UPDATE AN OPTION
	 *
	 *************************************************************************************************************************/

	/**
	 * Use to get:
	 *  a) PLUGIN-WIDE option not specific to any KB with e p k b prefix.
	 *  b) ADD-ON-SPECIFIC option with ADD-ON prefix.
	 *  b) KB-SPECIFIC configuration with e p k b prefix and KB ID suffix.
	 *
	 * @param $option_name
	 * @param $default
	 * @param bool|false $is_array
	 * @param bool $return_error
	 *
	 * @return array|string|WP_Error or default or error if $return_error is true
	 */
	public static function get_wp_option( $option_name, $default, $is_array=false, $return_error=false ) {
		/** @var $wpdb Wpdb */
		global $wpdb;

		if ( isset(self::$wp_options_cache[$option_name]) ) {
			return self::$wp_options_cache[$option_name];
		}

		// retrieve specific WP option
		$option = $wpdb->get_var( $wpdb->prepare("SELECT option_value FROM $wpdb->options WHERE option_name = %s", $option_name ) );
		if ( $option !== null ) {
			$option = maybe_unserialize( $option );
		}

		if ( $return_error && $option === null && ! empty($wpdb->last_error) ) {
			$wpdb_last_error = $wpdb->last_error;   // add_log changes last_error so store it first
			EPDA_Logging::add_log( "DB failure: " . $wpdb_last_error, 'Option Name: ' . $option_name );
			return new WP_Error(__( 'Error occurred', 'scroll-down-arrow' ), $wpdb_last_error);
		}

		// if WP option is missing then return defaults
		if ( $option === null || ( $is_array && ! is_array( $option ) ) ) {
			return $default;
		}

		self::$wp_options_cache[$option_name] = $option;

		return $option;
	}

	/**
	 * Save WP option
	 * @param $option_name
	 * @param $option_value
	 * @return mixed|WP_Error
	 */
	public static function save_wp_option( $option_name, $option_value ) {
		/** @var $wpdb Wpdb */
		global $wpdb;

		// do not store null
		if ( $option_value === null ) {
			$option_value = '';
		}

		// add or update the option
		$serialized_value = $option_value;

		// check if array or object type of option can be properly serialized
		if ( is_array( $option_value ) || is_object( $option_value ) ) {
			$serialized_value = maybe_serialize($option_value);
			if ( empty( $serialized_value ) ) {
				return new WP_Error( '434', esc_html__( 'Error occurred', 'scroll-down-arrow' ) . ' ' . $option_name );
			}
		}

		$result = $wpdb->query( $wpdb->prepare( "INSERT INTO $wpdb->options (`option_name`, `option_value`, `autoload`) VALUES (%s, %s, %s)
 												 ON DUPLICATE KEY UPDATE `option_name` = VALUES(`option_name`), `option_value` = VALUES(`option_value`), `autoload` = VALUES(`autoload`)",
												$option_name, $serialized_value, 'no' ) );
		if ( $result === false ) {
			EPDA_Logging::add_log( 'Failed to update option', $option_name );
			return new WP_Error( '435', 'Failed to update option ' . $option_name );
		}

		self::$wp_options_cache[$option_name] = $option_value;

		return $option_value;
	}

	/**
	 * Check if Aaccess Manager is considered active.
	 *
	 * @param bool $is_active_check_only
	 * @return bool
	 */
	public static function is_amag_on( $is_active_check_only=true ) {
		/** @var $wpdb Wpdb */
		global $wpdb;

		if ( defined( 'AMAG_PLUGIN_NAME' ) ) {
			return true;
		}

		if ( $is_active_check_only ) {
			return false;
		}

		$table = $wpdb->prefix . 'am'.'gr_kb_groups';
		$result = $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $table ) );

		return ( ! empty( $result ) && ( $table == $result ) );
	}

	public static function is_knowledge_base_enabled() {
		return defined('EP'.'KB_PLUGIN_NAME');
	}



	/**************************************************************************************************************************
	 *
	 *                     OTHER
	 *
	 *************************************************************************************************************************/

	/**
	 * Return string representation of given variable for logging purposes
	 *
	 * @param $var
	 *
	 * @return string
	 */
	public static function get_variable_string( $var ) {

		if ( ! is_array($var) ) {
			return self::get_variable_not_array( $var );
		}

		if ( empty($var) ) {
			return '[empty]';
		}

		$output = 'array';
		$ix = 0;
		foreach ($var as $key => $value) {

            if ( $ix++ > 10 ) {
                $output .= '[.....]';
                break;
            }

			$output .= "[" . $key . " => ";
			if ( ! is_array($value) ) {
				$output .= self::get_variable_not_array( $value ) . "]";
				continue;
			}

			$ix2 = 0;
			$output .= "[";
			$first = true;
			foreach($value as $key2 => $value2) {
                if ( $ix2++ > 10 ) {
                    $output .= '[.....]';
                    break;
                }

				if ( is_array($value2) ) {
                    $output .= print_r($value2, true);
                } else {
					$output .= ( $first ? '' : ', ' ) . $key2 . " => " . self::get_variable_not_array( $value2 );
					$first = false;
					continue;
				}
            }
			$output .= "]]";
		}

		return $output;
	}

	private static function get_variable_not_array( $var ) {

		if ( $var === null ) {
			return '<' . 'null' . '>';
		}

		if ( ! isset($var) ) {
            /** @noinspection HtmlUnknownAttribute */
            return '<' . 'not set' . '>';
		}

		if ( is_array($var) ) {
			return empty($var) ? '[]' : '[...]';
		}

		if ( is_object( $var ) ) {
			return '<' . get_class($var) . '>';
		}

		if ( is_bool( $var ) ) {
			return $var ? 'TRUE' : 'FALSE';
		}

		if ( is_string( $var ) ) {
			return empty( $var ) ? '<empty string>' : $var;
		}

		if ( is_numeric( $var ) ) {
			return $var;
		}

		return '<' . 'unknown' . '>';
	}

	public static function mb_strtolower( $string ) {
		return function_exists( 'mb_strtolower' ) ? mb_strtolower( $string ) : strtolower( $string );
	}

	/**
	 * Return allowed HTML tags and attributes for front-end and wp editor
	 *
	 * @param $is_frontend
	 * @return array
	 */
	public static function get_extended_html_tags( $is_frontend=false ) {

		$extended_post_tags = [];
		if ( $is_frontend || self::is_user_allowed_unfiltered_html() ) {
			$extended_post_tags = [
				'source' => [
					'src' => true,
					'type' => true
				]
			];
		}

		return array_merge( wp_kses_allowed_html( 'post' ), $extended_post_tags );
    }

	/**
	 * Return allowed HTML tags and attributes for ADMIN UI
	 *
	 * @param $extra_tags
	 * @return array
	 */
	public static function get_admin_ui_extended_html_tags( $extra_tags=[] ) {

		$extended_post_tags = [
			'input'     => self::get_admin_ui_extended_html_attributes(),
			'select'    => self::get_admin_ui_extended_html_attributes(),
			'option'    => self::get_admin_ui_extended_html_attributes(),
			'form'      => self::get_admin_ui_extended_html_attributes()
		];

		foreach( $extra_tags as $extra_tag ) {
			$extended_post_tags += [ $extra_tag => self::get_admin_ui_extended_html_attributes() ];
		}

		global $allowedposttags;
		$allowed_post_tags = empty( $allowedposttags ) ? wp_kses_allowed_html( 'post' ) : $allowedposttags;

		return array_merge( $allowed_post_tags, $extended_post_tags );
	}

	/**
	 * Return list of HTML attributes allowed in admin UI
	 *
	 * @return bool[]
	 */
	private static function get_admin_ui_extended_html_attributes() {
		return [
			'name'              => true,
			'type'              => true,
			'value'             => true,
			'class'             => true,
			'style'             => true,
			'data-*'            => true,
			'id'                => true,
			'checked'           => true,
			'selected'          => true,
			'method'            => true,
			'src'               => true,
			'width'             => true,
			'height'            => true,
			'title'             => true,
			'frameborder'       => true,
			'allow'             => true,
			'allowfullscreen'   => true,
			'enctype' 			=> true,
			'autocomplete'      => true,
			'action'            => true,
			'required'          => true,
			'placeholder'       => true
		];
	}

	/**
	 * Add specific CSS styles allowed in admin UI
	 *
	 * @param $safe_style_css
	 * @return array
	 */
	public static function admin_ui_safe_style_css( $safe_style_css ) {
		return array_merge( $safe_style_css, array(
			'display',
		) );
	}

	/**
	 * Wrapper for WordPress 'wp_kses' to use for HTML filtering in admin UI
	 *
	 * @param $html
	 * @param array $extra_tags
	 * @return string
	 */
	public static function admin_ui_wp_kses( $html, $extra_tags=[] ) {

		// allow specific CSS styles that are disabled by default in WordPress core
		add_filter( 'safe_style_css', array( 'EPDA_Utilities', 'admin_ui_safe_style_css' ) );

		$sanitized_html = wp_kses( $html, self::get_admin_ui_extended_html_tags( $extra_tags ) );

		// disallow specific CSS styles
		remove_filter( 'safe_style_css', array( 'EPDA_Utilities', 'admin_ui_safe_style_css' ) );

		return $sanitized_html;
	}

	public static function is_user_allowed_unfiltered_html() {
		if ( ! function_exists( 'wp_get_current_user' ) ) {
			return false;
		}

		return current_user_can( 'unfiltered_html' ) || current_user_can( 'manage_options' );
	}

	/**
	 * Adjust list of safe CSS properties that allowed in wp_kses
	 *
	 * @param $styles
	 *
	 * @return mixed
	 */
	public static function safe_inline_css_properties( $styles ) {
		$styles[] = 'display';
		return $styles;
	}

	/**
	 * Get classes based on theme name for specific targeting
	 * @param $prefix
	 * @return string
	 */
	public static function get_active_theme_classes( $prefix = 'mp' ) {

		$current_theme = wp_get_theme();
		if ( ! empty( $current_theme ) && is_object( $current_theme ) && get_class( $current_theme ) != 'WP_Theme' ) {
			return '';
		}

		// get parent theme class if this is child theme
		$current_theme_parent = $current_theme->parent();
		if ( ! empty( $current_theme_parent ) && is_object( $current_theme_parent ) && get_class( $current_theme_parent ) == 'WP_Theme' ) {
			return 'ecda_' . $prefix . '_active_theme_' . $current_theme_parent->get_stylesheet();
		}

		return 'ecda_' . $prefix . '_active_theme_' . $current_theme->get_stylesheet();
	}

	/**
	 * Same code as wp_slash has in WP-5.5.0 (introduced in WP-3.6.0)
	 * The wp_slash_strings_only is now deprecated (introduced in WP-5.3.0),
	 * But the wp_slash does not handle non-string values until WP-5.5.0
	 * We need this function to support newest and oldest WP versions
	 *
	 * @param string|array $value
	 *
	 * @return string|array
	 */
	public static function slash_strings_only( $value ) {

		if ( is_array( $value ) ) {
			$value = array_map( 'wp_slash', $value );
		}

		if ( is_string( $value ) ) {
			return addslashes( $value );
		}

		return $value;
	}

	public static function get_post_type_labels( $disallowed_post_types, $allowed_post_types=[], $exclude_kb=false ) {

		$cpts = [];

		$wp_cpts = get_post_types( [ 'public' => true ], 'object' );
		foreach ( $wp_cpts as $post_type => $post_type_object ) {

			if ( $exclude_kb && EPDA_KB_Core_Utilities::is_kb_post_type( $post_type ) ) {
				continue;
			}

			if ( in_array( $post_type, $disallowed_post_types ) ) {
				continue;
			}

			if ( ! EPDA_KB_Core_Utilities::is_kb_post_type( $post_type ) && ! empty( $allowed_post_types ) && ! in_array( $post_type, $allowed_post_types ) ) {
				continue;
			}

			$cpts[ $post_type ] = self::get_post_type_label( $post_type_object );
		}

		return $cpts;
	}

	/**
	 * Return pretty label for post type
	 *
	 * @param $post_type_object - see get_post_types() results
	 * @return string
	 */
	public static function get_post_type_label( $post_type_object ) {

		// Standard
		if ( $post_type_object->name == 'post' ) {
			return esc_html__( 'Post' );
		}

		if ( $post_type_object->name == 'page' ) {
			return esc_html__( 'Page' );
		}

		if ( in_array( $post_type_object->name, ['ip_lesson', 'ip_quiz', 'ip_question', 'ip_course'] ) ) {
			return $post_type_object->label . ' (LearnPress)';
		}

		if ( in_array( $post_type_object->name, ['sfwd-lessons', 'sfwd-quiz', 'sfwd-topic'] ) ) {
			return $post_type_object->label . ' (LearnDash)';
		}

		if ( in_array( $post_type_object->name, ['forum', 'topic'] ) ) {
			return $post_type_object->label . ' (bbPress)';
		}

		// BasePress
		if ( $post_type_object->name == 'knowledgebase' && count($post_type_object->taxonomies) == 1 && isset( $post_type_object->taxonomies[0] ) && $post_type_object->taxonomies[0] == 'knowledgebase_cat' ) {
			return $post_type_object->label . ' (BasePress)';
		}

		// weDocs
		if ( $post_type_object->name == 'docs' && count($post_type_object->taxonomies) == 1 && isset( $post_type_object->taxonomies[0] ) && $post_type_object->taxonomies[0] == 'doc_tag' ) {
			return $post_type_object->label . ' (WeDocs)';
		}

		// BetterDocs
		if ( $post_type_object->name == 'docs' && count($post_type_object->taxonomies) == 0 ) {
			return $post_type_object->label . ' (BetterDocs)';
		}

		// Woocommerce
		if ( $post_type_object->name == 'product' ) {
			return $post_type_object->label . ' (WooCommerce)';
		}

		return $post_type_object->label;
	}

	/**
	 * Check if the current theme is a block theme.
	 * @return bool
	 */
	public static function is_block_theme() {
		if ( function_exists( 'wp_is_block_theme' ) ) {
			return (bool) wp_is_block_theme();
		}
		if ( function_exists( 'gutenberg_is_fse_theme' ) ) {
			return (bool) gutenberg_is_fse_theme();
		}

		return false;
	}
}