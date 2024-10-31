<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * HTML boxes and dialogs for admin pages
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPDA_HTML_Forms {

	/********************************************************************************
	 *
	 *                                   NOTIFICATIONS
	 *
	 ********************************************************************************/

	/**
	 * This is the Middle Notification Box
	 * Must be placed within the Admin Content ( #ekb-admin-page-wrap ). Used inside boxes and within the Admin Content.
	 *
	 * @param array $args Array of Settings.
	 * @param bool $return_html Optional. Returns html if true, otherwise echo's out function html.
     *
     * Types - success, error, error-no-icon, warning, info
	 *
	 * @return string
	 */
	public static function notification_box_middle( array $args = array(), $return_html=false ) {

		$icon = '';
		switch ( $args['type']) {
			case 'error':   $icon = 'epdafa-exclamation-triangle';
				break;
				break;
			case 'success': $icon = 'epdafa-check-circle';
				break;
				break;
			case 'warning': $icon = 'epdafa-exclamation-circle';
				break;
			case 'info':    $icon = 'epdafa-info-circle';
				break;
			case 'error-no-icon':
			case 'success-no-icon':
			default:
				break;
		}

		if ( $return_html ) {
			ob_start();
		}        ?>

		<div <?php echo isset( $args['id'] ) ? 'id="' . esc_attr( $args['id'] ) . '"' : ''; ?> class="epda-notification-box-middle <?php echo 'epda-notification-box-middle--' . esc_attr( $args['type'] ); ?>">

			<div class="epda-notification-box-middle__icon">
				<div class="epda-notification-box-middle__icon__inner epdafa <?php echo esc_html( $icon ); ?>"></div>
			</div>

			<div class="epda-notification-box-middle__body">                <?php
				if ( ! empty( $args['title'] ) ) { ?>
					<h6 class="epda-notification-box-middle__body__title">						<?php
						echo wp_kses( $args['title'], array( 'a' => array(
							'href'  => array(),
							'title' => array()
						),
							'br'        => array(),
							'em'        => array(),
							'strong'    => array(),
							'ul'        => array(),
							'li'        => array(),
						) ); ?>
					</h6>                <?php
				}

				if ( isset( $args['desc'] ) ) { ?>
					<div class="epda-notification-box-middle__body__desc"><?php
						echo wp_kses( $args['desc'], array(
							'a' => array(
								'href'   => array(),
								'title'  => array(),
								'target' => array(),
								'class'  => array(),
							),
							'span'      => array(
								'class' => array(),
							),
							'br'        => array(),
							'em'        => array(),
							'strong'    => array(),
							'ul'        => array(),
							'li'        => array(),
						) ); ?>
					</div> <?php
				}

				if ( ! empty( $args['id'] ) && ! empty( $args['button_confirm'] ) ) {  ?>
					<div class="epda-notification-box-middle__buttons-wrap">
						<span class="epda-notification-box-middle__button-confirm epda-notice-dismiss"<?php echo empty( $args['close_target'] ) ? '' : ' data-target="' . esc_html( $args['close_target'] ) . '"'; ?>>
							<?php echo esc_html( $args['button_confirm'] ); ?></span>
					</div>     <?php
				}   ?>
			</div>

		</div>    <?php

		if ( $return_html ) {
			return ob_get_clean();
		}

		return '';
	}

	/**
	 * Show info or error message to the user
	 *
	 * @param $message
	 * @param string $title
	 * @param string $type
	 * @return string
	 */
	public static function notification_box_bottom( $message, $title='', $type='success' ) {

		$message = empty( $message ) ? '' : $message;

		return
			"<div class='epda-bottom-notice-message'>
				<div class='contents'>
					<span class='" . esc_attr( $type ) . "'>" .
			( empty( $title ) ? '' : '<h4>' . esc_html( $title ) . '</h4>' ) . "
						<p> " . wp_kses_post( $message ) . "</p>
					</span>
				</div>
				<div class='epda-close-notice epdafa epdafa-window-close'></div>
			</div>";
	}

	/**
	 * DIALOG BOX - User confirms action like delete records with OK or Cancel buttons.
	 *	$values ['id']                  CSS ID, used for JS targeting, no CSS styling.
	 *	$values ['title']               Top Title of Dialog Box.
	 *	$values ['body']                Text description.
	 *	$values ['form_inputs']         Form Inputs
	 *	$values ['accept_label']        Text for Accept button.
	 *	$values ['accept_type']         Text for Accept button. ( success, default, primary, error , warning )
	 *	$values ['show_cancel_btn']     ( yes, no )
	 *	$values ['show_close_btn']      ( yes, no )
	 *
	 * @param $values
	 */
	public static function dialog_confirm_action( $values ) { ?>

		<div id="<?php echo esc_attr( $values[ 'id' ] ); ?>" class="epda-dialog-box-form" style="<?php echo empty( $values['hidden'] ) ? '' : 'display: none;'; ?>">

			<!---- Header ---->
			<div class="epda-dbf__header">
				<h4><?php echo esc_html( $values['title'] ); ?></h4>
			</div>

			<!---- Body ---->
			<div class="epda-dbf__body">				<?php
				echo empty( $values['body']) ? '' : wp_kses( $values['body'], EPDA_Utilities::get_admin_ui_extended_html_tags() ); ?>
			</div>

			<!---- Form ---->			<?php
			if ( !empty( $values[ 'form_method' ] ) ) { 		?>
				<form class="epda-dbf__form"<?php echo empty( $values['form_method'] ) ? '' : ' method="' . esc_attr( $values['form_method'] ) . '"'; ?>>				<?php
					if ( isset($values['form_inputs']) ) {
						foreach ( $values['form_inputs'] as $input ) {
							echo '<div class="epda-dbf__form__input">' . wp_kses( $input, EPDA_Utilities::get_admin_ui_extended_html_tags() ) . '</div>';
						}
					} ?>
				</form>			<?php
			} 		?>

			<!---- Footer ---->
			<div class="epda-dbf__footer">

				<div class="epda-dbf__footer__accept <?php echo isset($values['accept_type']) ? 'epda-dbf__footer__accept--' . esc_attr( $values['accept_type'] ) : 'epda-dbf__footer__accept--success'; ?>">
					<span class="epda-accept-button epda-dbf__footer__accept__btn">
						<?php echo $values['accept_label'] ? esc_html( $values['accept_label'] ) : esc_html__( 'Accept', 'scroll-down-arrow' ); ?>
					</span>
				</div>				<?php
				if ( ! empty( $values['show_cancel_btn' ] ) && $values['show_cancel_btn'] === 'yes' ) { 		?>
					<div class="epda-dbf__footer__cancel">
						<span class="epda-dbf__footer__cancel__btn"><?php esc_html_e( 'Cancel', 'scroll-down-arrow' ); ?></span>
					</div>				<?php
				} 		?>
			</div>  		           <?php

			if ( ! empty( $values['show_close_btn'] ) && $values['show_close_btn'] === 'yes' ) { 		?>
				<div class="epda-dbf__close epdafa epdafa-times"></div>             <?php
			} 		?>

		</div>
		<div class="epda-dialog-box-form-black-background"></div>		<?php
	}

	/**
	 * Show a single Settings Box for one configuration for configuration pages
	 *
	 * @param $box_options
	 *
	 * @return false|string
	 */
	public static function admin_settings_box( $box_options ) {

		// Skip box if its content HTML is empty (due to user access level or add-ons disabled)
		if ( empty( $box_options['html'] ) ) {
			return '';
		}

		if ( $box_options['return_html'] ) {
			ob_start();
		}   ?>

		<!-- Admin Box -->
		<div class="epda-admin__boxes-list__box <?php echo esc_attr( $box_options['class'] ); ?>">  <?php

			// Display header
			if ( ! empty( $box_options['title'] ) ){    ?>
				<h4 class="epda-admin__boxes-list__box__header<?php
					echo empty( $box_options['icon_class'] ) ? '' : ' epda-kbc__boxes-list__box__header--icon ' . esc_attr( $box_options['icon_class'] );
				?>">
                    <span><?php echo esc_html( $box_options['title'] ); ?></span>   <?php
	                // Add box header tooltip
					if ( ! empty( $box_options['tooltip_title'] ) && ! empty( $box_options['tooltip_desc'] ) ) {
						$box_options['tooltip_args'] = isset( $box_options['tooltip_args'] ) ? $box_options['tooltip_args'] : [];
						EPDA_HTML_Elements::display_tooltip( $box_options['tooltip_title'], $box_options['tooltip_desc'], $box_options['tooltip_args'] );
					} ?>
                </h4>   <?php
			}

			// Display body         ?>
			<div class="epda-admin__boxes-list__box__body">   <?php

				// Display description
				if ( ! empty( $box_options['description'] ) ) {    ?>
					<p class="epda-admin__boxes-list__box__desc"><?php echo wp_kses_post( $box_options['description'] ); ?></p>   <?php
				}

				// Display HTML Content
				$box_options['extra_tags'] = isset( $box_options['extra_tags'] ) ? $box_options['extra_tags'] : array();   ?>
				<div class="epda-admin__boxes-list__box__content"><?php echo EPDA_Utilities::admin_ui_wp_kses( $box_options['html'], $box_options['extra_tags'] );  /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */ ?></div>

			</div>

		</div> <?php

		if ( $box_options['return_html'] ) {
			return ob_get_clean();
		}

		return '';
	}
}