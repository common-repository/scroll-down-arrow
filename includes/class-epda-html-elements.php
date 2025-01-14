<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Elements of form UI and others
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPDA_HTML_Elements {

	// Form Elements------------------------------------------------------------------------------------------/

	/**
	 * Add Default Fields
	 *
	 * @param array $input_array
	 * @param array $custom_defaults
	 *
	 * @return array
	 */
	public static function add_defaults( array $input_array, array $custom_defaults=array() ) {

		$defaults = array(
			'id'                => '',
			'name'              => 'text',
			'value'             => '',
			'label'             => '',
			'title'             => '',
			'class'             => '',
			'main_label_class'  => '',
			'label_class'       => '',
			'input_class'       => '',
			'input_group_class' => '',
			'radio_class'       => '',
			'action_class'      => '',
			'container_class'   => '',
			'desc'              => '',
			'info'              => '',
			'placeholder'       => '',
			'readonly'          => false,  // will not be submitted
			'required'          => '',
			'autocomplete'      => false,
			'data'              => false,
			'disabled'          => false,
			'max'               => 50,
			'options'           => array(),
			'label_wrapper'     => '',
			'input_wrapper'     => '',
			'icon_color'        => '',
			'return_html'       => false,
			'unique'            => true,
			'text_class'        => '',
			'icon'              => '',
			'list'              => array(),
			'btn_text'          => '',
			'btn_url'           => '',
			'more_info_text'    => '',
			'more_info_url'     => '',
			'tooltip_title'     => '',
			'tooltip_body'      => '',
			'tooltip_args'      => array(),
			'tooltip_external_links'      => array(),
			'is_pro'            => '',
			'is_pro_feature_ad' => '',
			'pro_tooltip_args'  => array(),
            'input_size'        => 'medium',
			'group_data'        => false
		);
		$defaults = array_merge( $defaults, $custom_defaults );
		return array_merge( $defaults, $input_array );
	}

	/**
	 * Renders an HTML Text field
	 *
	 * @param array $args Arguments for the text field
	 * @param bool $return_html
	 * @return false|string
	 */
	public static function text( $args = array(), $return_html=false ) {

		if ( $return_html ) {
			ob_start();
		}

		$args = self::add_defaults( $args );
		$args = self::get_specs_info( $args );

		$readonly = $args['readonly'] ? ' readonly' : '';
		$required = empty( $args['required'] ) ? '' : ' required';

		$group_data_escaped = self::get_data_escaped( $args['group_data'] );
		$data_escaped = self::get_data_escaped( $args['data'] );  ?>

		<div class="epda-input-group epda-admin__text-field <?php echo esc_html( $args['input_group_class'] ); ?>" id="<?php echo esc_attr( $args['name'] ); ?>_group" <?php echo $group_data_escaped;  /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */ ?>>

			<label class="<?php echo esc_attr( $args['label_class'] ); ?>" for="<?php echo esc_attr( $args['name'] ); ?>">  <?php
			    echo wp_kses_post( $args['label'] );

				self::display_tooltip( $args['tooltip_title'], $args['tooltip_body'], $args['tooltip_args'], $args['tooltip_external_links'] );

				if ( $args['is_pro'] ) {
					self::display_pro_setting_tag( $args['pro_tooltip_args'] );
				}
				if ( ! empty( $args['desc'] ) ) {
					echo wp_kses_post( $args['desc'] );
				}   ?>
			</label>

			<div class="input_container <?php echo esc_attr( $args['input_class'] ); ?>">
			    <input type="text"
			           class="epda-input--<?php echo esc_attr( $args['input_size'] ); ?>"
			           name="<?php echo esc_attr( $args['name'] ); ?>"
			           id="<?php echo  esc_attr( $args['name'] ); ?>"
			           autocomplete="<?php echo ( $args[ 'autocomplete' ] ? 'on' : 'off' ); ?>"
			           value="<?php echo esc_attr( $args['value'] ); ?>"
			           placeholder="<?php echo esc_attr( $args['placeholder'] ); ?>"						<?php
			           echo $data_escaped . esc_attr( $readonly . $required );						?>
			           maxlength="<?php echo esc_attr( $args['max'] ); ?>"
			    >
			</div>

		</div>		<?php

		if ( $return_html ) {
			return ob_get_clean();
		}

		return '';
	}

	/**
	 * Renders an HTML drop-down box
	 *
	 * @param array $args
	 */
	public static function dropdown( $args = array() ) {

		$args = self::add_defaults( $args );
		$args = self::get_specs_info( $args );

		$group_data_escaped = self::get_data_escaped( $args['group_data'] );    ?>

		<div class="epda-input-group <?php echo esc_attr( $args['input_group_class'] ); ?>" id="<?php echo esc_attr( $args['name'] ); ?>_group" <?php echo $group_data_escaped; /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */ ?>>
			<label class="<?php echo esc_attr( $args['label_class'] ); ?>" for="<?php echo esc_attr( $args['name'] ); ?>">  <?php
				echo wp_kses_post( $args['label'] );

				self::display_tooltip( $args['tooltip_title'], $args['tooltip_body'], $args['tooltip_args'], $args['tooltip_external_links'] );

				if ( $args['is_pro'] ) {
					self::display_pro_setting_tag( $args['pro_tooltip_args'] );
				} ?>
			</label>

			<div class="input_container <?php echo esc_attr( $args['input_class'] ); ?>">

				<select name="<?php echo esc_attr( $args['name'] ); ?>" id="<?php echo esc_attr( $args['name'] ); ?>">     <?php
					foreach( $args['options'] as $key => $value ) {
						$label = is_array( $value ) ? $value['label'] : $value;
                        $class = isset( $value['class'] ) ? $value['class'] : '';
						echo '<option value="' . esc_attr( $key ) . '" class="' . esc_attr( $class ) . '"' . selected( $key, $args['value'], false ) . '>' . esc_html( $label ) . '</option>';
					}  ?>
				</select>
			</div>

		</div>		<?php
	}

	/**
	 * Renders several HTML radio buttons in a Row
	 * Type of Radio buttons: use the input_group_class
	 *          epda-radio-vertical-group-container             Regular Radio Horizontal Group
	 *          epda-radio-vertical-button-group-container      Button Style Radio Horizontal Group
	 *
	 * @param array $args
	 * @return false|string
	 */
	public static function radio_buttons_horizontal( $args = array() ) {

		$defaults = array(
			'id'                => 'radio',
			'name'              => 'radio-buttons',
            'desc_condition'    => '',
		);
		$args = self::add_defaults( $args, $defaults );
		$args = self::get_specs_info( $args );

		$ix = 0;

		$group_data_escaped = self::get_data_escaped( $args['group_data'] );

		if ( $args['return_html'] ) {
			ob_start();
		}   ?>

		<div class="epda-input-group epda-radio-horizontal-button-group-container <?php echo esc_attr( $args['input_group_class'] ); ?>" id="<?php echo esc_attr( $args['name'] ); ?>_group" <?php echo $group_data_escaped; /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */ ?>>    <?php

			if ( ! empty( $args['label'] ) ) {  ?>
				<span class="epda-main_label <?php echo esc_attr( $args['main_label_class'] ); ?>">                <?php
					echo wp_kses_post( $args['label'] );
					self::display_tooltip( $args['tooltip_title'], $args['tooltip_body'], $args['tooltip_args'], $args['tooltip_external_links'] );

	                if ( $args['is_pro'] ) {
		                self::display_pro_setting_tag( $args['pro_tooltip_args'] );
	                }   ?>
				</span> <?php
			}   ?>

            <div class="epda-radio-buttons-container <?php echo esc_attr( $args['input_class'] ); ?>" id="<?php echo esc_attr( $args['name'] ); ?>">              <?php

				foreach( $args['options'] as $key => $label ) {
					if ( empty( $label ) ) {
						continue;
					}?>
                    <div class="epda-input-container">

                        <input class="epda-input" type="radio"
                               name="<?php echo esc_attr( $args['name'] ); ?>"
                               id="<?php echo esc_attr( $args['name'] . $ix ); ?>"
                               value="<?php echo esc_attr( $key ); ?>"  <?php
								checked( $key, $args['value'] );	?>
                        >
	                    <label class="epda-label" for="<?php echo esc_attr( $args['name'] . $ix ); ?>">
                            <span class="epda-label__text"><?php echo wp_kses_post( $label ); ?></span>
                        </label>

                    </div> <?php

					$ix++;
				} //foreach				?>

            </div> <?php

			if ( $args['desc'] ) {

                // If there is a condition check for which option is checked.
                $showDesc = '';

                // If there is a condition check for which option is checked.
				if ( isset( $args['desc_condition'] ) ) {
					if ( (string) esc_attr( $args['desc_condition'] ) === (string) esc_attr( $args['value'] ) ) {
						$showDesc = 'radio-buttons-horizontal-desc--show';
					}
				} else {  // If no Condition show desc all the time.
					$showDesc = 'radio-buttons-horizontal-desc--show';
				}
				echo '<span class="radio-buttons-horizontal-desc ' . esc_attr( $showDesc ) . '">' . wp_kses_post( $args['desc'] ) . '</span>';

			} ?>

        </div>	<?php

		if ( $args['return_html'] ) {
			return ob_get_clean();
		}
	}

	/**
	 * Renders several HTML radio buttons in a row but as Icons.
	 *
	 * @param array $args
	 *  options key     = icon CSS name
	 *  option value    = text ( Hidden )*
	 */
	public static function radio_buttons_icon_selection( $args = array() ) {

		$defaults = array(
			'id'                => 'radio',
			'name'              => 'radio-buttons',
		);
		$args = self::add_defaults( $args, $defaults );
		$args = self::get_specs_info( $args );

		$ix = 0;

		$group_data_escaped = self::get_data_escaped( $args['group_data'] );    ?>

		<div class="epda-input-group epda-admin__radio-icons <?php echo esc_attr( $args['input_group_class'] ); ?>" id="<?php echo esc_attr( $args['name'] ); ?>_group" <?php echo $group_data_escaped; /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */ ?>>

			<span class="epda-main_label <?php echo esc_attr( $args['main_label_class'] ); ?>"><?php echo wp_kses_post( $args['label'] );

				self::display_tooltip( $args['tooltip_title'], $args['tooltip_body'], $args['tooltip_args'], $args['tooltip_external_links'] );

				if ( $args['is_pro'] ) {
	                self::display_pro_setting_tag( $args['pro_tooltip_args'] );
				} ?>
            </span>

			<div class="epda-radio-buttons-container <?php echo esc_attr( $args['input_class'] ); ?>" id="<?php echo esc_attr( $args['name'] ); ?>">              <?php 
			
				foreach( $args['options'] as $key => $label ) {	?>

					<div class="epda-input-container">

						<label class="epda-label" for="<?php echo esc_attr( $args['name'] . $ix ); ?>">
							<span class="epda-label__text"><?php echo esc_html( $label ); ?></span>
							<input class="epda-input" type="radio"
							        name="<?php echo esc_attr( $args['name'] ); ?>"
							        id="<?php echo esc_attr( $args['name'] . $ix ); ?>"
							        value="<?php echo esc_attr( $key ); ?>" <?php
									checked( $key, $args['value'] );	?>
							>   <?php
							if ($args['name'] !== 'arrow_type') { ?>
								<span class="<?php echo preg_match( '/ep_font_/', $key ) ? '' : 'epdafa epdafa-font epdafa-'; ?><?php echo esc_attr( $key ); ?> epdafa-input-icon"></span>  <?php
							} else { ?>
								<span class="<?php echo esc_attr( $key ); ?> epdafa-input-icon"></span> <?php
							} ?>
						</label>

					</div> <?php

					$ix++;
				} //foreach

				if ( $args['desc'] ) {
					echo wp_kses_post( $args['desc'] );
				} ?>
			</div>
		</div>	<?php
	}

	/**
	 * Output submit button
	 *
	 * @param string $button_label
	 * @param string $action
	 * @param string $main_class
	 * @param string $html - any additional hidden fields
	 * @param bool $unique_button - is this unique button or a group of buttons - use 'ID' for the first and 'class' for the other
	 * @param bool $return_html
	 * @param string $inputClass
	 * @return string
	 */
	public static function submit_button_v2( $button_label, $action, $main_class='', $html='', $unique_button=true, $return_html=false, $inputClass='' ) {

		if ( $return_html ) {
			ob_start();
		}		?>

		<div class="epda-submit <?php echo esc_attr( $main_class ); ?>">
			<input type="hidden" name="action" value="<?php echo esc_attr( $action ); ?>">     <?php

			if ( $unique_button ) {  ?>
				<input type="hidden" name="_wpnonce_epda_ajax_action" value="<?php echo wp_create_nonce( "_wpnonce_epda_ajax_action" ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped   ?>">
				<input type="submit" id="<?php echo esc_attr( $action ); ?>" class="<?php echo esc_attr( $inputClass ); ?>" value="<?php echo esc_attr( $button_label ); ?>" >  <?php
			} else {    ?>
				<input type="submit" class="<?php echo esc_attr( $action ) . ' ' . esc_attr( $inputClass ); ?>" value="<?php echo esc_attr( $button_label ); ?>" >  <?php
			}

			echo wp_kses_post( $html );  ?>
		</div>  <?php

		if ( $return_html ) {
			return ob_get_clean();
		}

		return '';
	}

	/**
	 * Display a tooltip for admin form fields.
	 *
	 * @param string $title - The title of the tooltip.
	 * @param string $body_escaped - The content/body of the tooltip.
	 * @param array $args - Additional arguments for the tooltip.
	 * @param array $external_links - An array of external link for the tooltip. //// [ [ 'link_text' => string, 'link_url' => string ], [...] ]
	 *
	 * @return void
	 */
	public static function display_tooltip( $title, $body_escaped, $args = array(), $external_links = array() ) {
		if ( empty( $body_escaped ) && empty( $external_links ) ) {
			return;
		}

		$defaults = array(
			'class'         => '',
			'open-icon'     => 'info-circle',
			'open-text'     => '',
			'link_text'     => esc_html__( 'Learn More', 'scroll-down-arrow' ),
			'link_url'      => '',
			'link_target'   => '_blank'
		);
		$args = array_merge( $defaults, $args );  ?>

		<div class="epda__option-tooltip <?php echo esc_attr( $args['class'] ); ?>">
			<span class="epda__option-tooltip__button <?php echo $args['open-icon'] ? 'epdafa epdafa-' . esc_attr( $args['open-icon'] ) : ''; ?>">  <?php
				echo esc_html( $args['open-text'] );  ?>
			</span>
			<div class="epda__option-tooltip__contents">    <?php
				if ( ! empty( $title ) ) {   ?>
					<div class="epda__option-tooltip__header">						<?php
						echo esc_html( $title );  ?>
					</div>  <?php
				}   ?>
				<div class="epda__option-tooltip__body">					<?php
						//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						echo $body_escaped;

					if ( ! empty( $external_links ) ) {
						foreach ( $external_links as $external_link ) { ?>
							<div class="epda__option-tooltip__body__external_link">
								<a target="_blank" href="<?php echo esc_url( $external_link['link_url'] ); ?>"><?php echo esc_html( $external_link['link_text'] ); ?></a><span class="epdafa epdafa-external-link"></span>
							</div> <?php
						}
					} ?>

				</div>  <?php
				if ( ! empty( $args['link_url'] ) ) { ?>
					<div class="epda__option-tooltip__footer">
						<a href="<?php echo esc_url( $args['link_url'] ); ?>" class="epda__option-tooltip__button" target="<?php echo esc_attr( $args['link_target'] ); ?>">  <?php
							echo esc_html( $args['link_text'] );    ?>
						</a>
					</div>  <?php
				}  ?>
			</div>
		</div>  <?php
	}

	/**
	 *  Display a PRO Tag for settings and a Tool tip if user clicks on the settings.
	 *
	 * @param $args
	 */
	public static function display_pro_setting_tag( $args ) {  ?>

		<div class="epda__option-pro-tag-container">
			<div class="epda__option-pro-tag"><?php echo esc_html__( 'PRO', 'scroll-down-arrow' ); ?></div>
			<div class="epda__option-pro-tooltip">

				<div class="epda__option-pro-tooltip__contents">
					<div class="epda__option-pro-tooltip__header">					<?php
						echo empty( $args['title'] ) ? '' : esc_html( $args['title'] ); ?>
					</div>
					<div class="epda__option-pro-tooltip__body">					<?php
						echo empty( $args['body'] ) ? '' : esc_html( $args['body'] ); ?>
					</div>
					<div class="epda__option-pro-tooltip__footer">  <?php
						if ( ! empty( $args['btn_url'] ) && ! empty( $args['btn_text'] ) ) { ?>
							<a class="epda__option-pro-tooltip__button epda-success-btn" href="<?php echo esc_url( $args['btn_url'] ) ?>" target="_blank" rel="nofollow">							<?php
								echo esc_html( $args['btn_text'] ); ?>
							</a>    <?php
						}   ?>
					</div>
				</div>
			</div>
		</div>		 <?php
	}

	/**
	 * Return an HTML Color Picker
	 *
	 * @param array $args Arguments for the text field
	 * @param bool $return_html
	 * @return false|string
	 */
	public static function color( $args = array(), $return_html=false ) {

		if ( $return_html ) {
			ob_start();
		}

		$args = self::add_defaults( $args );
		$args = self::get_specs_info( $args );

		$group_data_escaped = self::get_data_escaped( $args['group_data'] );
		$data_escaped = self::get_data_escaped( $args['data'] );  ?>

		<div class="epda-input-group epda-admin__color-field <?php echo esc_html( $args['input_group_class'] ); ?>" id="<?php echo esc_attr( $args['name'] ); ?>_group" <?php echo $group_data_escaped; /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */ ?>>

			<label class="<?php echo esc_attr( $args['label_class'] ); ?>" for="<?php echo esc_attr( $args['name'] ); ?>">  <?php
				echo esc_html( $args['label'] );
				self::display_tooltip( $args['tooltip_title'], $args['tooltip_body'], $args['tooltip_args'], $args['tooltip_external_links'] );
				if ( $args['is_pro'] ) {
					self::display_pro_setting_tag( $args['pro_tooltip_args'] );
				}     ?>
			</label>

			<div class="input_container epda-color-picker <?php echo esc_attr( $args['input_class'] ); ?>">
				<input type="text"
					   name="<?php echo esc_attr( $args['name'] ); ?>"
					   id="<?php echo esc_attr( $args['name'] ); ?>"
					   value="<?php echo esc_attr( $args['value'] ); ?>"
						<?php echo $data_escaped; /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */ ?>
				>
			</div>

		</div>		<?php

		if ( $return_html ) {
			return ob_get_clean();
		}

		return '';
	}

	/**
	 * Return data attributes with escaped keys and values
	 *
	 * @param $data
	 * @return string
	 */
	public static function get_data_escaped( $data ) {
		$data_escaped = '';

		if ( empty( $data ) ) {
			return $data_escaped;
		}

		foreach ( $data as $key => $value ) {
			$data_escaped .= 'data-' . esc_attr( $key ) . '="' . esc_attr( $value ) . '" ';
		}

		return $data_escaped;
	}

	private static function get_specs_info( $args ) {

		if ( empty( $args['specs'] ) ) {
			return $args;
		}

		$specs_name = $args['specs'];
		$field_specs = EPDA_Config_Specs::get_fields_specification();

		if ( empty( $field_specs[$specs_name] ) ) {
			return $args;
		}

		$field_spec = $field_specs[$specs_name];
		$field_spec = wp_parse_args( $field_spec, EPDA_Config_Specs::get_defaults() );

		$args_specs = array(
			'name'              => $field_spec['name'],
			'label'             => empty( $args['label'] ) ? $field_spec['label'] : $args['label'],
			'type'              => $field_spec['type'],
			'input_group_class' => 'epda-admin__input-field epda-admin__' . $field_spec['type'] . '-field' . ' ' . $args['input_group_class'],
			'input_class'       => ! empty( $field_spec['is_pro'] ) ? 'epda-admin__input-disabled' : '',
			'is_pro'            => empty( $field_spec['is_pro'] ) ? false : $field_spec['is_pro'],
			'desc'              => empty( $args['desc'] ) ? '' : $args['desc'],
			'input_size'        => empty( $field_spec['input_size'] ) ? 'medium' : $field_spec['input_size'],
		);

		if ( $args_specs['type'] == 'select' && empty( $args['options'] ) ) {
			$args['options'] = $field_spec['options'];
		}

		return array_merge( $args, $args_specs );
	}
}