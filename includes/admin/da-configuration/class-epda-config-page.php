<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Display Down Arrow configuration page
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPDA_Config_Page {

	const ORDER_LOCATIONS_BY = ['post_title', 'post_modified', 'unassigned_first', 'assigned_first'];

	private $message = array(); // error/warning/success messages
	private $empty_home_page_selected = false;

	/**
	 * Displays the Down Arrow Config page with top panel
	 */
	public function display_page() {

        if ( ! current_user_can( EPDA_Utilities::ADMIN_CAPABILITY ) ) {
	        EPDA_Utilities::ajax_show_error_die( esc_html__( 'You do not have permission to edit Down Arrow config.', 'scroll-down-arrow' ) );
	        return;
        }

        $da_config = epda_get_instance()->da_config_obj->get_config( true );
        if ( is_wp_error( $da_config ) ) {
	        EPDA_HTML_Admin::display_config_error_page( $da_config );
	        return;
        }

        $admin_page_views = $this->get_regular_views_config( $da_config );  ?>

        <!-- Admin Page Wrap -->
        <div id="epda-admin-page-wrap">

            <div class="epda-configuration-page-container">     <?php

				/**
				 * ADMIN HEADER
				 */
				EPDA_HTML_Admin::admin_header();

				/**
				 * ADMIN TOP PANEL
				 */
				EPDA_HTML_Admin::admin_toolbar( $admin_page_views );

				/**
				 * ADMIN SECONDARY TABS
				 */
				EPDA_HTML_Admin::admin_secondary_tabs( $admin_page_views );

				/**
				 * LIST OF SETTINGS IN TABS
				 */
				EPDA_HTML_Admin::admin_settings_tab_content( $admin_page_views, 'epda-config-wrapper' );    ?>

                <div class="epda-bottom-notice-message fadeOutDown"></div>
            </div>
        </div>	    <?php

		/**
		 * Show any notifications
		 */
		foreach ( $this->message as $class => $message ) {
			echo EPDA_HTML_Forms::notification_box_bottom( $message, '', $class );  // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}

	/**
	 * Get configuration array for regular views of Down Arrow Configuration page
     *
	 * @param $da_config
	 * @return array[]
	 */
	private function get_regular_views_config( $da_config ) {

		$regular_views = [];

		/**
		 * VIEW: SETTINGS
		 */
		$regular_views[] = array(

			// Shared
			'active' => true,

			'list_key' => 'settings',

			// Top Panel Item
			'label_text' => esc_html__( 'Settings', 'scroll-down-arrow' ),
			'icon_class' => 'epdafa epdafa-cogs',

			// Boxes List
			'list_top_actions_html_escaped' => self::settings_tab_actions_row(),
			'boxes_list' => array(
				array(
					'title' => esc_html__( 'Show Arrow On Pages', 'scroll-down-arrow' ),
					'html'  => $this->get_show_on_pages_form( $da_config ),
				),
				array(
					'title' => esc_html__( 'Arrow HTML Settings', 'scroll-down-arrow' ),
					'html'  => $this->get_arrow_html_form( $da_config ),
				),
			),
		);

		return $regular_views;
	}

	/**
	 * Show actions row for Settings tab
	 *
	 * @return string
	 */
	private static function settings_tab_actions_row() {

		ob_start();		?>

        <div class="epda-admin__list-actions-row"><?php
			EPDA_HTML_Elements::submit_button_v2( esc_html__( 'Save Settings', 'scroll-down-arrow' ), 'epda_save_da_settings', 'epda_save_da_settings_form', '', true, '', 'epda-success-btn');   ?>
        </div>      <?php

		return ob_get_clean();
	}

	/**
	 * Get Arrow Scripts settings form HTML
	 *
	 * @param $da_config
	 * @return false|string
	 */
	private function get_show_on_pages_form( $da_config ) {

		$cpt_tooltip_names_escaped = '';
		foreach ( $this->get_cpt_locations() as $location ) {
			$cpt_tooltip_names_escaped .= '<li>' . esc_html( $location->post_title ) . '</li>';
		}

		ob_start();

		// selected Locations and search inputs
		$this->display_locations_field( 'page', $da_config, esc_html__( 'Select pages to display the Widget', 'scroll-down-arrow' ), $cpt_tooltip_names_escaped );
		$this->display_locations_field( 'post', $da_config, esc_html__( 'Select posts to display the Widget', 'scroll-down-arrow'  ), $cpt_tooltip_names_escaped );

		// show CPTs of supported types if any
		$cpt_tooltip_desc = esc_html__( 'Select posts in Custom Post Types to display the Widget. List of supported Custom Post Types:', 'scroll-down-arrow' ) . '<br/>';
		$cpt_tooltip_desc .= '<ul>' . $cpt_tooltip_names_escaped . '</ul>';
		$cpt_tooltip_desc .= esc_html__( 'If we are missing a Custom Post Type, please contact us.', 'scroll-down-arrow' );

		$this->display_locations_field( 'cpt', $da_config, $cpt_tooltip_desc, $cpt_tooltip_names_escaped );

		return ob_get_clean();
	}

	/**
	 * Display list of selected Locations with search input
	 *
	 * @param $locations_type
	 * @param $da_config
	 * @param $tooltip
	 * @param $cpt_tooltip_names
	 * @param $kb_ad_button
	 */
	private function display_locations_field( $locations_type, $da_config, $tooltip , $cpt_tooltip_names ) {

		$locations_search_title = ''; //esc_html__( 'Search', 'scroll-down-arrow' );
		switch ( $locations_type ) {
			case 'page':
				$locations_search_title .= esc_html__( 'Add Pages', 'scroll-down-arrow' );
				$locations_search_placeholder = esc_html__( 'type to find page', 'scroll-down-arrow' );
				break;

			case 'post':
				$locations_search_title .= esc_html__( 'Add Posts', 'scroll-down-arrow' );
				$locations_search_placeholder = esc_html__( 'type to find post', 'scroll-down-arrow' );
				break;

			case 'cpt':
				$locations_search_title .= esc_html__( 'Add CPTs', 'scroll-down-arrow' );
				$locations_search_placeholder = esc_html__( 'type to find Custom Post Types', 'scroll-down-arrow' );
				break;

			default:
				$locations_search_placeholder = '';
				break;
		}   ?>

		<!-- Locations Field -->
		<div class="epda-wp__locations-list-option">
			<div class="epda-wp__locations-list-select epda-wp__locations-list-select--<?php echo esc_attr( $locations_type ); ?>">
				<div class="epda-wp__locations-list-search-title"><span>					<?php
					echo esc_html( $locations_search_title ); ?></span>  <?php
					EPDA_HTML_Elements::display_tooltip( $locations_search_title, $tooltip );   ?>
				</div>
				<div class="epda-wp__locations-list-search-body">
					<div class="epda-wp__locations-list-input-wrap">

						<!-- Search Input -->
						<input class="epda-wp__locations-list-input"
							   type="text"
							   value=""
							   data-post-type="<?php echo esc_attr( $locations_type ); ?>"
							   placeholder="<?php echo esc_attr( $locations_search_placeholder ); ?>">

						<!-- List of Locations -->
						<div class="epda-wp__locations-list-wrap">
							<input type="hidden" value="<?php echo esc_attr( $locations_type ); ?>" name="location_ids">
							<ul class="epda-wp__found-locations-list" style="display:none;"></ul>   <?php
							$this->get_locations_list( $locations_type, $da_config );     ?>
						</div>		
					</div>
				</div>
			</div>
		</div>  <?php
	}

	/**
	 * Return or display a certain type (page, post, cpt) of Locations for a given Widget
	 *
	 * @param $locations_type
	 * @param $da_config
	 */
	private function get_locations_list( $locations_type, $da_config ) {

		if ( ! empty( $da_config['location_pages_list'] ) && in_array( 0, $da_config['location_pages_list'] ) ) {
			$this->empty_home_page_selected = true;
		}

		// Limit of displayed locations
		$limit = 15;

		$include_locations = empty( $da_config['location_' . $locations_type . 's_list'] ) ? [] : $da_config['location_' . $locations_type . 's_list'];
		$locations = empty( $include_locations ) ? [] : $this->get_locations( $locations_type, 'post_title', '', [], $include_locations, false );

		ob_start(); ?>
		<ul class="epda-wp__selected-locations-list">   <?php
			$count = 0;
			foreach ( $locations as $location ) {
				$location_title = strlen( $location->post_title ) > 40 ? substr( $location->post_title, 0, 40 ) . '...' : $location->post_title;    ?>
				<li class="epda-wp__location epda-wp__location--selected <?php echo ( ++$count > $limit ) ? 'epda-wp__location--hidden' : ''; ?>" data-id="<?php echo esc_attr( $location->ID ); ?>">
					<span><?php echo esc_html( $location_title ); ?></span>
				</li>   <?php
			}   ?>
		</ul>   <?php
		$selected_locations_html_escaped = ob_get_clean();

		echo $selected_locations_html_escaped;  // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

		<div class="epda-wp__selected-locations-popup">
			<a class="epda-wp__popup-show-btn <?php echo ( $count <= $limit ) ? esc_attr( 'epda-wp__popup-show-btn--hidden' ) : ''; ?>">
				<?php esc_html_e( 'View All', 'scroll-down-arrow' ); ?>
			</a>    <?php
			EPDA_HTML_Admin::widget_details_popup(
				esc_html__( 'Selected Locations', 'scroll-down-arrow' ) . ' (' . ucfirst( $locations_type ) . 's)',
				$selected_locations_html_escaped
			);   ?>
		</div>  <?php
	}

	/**
	 * Return list of a certain type of Locations (page, post, or cpt)
	 *
	 * @param $locations_type
	 * @param $order_by
	 * @param $search_value
	 * @param $excluded_locations
	 * @param $included_locations
	 * @param bool $include_all_if_empty
	 * @return array
	 */
	private function get_locations( $locations_type, $order_by, $search_value, $excluded_locations, $included_locations, $include_all_if_empty=true ) {

		// for CPT we just return list of CPT names but do not need post ids or titles
		if ( $locations_type == 'cpt' ) {
			return $this->get_cpt_locations( $included_locations, $excluded_locations, $include_all_if_empty );
		}

		if ( ! in_array( $order_by, self::ORDER_LOCATIONS_BY ) ) {
			$order_by = self::ORDER_LOCATIONS_BY[0];
		}

		// if home page is not an actual page then include it as the first list entry by default only for:
		// - the first page if search_value is empty
		// - or if default Home Page title contains the search_value
		$home_page_title = esc_html__( 'Home Page', 'scroll-down-arrow' );
		$page_on_front = get_option( 'page_on_front' );
		$static_home_page_title = empty( $page_on_front ) ? '' : ' (' . get_the_title( $page_on_front ) . ')';
		$home_page_available = ( $this->empty_home_page_selected && in_array( EPDA_Config_Specs::HOME_PAGE, $included_locations ) ) || ( ! $this->empty_home_page_selected && empty( $included_locations ) );
		$use_empty_front_page = $locations_type == 'page' && ! in_array( EPDA_Config_Specs::HOME_PAGE, $excluded_locations ) && $home_page_available;
		$is_home_page_in_search = ! empty( $search_value ) && stripos( $home_page_title, $search_value ) !== false;

		$home_page = null;
		if ( $use_empty_front_page && ( $is_home_page_in_search || empty( $search_value ) ) ) {
			$home_page = new stdClass();
			$home_page->ID = EPDA_Config_Specs::HOME_PAGE;
			$home_page->post_title = $home_page_title . $static_home_page_title;
			$home_page->post_type = 'page';
		}

		global $wpdb;

		$params = array();

		// to retrieve list of Location objects
		$query_sql = "SELECT ID, post_title, post_type";

		// start assembling the SQL query
		$query_sql .= " FROM $wpdb->posts WHERE post_status IN ('publish', 'private', 'draft')";

		// excluded Location ids
		if ( ! empty( $excluded_locations ) ) {
			$params[] = implode("', '", $excluded_locations );
			$query_sql .= " AND ID NOT IN(%s)";
		}

		// included Location ids
		if ( ! empty( $included_locations ) ) {
			$params[] = implode("', '", $included_locations );
			$query_sql .= " AND ID IN(%s)";
		}

		// specify post types of Locations
		$params[] = $locations_type;
		$query_sql .= " AND post_type = %s AND post_mime_type = ''";

		// optionally use search string
		if ( ! empty( $search_value ) ) {
			$params[] = '% ' . $wpdb->esc_like( $search_value ) . ' %';
			$query_sql .= " AND post_title LIKE %s";
		}

		$params[] = $order_by;
		$query_sql .= " ORDER BY %s ASC";

		// query Locations
		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$locations = $wpdb->get_results( $wpdb->prepare( $query_sql, $params ) );
		if ( ! is_array( $locations ) ) {
			$locations = array();
		}

		// add default Home Page to found Locations (ignore ordering for the default Home Page for now to simplify the logic)
		if ( ! empty( $home_page ) ) {
			array_unshift( $locations, $home_page );
		}

		return $locations;
	}

	/**
	 * Get Arrow HTML settings form HTML
	 *
	 * @param $da_config
	 * @return false|string
	 */
	private function get_arrow_html_form( $da_config ) {

		ob_start(); ?>

        <div class="epda-wp__options-container epda-wp__options-container epda-wp__options-container--da-settings">   <?php

	        EPDA_HTML_Elements::radio_buttons_icon_selection( [
                'value'        => $da_config['arrow_type'],
                'specs'        => 'arrow_type',
                'tooltip_body' => esc_html__( 'Style of the Arrow', 'scroll-down-arrow' ),
            ] );
	        EPDA_HTML_Elements::dropdown( [
		        'value'        => $da_config['animation_type'],
		        'specs'        => 'animation_type',
		        'tooltip_body' => esc_html__( 'The style of animation that the arrow will perform, like bouncing, pulsing, and so on.', 'scroll-down-arrow' ),
	        ] );
	        EPDA_HTML_Elements::text( [
		        'value'        => $da_config['size'],
		        'specs'        => 'size',
		        'input_size'   => 'small',
		        'tooltip_body' => esc_html__( 'Size of the arrow in pixels.', 'scroll-down-arrow' ),
	        ] );
	        EPDA_HTML_Elements::color( [
		        'value'        => $da_config['color'],
		        'specs'        => 'color',
		        'input_size'   => 'small',
		        'tooltip_body' => esc_html__( 'The color code of the arrow', 'scroll-down-arrow' ),
	        ] );
	        EPDA_HTML_Elements::radio_buttons_horizontal( [
		        'value'             => $da_config['enable_duration'],
		        'specs'             => 'enable_duration',
		        'input_group_class' => 'epda-radio-horizontal-button-group-container',
		        'tooltip_body'      => esc_html__( 'If enabled, the Arrow will stay visible permanently instead of disappearing after duration set in Duration Time field.', 'scroll-down-arrow' ),
	        ] );
	        EPDA_HTML_Elements::text( [
		        'value'        => $da_config['duration_time'],
		        'specs'        => 'duration_time',
		        'input_size'   => 'small',
		        'tooltip_body' => esc_html__( 'Duration of the arrow (in milliseconds) before it disappears.', 'scroll-down-arrow' ),
	        ] );
	        EPDA_HTML_Elements::radio_buttons_horizontal( [
		        'value'             => $da_config['enable_bouncing'],
		        'specs'             => 'enable_bouncing',
		        'input_group_class' => 'epda-radio-horizontal-button-group-container',
		        'tooltip_body'      => esc_html__( 'Enable the arrow bouncing up and down.', 'scroll-down-arrow' ),
	        ] );
	        EPDA_HTML_Elements::radio_buttons_horizontal( [
		        'value'        => $da_config['bouncing_speed'],
		        'specs'        => 'bouncing_speed',
		        'input_group_class' => 'epda-radio-horizontal-button-group-container',
		        'tooltip_body' => esc_html__( 'Rate at which the arrow bounces.', 'scroll-down-arrow' ),
	        ] );
	        EPDA_HTML_Elements::radio_buttons_horizontal( [
		        'value'             => $da_config['disappear_after_scroll'],
		        'specs'             => 'disappear_after_scroll',
		        'input_group_class' => 'epda-radio-horizontal-button-group-container',
		        'tooltip_body'      => esc_html__( 'If enabled, the Arrow will disappear after user scrolls down.', 'scroll-down-arrow' ),
	        ] );
	        EPDA_HTML_Elements::text( [
		        'value'        => $da_config['move_to_id'],
		        'specs'        => 'move_to_id',
		        'input_size'   => 'medium',
		        'tooltip_body' => esc_html__( 'Enter CSS ID in order to allow users to click on the Arrow and be moved to a specific location on the page.', 'scroll-down-arrow' ),
	        ] );
	        EPDA_HTML_Elements::text( [
		        'value'        => $da_config['arrow_css_id'],
		        'specs'        => 'arrow_css_id',
		        'input_size'   => 'medium',
		        'tooltip_body' => esc_html__( 'For developers, this is CSS ID of your choice, so you can target it with CSS or Javascript.', 'scroll-down-arrow' ),
	        ] );
	        EPDA_HTML_Elements::text( [
		        'value'        => $da_config['arrow_css_class'],
		        'specs'        => 'arrow_css_class',
		        'input_size'   => 'medium',
		        'tooltip_body' => esc_html__( 'For developers, the CSS Class of your choice so you can target it with CSS or Javascript.', 'scroll-down-arrow' ),
	        ] );        ?>
        </div>  <?php

		return ob_get_clean();
	}

	/**
	 * Return list of cpt Locations
	 *
	 * @param array $included_locations
	 * @param array $excluded_locations
	 * @param bool $include_all_if_empty
	 * @return array
	 */
	private function get_cpt_locations( $included_locations=[], $excluded_locations=[], $include_all_if_empty=true ) {

		$white_cpt_list = array( EPDA_KB_Core_Utilities::KB_POST_TYPE_PREFIX,
								 'ip_lesson', 'ip_quiz', 'ip_question', 'ip_course', 'sfwd-lessons', 'sfwd-quiz', 'sfwd-topic', 'forum', 'topic', 'product', 'download' );

		$locations = array();
		$custom_post_types = EPDA_Utilities::get_post_type_labels( [], [] );
		foreach ( $custom_post_types as $cpt => $cpt_title ) {

			if ( in_array( $cpt, $excluded_locations ) ) {
				continue;
			}

			// if included locations is empty then include all CPTs
			if ( ! in_array( $cpt, $included_locations ) && ! $include_all_if_empty ) {
				continue;
			}

			$location = new stdClass();
			$location->ID = $cpt;
			$location->post_title = $cpt_title;
			$location->post_type = $cpt;
			$location->url = '';
			$locations[] = $location;
		}

		return $locations;
	}

	/**
	 * Return or display a certain type (page, post, cpt) of Locations that are not assigned in any Widgets
	 *
	 * @param $locations_type
	 * @param string $search_value
	 * @param array $excluded_ids
	 * @return string
	 */
	public function get_available_locations_list( $locations_type, $search_value='', $excluded_ids=[] ) {

		$locations = $this->get_locations( $locations_type, 'post_title', $search_value, $excluded_ids, [] );

		ob_start();

		foreach ( $locations as $location ) {
			$location_title = strlen( $location->post_title ) > 25 ? substr( $location->post_title, 0, 25 ) . '...' : $location->post_title; ?>
			<li class="epda-wp__location epda-wp__location--selected" data-id="<?php echo esc_attr( $location->ID ); ?>">
				<span><?php echo esc_html( $location_title ); ?></span>
			</li>   <?php
		}

		return ob_get_clean();
	}
}
