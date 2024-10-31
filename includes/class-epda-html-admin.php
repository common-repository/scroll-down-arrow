<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * HTML Elements for admin pages excluding boxes
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPDA_HTML_Admin {

	/********************************************************************************
	 *
	 *                             ADMIN HEADER
	 *
	 ********************************************************************************/

	/**
	 * Show Admin Header
	 *
	 * @param string $content_type
	 */
	public static function admin_header( $content_type='header' ) {  ?>

		<!-- Admin Header -->
		<div class="epda-admin__header">
			<div class="epda-admin__section-wrap epda-admin__section-wrap__header">   <?php

				switch ( $content_type ) {
					case 'header':
					default:
						echo self::admin_header_content(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						break;
					case 'logo':
						echo self::get_admin_header_logo(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						break;
				}  ?>

			</div>
		</div>  <?php
	}

	/**
	 * Show content of Admin Header
	 *
	 * @return string
	 */
	private static function admin_header_content() {

		ob_start();

		echo self::get_admin_header_logo(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		$result = ob_get_clean();

		return empty( $result ) ? '' : $result;
	}

	/**
	 * Fill missing fields in single admin page view configuration array with default values
	 *
	 * @param $page_view
	 * @return array
	 */
	private static function admin_page_view_fill_missing_with_default( $page_view ){

		// Do not fill empty or not valid array
		if ( empty( $page_view ) || ! is_array( $page_view ) ) {
			return $page_view;
		}

		// Default page view
		$default_page_view = array(

			// Shared
			'minimum_required_capability' => EPDA_Utilities::ADMIN_CAPABILITY,
			'active'                    => false,
			'list_id'                   => '',
			'list_key'                  => '',

			// Top Panel Item
			'label_text'                => '',
			'main_class'                => '',
			'label_class'               => '',
			'icon_class'                => '',

			// Secondary Panel Items
			'secondary'                 => array(),

			// Boxes List
			'list_top_actions_html_escaped'     => '',
			'top_actions_minimum_required_capability' => EPDA_Utilities::ADMIN_CAPABILITY,
			'list_bottom_actions_html_escaped'  => '',
			'bottom_actions_minimum_required_capability' => EPDA_Utilities::ADMIN_CAPABILITY,
			'boxes_list'                => array(),

			// List footer HTML
			'list_footer_html'          => '',
		);

		// Default secondary view
		$default_secondary = array(

			// Shared
			'list_key'                  => '',
			'active'                    => false,
			'minimum_required_capability' => EPDA_Utilities::ADMIN_CAPABILITY,

			// Secondary Panel Item
			'label_text'                => '',
			'main_class'                => '',
			'label_class'               => '',
			'icon_class'                => '',

			// Secondary Boxes List
			'list_top_actions_html_escaped'     => '',
			'top_actions_minimum_required_capability' => EPDA_Utilities::ADMIN_CAPABILITY,
			'list_bottom_actions_html_escaped'  => '',
			'bottom_actions_minimum_required_capability' => EPDA_Utilities::ADMIN_CAPABILITY,
			'boxes_list'                => array(),
		);

		// Default box
		$default_box = array(
			'minimum_required_capability' => EPDA_Utilities::ADMIN_CAPABILITY,
			'icon_class'    => '',
			'class'         => '',
			'title'         => '',
			'description'   => '',
			'html'          => '',
			'return_html'   => false,
			'extra_tags'    => [],
		);

		// Set default view
		$page_view = array_merge( $default_page_view, $page_view );

		// Set default boxes
		foreach ( $page_view['boxes_list'] as $box_index => $box_content ) {

			// Do not fill empty or not valid array
			if ( empty( $page_view['boxes_list'][$box_index] ) || ! is_array( $page_view['boxes_list'][$box_index] ) ) {
				continue;
			}

			$page_view['boxes_list'][$box_index] = array_merge( $default_box, $box_content );
		}

		// Set default secondary views
		foreach ( $page_view['secondary'] as $secondary_index => $secondary_content ) {

			// Do not fill empty or not valid array
			if ( empty( $page_view['secondary'][$secondary_index] ) || ! is_array( $page_view['secondary'][$secondary_index] ) ) {
				continue;
			}

			$page_view['secondary'][$secondary_index] = array_merge( $default_secondary, $secondary_content );

			// Set default boxes
			foreach ( $page_view['secondary'][$secondary_index]['boxes_list'] as $box_index => $box_content ) {

				// Do not fill empty or not valid array
				if ( empty(  $page_view['secondary'][$secondary_index]['boxes_list'][$box_index] ) || ! is_array(  $page_view['secondary'][$secondary_index]['boxes_list'][$box_index] ) ) {
					continue;
				}

				$page_view['secondary'][$secondary_index]['boxes_list'][$box_index] = array_merge( $default_box, $box_content );
			}
		}

		return $page_view;
	}

	/**
	 * Show Admin Toolbar
	 *
	 * @param $admin_page_views
	 */
	public static function admin_toolbar( $admin_page_views ) {     ?>

		<!-- Admin Top Panel -->
		<div class="epda-admin__top-panel">
			<div class="epda-admin__section-wrap epda-admin__section-wrap__top-panel">      <?php

				foreach( $admin_page_views as $page_view ) {

					// Optionally we can have null in $page_view, make sure we handle it correctly
					if ( empty( $page_view ) || ! is_array( $page_view ) ) {
						continue;
					}

					// Fill missing fields in admin page view configuration array with default values
					$page_view = self::admin_page_view_fill_missing_with_default( $page_view );

					// Do not render toolbar tab if the user does not have permission
					if ( ! current_user_can( $page_view['minimum_required_capability'] ) ) {
						continue;
					}   ?>

					<div class="epda-admin__top-panel__item epda-admin__top-panel__item--<?php echo esc_attr( $page_view['list_key'] );
						echo empty( $page_view['secondary'] ) ? '' : ' epda-admin__top-panel__item--parent ';
						echo esc_attr( $page_view['main_class'] ); ?>"
					    <?php echo empty( $page_view['list_id'] ) ? '' : ' id="' . esc_attr( $page_view['list_id'] ) . '"'; ?> data-target="<?php echo esc_attr( $page_view['list_key'] ); ?>">
						<div class="epda-admin__top-panel__icon epda-admin__top-panel__icon--<?php echo esc_attr( $page_view['list_key'] ); ?> <?php echo esc_attr( $page_view['icon_class'] ); ?>"></div>
						<p class="epda-admin__top-panel__label epda-admin__boxes-list__label--<?php echo esc_attr( $page_view['list_key'] ); ?>"><?php echo wp_kses_post( $page_view['label_text'] ); ?></p>
					</div> <?php
				}       ?>

			</div>
		</div>  <?php
	}

	/**
	 * Display admin second-level tabs below toolbar
	 *
	 * @param $admin_page_views
	 */
	public static function admin_secondary_tabs( $admin_page_views ) {  ?>

		<!-- Admin Secondary Panels List -->
		<div class="epda-admin__secondary-panels-list">
			<div class="epda-admin__section-wrap epda-admin__section-wrap__secondary-panel">  <?php

				foreach ( $admin_page_views as $page_view ) {

					// Optionally we can have null in $page_view, make sure we handle it correctly
					if ( empty( $page_view ) || ! is_array( $page_view ) ) {
						continue;
					}

					// Optionally we can have empty in $page_view['secondary'], make sure we handle it correctly
					if ( empty( $page_view['secondary'] ) || ! is_array( $page_view['secondary'] ) ) {
						continue;
					}

					// Fill missing fields in admin page view configuration array with default values
					$page_view = self::admin_page_view_fill_missing_with_default( $page_view );

					// Do not render toolbar tab if the user does not have permission
					if ( ! current_user_can( $page_view['minimum_required_capability'] ) ) {
						continue;
					}   ?>

					<!-- Admin Secondary Panel -->
					<div id="epda-admin__secondary-panel__<?php echo esc_attr( $page_view['list_key'] ); ?>" class="epda-admin__secondary-panel">  <?php

						foreach ( $page_view['secondary'] as $secondary ) {

							// Optionally we can have empty in $secondary, make sure we handle it correctly
							if ( empty( $secondary ) || ! is_array( $secondary ) ) {
								continue;
							}

							// Do not render secondary toolbar tab if the user does not have permission
							if ( ! current_user_can( $secondary['minimum_required_capability'] ) ) {
								continue;
							}       ?>

							<div class="epda-admin__secondary-panel__item epda-admin__secondary-panel__<?php echo esc_attr( $secondary['list_key'] ); ?> <?php
								echo ( $secondary['active'] ? 'epda-admin__secondary-panel__item--active' : '' );
								echo esc_attr( $secondary['main_class'] ); ?>" data-target="<?php echo esc_attr( $page_view['list_key'] ) . '__' .esc_attr( $secondary['list_key'] ); ?>">     <?php

								// Optional icon for secondary panel item
								if ( ! empty( $secondary['icon_class'] ) ) {        ?>
									<span class="epda-admin__secondary-panel__icon <?php echo esc_attr( $secondary['icon_class'] ); ?>"></span>     <?php
								}       ?>

								<p class="epda-admin__secondary-panel__label epda-admin__secondary-panel__<?php echo esc_attr( $secondary['list_key'] ); ?>__label"><?php echo wp_kses_post( $secondary['label_text'] ); ?></p>
							</div>  <?php

						}   ?>
					</div>  <?php

				}   ?>

			</div>
		</div>  <?php
	}

	/**
	 * Show list of settings for each setting in a tab
	 *
	 * @param $admin_page_views
	 * @param string $content_class
	 */
	public static function admin_settings_tab_content( $admin_page_views, $content_class='' ) {    ?>

		<!-- Admin Content -->
		<div class="epda-admin__content <?php echo esc_attr( $content_class ); ?>"> <?php

			echo '<div class="epda-admin__boxes-list-container">';
			foreach ( $admin_page_views as $page_view ) {

				// Optionally we can have null in $page_view, make sure we handle it correctly
				if ( empty( $page_view ) || ! is_array( $page_view ) ) {
					continue;
				}

				// Fill missing fields in admin page view configuration array with default values
				$page_view = self::admin_page_view_fill_missing_with_default( $page_view );

				// Do not render view if the user does not have permission
				if ( ! current_user_can( $page_view['minimum_required_capability'] ) ) {
					continue;
				}   ?>

				<!-- Admin Boxes List -->
				<div id="epda-admin__boxes-list__<?php echo esc_attr( $page_view['list_key'] ); ?>" class="epda-admin__boxes-list">     <?php

					// List body
					self::admin_setting_boxes_for_tab( $page_view );

					// Optional list footer
					if ( ! empty( $page_view['list_footer_html'] ) ) {   ?>
							<div class="epda-admin__section-wrap epda-admin__section-wrap__<?php echo esc_attr( $page_view['list_key'] ); ?>">
								<div class="epda-admin__boxes-list__footer"><?php echo wp_kses_post( $page_view['list_footer_html'] ); ?></div>
						</div>      <?php
					}   ?>

				</div><?php
			}
			echo '</div>'; ?>
		</div><?php
	}

	/**
	 * Show single List of Settings Boxes for Admin Page
	 *
	 * @param $page_view
	 */
	private static function admin_setting_boxes_for_tab( $page_view ) {

		// Boxes List for view without secondary panel
		if ( empty( $page_view['secondary'] ) ) {

			// Make sure we can handle empty boxes list correctly
			if ( empty( $page_view['boxes_list'] ) || ! is_array( $page_view['boxes_list'] ) ) {
				return;
			}   ?>

			<!-- Admin Section Wrap -->
			<div class="epda-admin__section-wrap epda-admin__section-wrap__<?php echo esc_attr( $page_view['list_key'] ); ?>">  <?php

				self::admin_settings_display_boxes_list( $page_view );   ?>

			</div>      <?php

		// Boxes List for view with secondary tabs
		} else {

			// Secondary Lists of Boxes
			foreach ( $page_view['secondary'] as $secondary ) {

				// Make sure we can handle empty boxes list correctly
				if ( empty( $secondary['boxes_list'] ) || ! is_array( $secondary['boxes_list'] ) ) {
					continue;
				}   ?>

				<!-- Admin Section Wrap -->
				<div class="epda-setting-box-container epda-setting-box-container-type-<?php echo esc_attr( $page_view['list_key'] ); ?>">

					<!-- Secondary Boxes List -->
					<div id="epda-setting-box__list-<?php echo esc_attr( $page_view['list_key'] ) . '__' . esc_attr( $secondary['list_key'] ); ?>"
					     class="epda-setting-box__list <?php echo ( $secondary['active'] ? 'epda-setting-box__list--active' : '' ); ?>">   <?php

						self::admin_settings_display_boxes_list( $secondary );   ?>

					</div>

				</div>  <?php
			}
		}
	}

	/**
	 * Display boxes list for admin settings
	 *
	 * @param $page_view
	 */
	private static function admin_settings_display_boxes_list( $page_view ) {

		// Optional buttons row displayed at the top of the boxes list
		if ( ! empty( $page_view['list_top_actions_html_escaped'] ) && current_user_can( $page_view['top_actions_minimum_required_capability'] ) ) {
			echo $page_view['list_top_actions_html_escaped']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		// Admin Boxes with configuration
		foreach ( $page_view['boxes_list'] as $box_options ) {

			// Do not render empty or not valid array
			if ( empty( $box_options ) || ! is_array( $box_options ) ) {
				continue;
			}

			// Do not render box if the user does not have permission
			if ( ! current_user_can( $box_options['minimum_required_capability'] ) ) {
				continue;
			}

			EPDA_HTML_Forms::admin_settings_box( $box_options );
		}

		// Optional buttons row displayed at the bottom of the boxes list
		if ( ! empty( $page_view['list_bottom_actions_html_escaped'] ) && current_user_can( $page_view['top_actions_minimum_required_capability'] )) {
			echo $page_view['list_bottom_actions_html_escaped']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}

	/**
	 * Get logo container for the admin header
	 *
	 * @return string
	 */
	private static function get_admin_header_logo() {

		ob_start();     ?>

		<!-- Down Arrow Logo -->
		<div class="epda-admin__header__logo-wrap">
			<div class="epda__desc__logo">
				<span class="epda__desc__logo__icon">
					<span class="ep-help-dialog-icon">
						<span class="ep_font_icon_help_dialog"></span>
						<span class="ep_font_icon_help_dialog-background"></span>
					</span>
				</span>
			</div>
			<div class="epda__desc__name"><?php esc_html_e( 'Down Arrow', 'scroll-down-arrow' ); ?></div>
		</div>  <?php

		$result = ob_get_clean();

		return empty( $result ) ? '' : $result;
	}


	/********************************************************************************
	 *
	 *                                   VARIOUS
	 *
	 ********************************************************************************/

	/**
	 * Widget Details Popup - user can only click 'OK' button
	 *
	 * @param string $title
	 * @param string $body
	 * @param string $accept_label
	 */
	public static function widget_details_popup( $title='', $body='', $accept_label='' ) {

		$body_escaped = EPDA_Utilities::admin_ui_wp_kses( $body );   ?>

        <div class="epda-admin__widget-details-popup">

            <!---- Header ---->
            <div class="epda-admin__widget-details-popup__header">
                <h4><?php echo esc_html( $title ); ?></h4>
            </div>

            <!---- Body ---->
            <div class="epda-admin__widget-details-popup__body">
				<?php echo $body_escaped;  // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped?>
            </div>

            <!---- Footer ---->
            <div class="epda-admin__widget-details-popup__footer">
                <div class="epda-admin__widget-details-popup__footer__accept">
					<span class="epda-admin__widget-details-popup__accept-btn">
						<?php echo empty( $accept_label ) ? esc_html__( 'OK', 'scroll-down-arrow' ) : esc_html( $accept_label ); ?>
					</span>
                </div>
            </div>

        </div>

        <div class="epda-admin__widget-details-popup__overlay"></div>      <?php
	}

	/**
	 * Generic admin page to display message on configuration error
	 *
	 * @param string|WP_Error $wp_error
	 */
	public static function display_config_error_page( $wp_error = '' ) {

		if ( is_wp_error( $wp_error ) ) {
			$wp_error = $wp_error->get_error_message();
		}

		$title = $wp_error ? esc_html__( 'Cannot load configuration.', 'scroll-down-arrow' ) . ' (' . $wp_error . ')' : esc_html__( 'Cannot load configuration.', 'scroll-down-arrow' ); ?>
		<div id="epda-admin-page-wrap" class="epda-admin-page-wrap--config-error">  <?php
			EPDA_HTML_Forms::notification_box_middle( [ 'type' => 'error', 'title' => $title, 'desc' =>  EPDA_Utilities::contact_us_for_support() ] ); ?>
		</div>  <?php
	}
}
