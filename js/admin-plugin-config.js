jQuery(document).ready(function($) {

	/*************************************************************************************************
	 *
	 *          Misc
	 *
	 ************************************************************************************************/

	// Disable PRO inputs for Advanced Settings page
	$( '#epda-admin__boxes-list__settings .epda-admin__input-disabled' ).each( function(){
		$( this ).find( 'input, select, textarea' ).prop( 'disabled', true );
	});

	// Toggle the PRO Setting Tooltip
	$( document ).on( 'click', '.epda-admin__input-disabled, .epda__option-pro-tag', function (){
		$( this ).closest( '.epda-input-group' ).find( '.epda__option-pro-tooltip' ).toggle();
	});

	// Hide PRO Setting Tooltip if click outside the tooltip
	$( document ).on( 'click', function (e){
		let target = $( e.target );
		if ( ! target.closest( '.epda__option-pro-tooltip' ).length && ! target.closest( '.epda-admin__input-disabled' ).length && ! target.closest( '.epda__option-pro-tag' ).length  ) {
			$( '.epda__option-pro-tooltip' ).hide();
		}
	});


	/*************************************************************************************************
	 *
	 *          Save Settings
	 *
	 ************************************************************************************************/

	// Save Settings button - Submit
	$( document.body ).on( 'click', '#epda_save_da_settings', function( e ) {
		e.preventDefault();

		let form = $( this ).closest( '.epda-admin__boxes-list--active' );

		// Prepare form data
		let postData = {
			action: 'epda_save_da_settings',
			_wpnonce_epda_ajax_action: epda_vars.nonce,

			arrow_css_id:           form.find( '[name="arrow_css_id"]' ).val(),
			arrow_ass_class:        form.find( '[name="arrow_css_class"]' ).val(),
			arrow_type:             form.find( '[name="arrow_type"]:checked' ).val(),
			animation_type:         form.find( '[name="animation_type"]' ).val(),
			size:             		  form.find( '[name="size"]' ).val(),
			color:            		  form.find( '[name="color"]' ).val(),
			duration_time:         	form.find( '[name="duration_time"]' ).val(),
			bouncing_speed:         form.find( '[name="bouncing_speed"]:checked' ).val(),
			move_to_id:             form.find( '[name="move_to_id"]' ).val(),
			disappear_after_scroll: form.find( '[name="disappear_after_scroll"]:checked' ).val(),
			enable_bouncing:        form.find( '[name="enable_bouncing"]:checked' ).val(),
			enable_duration:        form.find( '[name="enable_duration"]:checked' ).val(),

			// Locations
			location_pages_list: get_widget_locations_data( form, 'page' ),
			location_posts_list: get_widget_locations_data( form, 'post' ),
			location_cpts_list: get_widget_locations_data( form, 'cpt' ),
		};

		// Send form
		epda_send_ajax( postData, function( response ){
			if ( ! response.error && typeof response.message != 'undefined' ) {
				epda_show_success_notification( response.message );
			}
		});
	});

	/*************************************************************************************************
	 *
	 *          AJAX calls
	 *
	 ************************************************************************************************/
	
	// generic AJAX call handler
	function epda_send_ajax( postData, refreshCallback, callbackParam, reload, alwaysCallback, $loader ) {

		let errorMsg;
		let theResponse;
		refreshCallback = (typeof refreshCallback === 'undefined') ? 'epda_callback_noop' : refreshCallback;

		$.ajax({
			type: 'POST',
			dataType: 'json',
			data: postData,
			url: ajaxurl,
			beforeSend: function (xhr)
			{
				if ( typeof $loader == 'undefined' || $loader === false ) {
					epda_loading_Dialog('show', '');
				} 
				
				if ( typeof $loader == 'object' ) {
					epda_loading_Dialog('show', '', $loader);
				} 
				
			}
		}).done(function (response)        {
			theResponse = ( response ? response : '' );
			if ( theResponse.error || typeof theResponse.message === 'undefined' ) {
				//noinspection JSUnresolvedVariable,JSUnusedAssignment
				errorMsg = theResponse.message ? theResponse.message : epda_admin_notification('', epda_vars.reload_try_again, 'error');
			}

		}).fail( function ( response, textStatus, error )        {
			//noinspection JSUnresolvedVariable
			errorMsg = ( error ? ' [' + error + ']' : epda_vars.unknown_error );
			//noinspection JSUnresolvedVariable
			errorMsg = epda_admin_notification(epda_vars.error_occurred + '. ' + epda_vars.msg_try_again, errorMsg, 'error');
		}).always(function() {
			
			theResponse = (typeof theResponse === 'undefined') ? '' : theResponse;
			
			if ( typeof alwaysCallback == 'function' ) {
				alwaysCallback( theResponse );
			}

			epda_loading_Dialog('remove', '');

			if ( errorMsg ) {
				$('.epda-bottom-notice-message').remove();
				$('body #epda-admin-page-wrap').append(errorMsg).removeClass('fadeOutDown');
				
				setTimeout( function() {
					$('.epda-bottom-notice-message').addClass( 'fadeOutDown' );
				}, 10000 );
				return;
			}

			if ( typeof refreshCallback === "function" ) {
				
				if ( callbackParam === 'undefined' ) {
					refreshCallback(theResponse);
				} else {
					refreshCallback(theResponse, callbackParam);
				}
			} else {
				if ( reload ) {
					location.reload();
				}
			}
		});
	}

	/**
	 * Displays a Center Dialog box with a loading icon and text.
	 *
	 * This should only be used for indicating users that loading or saving or processing is in progress, nothing else.
	 * This code is used in these files, any changes here must be done to the following files.
	 *   - admin-plugin-pages.js
	 *   - admin-kb-config-scripts.js
	 *
	 * @param  {string}    displayType     Show or hide Dialog initially. ( show, remove )
	 * @param  {string}    message         Optional    Message output from database or settings.
	 *
	 * @return {html}                      Removes old dialogs and adds the HTML to the end body tag with optional message.
	 *
	 */
	function epda_loading_Dialog( displayType, message, $el ){

		if( displayType === 'show' ){
			
			let loadingClass = ( typeof $el == 'undefined' ) ? '' : 'epda-admin-dialog-box-loading--relative';
			
			let output =
				'<div class="epda-admin-dialog-box-loading ' + loadingClass + '">' +

				//<-- Header -->
				'<div class="epda-admin-dbl__header">' +
				'<div class="epda-admin-dbl-icon epdafa epdafa-hourglass-half"></div>'+
				(message ? '<div class="epda-admin-text">' + message + '</div>' : '' ) +
				'</div>'+

				'</div>' +
				'<div class="epda-admin-dialog-box-overlay ' + loadingClass + '"></div>';

			//Add message output at the end of Body Tag
			if ( typeof $el == 'undefined' ) {
				$( 'body' ).append( output );
			} else { 
				$el.append( output );
			}
			
		}else if( displayType === 'remove' ){

			// Remove loading dialogs.
			$( '.epda-admin-dialog-box-loading' ).remove();
			$( '.epda-admin-dialog-box-overlay' ).remove();
		}

	}

	/* Dialogs --------------------------------------------------------------------*/
	// SHOW INFO MESSAGES
	function epda_admin_notification( $title, $message , $type ) {
		return '<div class="epda-bottom-notice-message">' +
			'<div class="contents">' +
			'<span class="' + $type + '">' +
			($title ? '<h4>'+$title+'</h4>' : '' ) +
			($message ? '<p>' + $message + '</p>': '') +
			'</span>' +
			'</div>' +
			'<div class="epda-close-notice epdafa epdafa-window-close"></div>' +
			'</div>';
	}

	let epda_notification_timeout;

	function epda_show_error_notification( $message, $title = '' ) {
		$( '.epda-bottom-notice-message' ).remove();
		$( 'body #epda-admin-page-wrap' ).append( epda_admin_notification( $title, $message, 'error' ) );

		clearTimeout( epda_notification_timeout );
		epda_notification_timeout = setTimeout( function() {
			$('.epda-bottom-notice-message').addClass( 'fadeOutDown' );
		}, 10000 );
	}

	function epda_show_success_notification( $message, $title = '' ) {
		$( '.epda-bottom-notice-message' ).remove();
		$( 'body #epda-admin-page-wrap' ).append( epda_admin_notification( $title, $message, 'success' ) );

		clearTimeout( epda_notification_timeout );
		epda_notification_timeout = setTimeout( function() {
			$( '.epda-bottom-notice-message' ).addClass( 'fadeOutDown' );
		}, 10000 );
	}

	// scroll to element with animation
	function epda_scroll_to( $el ) {
		if ( ! $el.length ) {
			return;
		}
		
		$("html, body").animate({ scrollTop: $el.offset().top - 100 }, 300);
	}

	/*************************************************************************************************
	 *
	 *          Show On Pages - Settings
	 *
	 ************************************************************************************************/

	function show_widget_search_loader( el ) {
		let $wrap = el.closest( '.epda-wp__locations-list-search-body' );

		if ( ! $wrap.length ) {
			return;
		}

		$wrap.find( '.epda-wp__locations-list-input-wrap' ).addClass( 'epda-wp__locations-list-input-wrap--loader' );
	}

	function hide_search_loader( el ) {
		let $wrap = el.closest( '.epda-wp__locations-list-search-body' );

		if ( ! $wrap.length ) {
			return;
		}

		$wrap.find( '.epda-wp__locations-list-input-wrap' ).removeClass( 'epda-wp__locations-list-input-wrap--loader' );
	}

	// Search input
	$( document.body ).on( 'input click', '.epda-admin__boxes-list__box__content .epda-wp__locations-list-input', function( e ){

		let form = $( this ).closest( '.epda-admin__boxes-list__box__content' ),
			search_input = $( this ),
			search_value = search_input.val(),
			locations_type = search_input.data( 'post-type' );

		setTimeout( function() {
			if ( search_value !== search_input.val() ) {
				return;
			}

			let postData = {
				action: 'epda_search_locations',
				_wpnonce_epda_ajax_action : epda_vars.nonce,
				locations_type: locations_type,
				search_value: search_value,
				excluded_ids: get_excluded_widget_locations( form ),
			};

			show_widget_search_loader( search_input );

			epda_send_ajax( postData, function( response ) {
				hide_search_loader( search_input );
				if ( ! response.error && typeof response.locations != 'undefined' ) {
					form.find( '.epda-wp__locations-list-select--' + locations_type + ' .epda-wp__found-locations-list' ).html( response.locations ).show();
				}
			}, false, false, undefined, 'no-loader' );
		}, 500 );
	} );

	// Add Location to the selected Locations list when click on any Location inside found Locations list
	$( document ).on( 'click', '.epda-admin__boxes-list__box__content .epda-wp__found-locations-list li', function( e ) {
		e.stopPropagation();
		if ( $( this ).find( '.epda-wp__location-assigns' ).length > 0 ) {
			return false;
		}
		$( this ).appendTo( $( this ).parent().parent().find( '.epda-wp__selected-locations-list' ) );

		epda_update_selected_locations_visibility();
	} );

	// Hide list of found Locations when click outside the list
	$( document ).on( 'click', function() {
		$( '.epda-wp__found-locations-list' ).html( '' ).hide();
	});

	// Remove Location from selected list if clicked on it
	$( document ).on( 'click' , '.epda-admin__boxes-list__box__content .epda-wp__selected-locations-list li', function() {

		let location_id = $( this ).data( 'id' );

		// Remove element from widget page and from popup dialog
		$( '.epda-admin__boxes-list__box__content .epda-wp__selected-locations-list li[data-id="' + location_id  + '"]' ).remove();

		epda_update_selected_locations_visibility();
	} );

	/**
	 * Update selected locations visibility
	 */
	function epda_update_selected_locations_visibility() {

		// Max visible locations
		let limit = 15;

		$( '.epda-admin__boxes-list__box__content .epda-wp__selected-locations-list' ).each( function(){

			let count = 0;
			let $list_items = $( this ).find( 'li' );

			// Update list item visibility
			$list_items.each( function(){
				if ( ++count <= limit ) {
					$( this ).removeClass( 'epda-wp__location--hidden' )
				} else {
					$( this ).addClass( 'epda-wp__location--hidden' )
				}
			} );

			let $view_all_button = $( this ).closest( '.epda-wp__selected-locations-popup' ).find( 'a' );

			// Update 'View All' button visibility
			if ( $list_items.length <= limit ) {
				$view_all_button.hide();
			} else {
				$view_all_button.show();
			}
		} );
	}

	function get_widget_locations_data( form, location_type ) {
		let locations_list = [];
		form.find( '.epda-wp__locations-list-select--' + location_type + ' .epda-wp__locations-list-wrap > .epda-wp__selected-locations-list li' ).each( function() {
			locations_list.push( $( this ).data( 'id' ) );
		} );
		return locations_list;
	}

	function get_excluded_widget_locations( form ) {
		let excluded_pages = get_widget_locations_data( form, 'page' ),
			excluded_posts = get_widget_locations_data( form, 'post' ),
			excluded_cpts = get_widget_locations_data( form, 'cpt' );
		return excluded_pages.concat( excluded_posts ).concat( excluded_cpts );
	}
});