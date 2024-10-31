<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 *  Display the Scroll Down Arrow on the frontend
 */
class EPDA_Arrow_View {

	public function __construct() {
		add_action( 'wp_footer', array( $this, 'output_arrow' ) );
	}

	/**
	 * Output Arrow on pages that user selected in settings
	 * @return void|null
	 */
	public function output_arrow() {

		$da_config = epda_get_instance()->da_config_obj->get_config();

		if ( empty( $da_config['location_pages_list'] ) && empty( $da_config['location_posts_list'] ) && empty( $da_config['location_cpts_list'] ) ) {
			return;
		}

		if ( ! $this->is_arrow_on_home_page( $da_config ) && ! $this->is_arrow_on_post_page( $da_config ) ) {
			return;
		}

		echo $this->display_arrow( $da_config );  // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		do_action( 'epda_enqueue_arrow_resources' );
		do_action( 'epda_enqueue_help_dialog_scripts' );
	}

	private function is_arrow_on_home_page( $da_config ) {
		return in_array( '0', $da_config['location_pages_list'] ) && is_front_page();
	}

	private function is_arrow_on_post_page( $da_config ) {

		if ( ( in_array( '0', $da_config['location_pages_list'] ) && count( $da_config['location_pages_list'] ) == 1 ) &&
			empty( $da_config['location_posts_list'] ) && empty( $da_config['location_cpts_list'] ) ) {
			return false;
		}

		// is this page or post or main page to display the arrow on?
		$post = get_queried_object();

		// woocommerce shop page. Queried object is not WP_Post for woo shop page, so we need special code for edge case
		if ( function_exists( 'is_shop' ) && function_exists( 'wc_get_page_id' ) && is_shop() ) {
			$page_id = wc_get_page_id( 'shop' );
			if ( empty( $page_id) || $page_id < 1 ) {
				return false;
			}
			$post = get_post( $page_id );
		}

		if ( empty( $post ) || get_class( $post ) !== 'WP_Post' || empty( $post->ID ) ) {
			return false;
		}

		if ( $post->post_type == 'post' || $post->post_type == 'page' ) {
			$post_type = $post->post_type;
		} else {
			$post_type = 'cpt';
		}

		// check matching widget by post id or CPT
		$key = $post_type == 'cpt' ? $post->post_type : $post->ID;

		return in_array( $key, $da_config['location_' . $post_type . 's_list'] );
	}

	/**
	 * Display Arrow
	 *
	 * @param array $da_config
	 * @return string of HTML output
	 */
	public function display_arrow($da_config ) {

		$disappear_after_scroll = $da_config['disappear_after_scroll'] == 'on' ? 'true' : 'false';
		$enable_bouncing = $da_config['enable_bouncing'] == 'on' ? 'true' : 'false';
		$enable_duration = $da_config['enable_duration'] == 'on' ? 'true' : 'false';

		$output = '<div id="ep-arrow">';

		if ( $da_config['move_to_id'] ) {
			$output .= '<a href="' . esc_url( '#' . str_replace( '#','', $da_config['move_to_id'] ) ) . '">';
		}

		//Fix Alignment based on Size of arrow
		$output .= '<div id="' . esc_attr( $da_config['arrow_css_id'] ) . '" ';
		$output .= ' style="';
		$output .= 'font-size:' . EPDA_Core_Utilities::filter_number( $da_config['size'] ) . 'px; ';
		$output .= 'color: #' . esc_attr( str_replace( '#', '', $da_config['color'] ) ) . '; ';
		$output .= 'animation-duration:' . EPDA_Core_Utilities::filter_number( $da_config['bouncing_speed'] )  . 's; ';
		$output .= 'margin-left:' . ' -' . EPDA_Core_Utilities::filter_number( $da_config['size'] ) / 2 . 'px; ';
		$output .= '" ';
		$output .= 'data-duration_time="' . EPDA_Core_Utilities::filter_number( $da_config['duration_time'] ) . '" ';
		$output .= 'data-scrolling="' . esc_attr( $disappear_after_scroll ) . '" ';
		$output .= 'data-enable_duration="' . esc_attr( $enable_duration ) . '" ';
		$output .= 'data-enable_bouncing="' . esc_attr( $enable_bouncing ) . '" ';
		$output .= 'class="scroll-down-arrow ';
		$output .= sanitize_html_class( $da_config['arrow_type'] ).' ';
		$output .= sanitize_html_class( $da_config['arrow_css_class'] ).' ';
		if ( $enable_bouncing == 'true' ) {
			$output .= sanitize_html_class( $da_config['animation_type'] ).' ';
		}
		$output .= '">';
		$output .= '</div>';    //id arrow id

		if ( $da_config['move_to_id'] ) {
			$output .= '</a>';  //link
		}

		$output .= '</div>';    //id ep-arrow

		return $output;
	}
}
