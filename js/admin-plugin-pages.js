jQuery(document).ready(function($) {

	let epda = $( '#epda-admin-page-wrap' );

	// Set special CSS class to #wpwrap for only HD admin pages
	if ( $( epda ).find( '.epda-admin__content' ).length > 0 ) {
		$( '#wpwrap' ).addClass( 'epda-admin__wpwrap' );
	}

	/*************************************************************************************************
	 *
	 *          ADMIN PAGES
	 *
	 ************************************************************************************************/

	/* Admin Top Panel Items -----------------------------------------------------*/
	$( '.epda-admin__top-panel__item' ).on( 'click', function() {

		// Warning for visual Editor
		if ( $( this ).hasClass( 'epda-article-structure-dialog' ) ) {
			return;
		}

		let active_top_panel_item_class = 'epda-admin__top-panel__item--active';
		let active_boxes_list_class = 'epda-admin__boxes-list--active';
		let active_secondary_panel_class = 'epda-admin__secondary-panel--active';

		// Do nothing for already active item
		if ( $( this ).hasClass( active_top_panel_item_class ) ) {
			return;
		}

		let list_key = $( this ).attr( 'data-target' );

		// Change class for active Top Panel item
		$( '.epda-admin__top-panel__item' ).removeClass( active_top_panel_item_class );
		$( this ).addClass( active_top_panel_item_class );

		// Change class for active Boxes List
		$( '.epda-admin__boxes-list' ).removeClass( active_boxes_list_class );
		$( '#epda-admin__boxes-list__' + list_key ).addClass( active_boxes_list_class );

		// Change class for active Secondary Panel
		$( '.epda-admin__secondary-panel' ).removeClass( active_secondary_panel_class );
		$( '#epda-admin__secondary-panel__' + list_key ).addClass( active_secondary_panel_class );

		// Licenses tab on Add-ons page - support for existing add-ons JS handlers
		let active_top_panel_item = this;
		setTimeout( function () {
			if ( $( active_top_panel_item ).attr( 'id' ) === 'epda_license_tab' ) {
				$( '#epda_license_tab').trigger( 'click' );
			}
		}, 100);

		// Update anchor
		window.location.hash = '#' + list_key;
	});

	// Set correct active tab after the page reloading
	(function(){
		let url_parts = window.location.href.split( '#' );

		// Set first item as active if there is no any anchor
		if ( url_parts.length === 1 ) {
			$( $( '.epda-admin__top-panel__item' )[0] ).trigger( 'click' );
			return;
		}

		let target_kyes = url_parts[1].split( '__' );

		let target_main_items = $( '.epda-admin__top-panel__item[data-target="' + target_kyes[0] + '"]' );

		// If no target items was found, then set the first item as active
		if ( target_main_items.length === 0 ) {
			$( $( '.epda-admin__top-panel__item' )[0] ).trigger( 'click' );
			return;
		}

		// Change class for active item
		$( target_main_items[0] ).trigger( 'click' );

		// Key for Secondary item was specified and it is not empty
		if ( target_kyes.length > 1 && target_kyes[1].length > 0 ) {
			setTimeout( function() {

				let target_secondary_items = $( '.epda-admin__secondary-panel__item[data-target="' + url_parts[1] + '"]' );

				// If no target items was found, then set the first item as active
				if ( target_secondary_items.length === 0 ) {
					$( $( '.epda-admin__secondary-panel__item' )[0] ).trigger( 'click' );
					return;
				}

				// Change class for active item
				$( target_secondary_items[0] ).trigger( 'click' );
			}, 100 );
		}
	})();

	/* Admin Secondary Panel Items -----------------------------------------------*/
	$( '.epda-admin__secondary-panel__item' ).on( 'click', function() {

		// Warning for visual Editor
		if ( $( this ).hasClass( 'epda-article-structure-dialog' ) ) {
			return;
		}

		let active_secondary_panel_item_class = 'epda-admin__secondary-panel__item--active';
		let active_secondary_boxes_list_class = 'epda-setting-box__list--active';

		// Do nothing for already active item
		if ( $( this ).hasClass( active_secondary_panel_item_class ) ) {
			return;
		}

		let list_key = $( this ).attr( 'data-target' );
		let parent_list_key = list_key.split( '__' )[0];

		// Change class for active Top Panel item
		$( '#epda-admin__secondary-panel__' + parent_list_key ).find( '.epda-admin__secondary-panel__item' ).removeClass( active_secondary_panel_item_class );
		$( this ).addClass( active_secondary_panel_item_class );

		// Change class for active Boxes List
		$( '#epda-admin__boxes-list__' + parent_list_key ).find( '.epda-setting-box__list' ).removeClass( active_secondary_boxes_list_class );
		$( '#epda-setting-box__list-' + list_key ).addClass( active_secondary_boxes_list_class );

		// Update anchor
		window.location.hash = '#' + list_key;
	});

	/* Misc ----------------------------------------------------------------------*/
	(function(){

		// TOGGLE DEBUG
		epda.find( '#epda_toggle_debug' ).on( 'click', function() {

			// Remove old messages
			$( '.epda-top-notice-message' ).html( '' );
			let parent = $( this ).parent();

			let postData = {
				action: parent.find( 'input[name="action"]' ).val(),
				_wpnonce_epda_ajax_action: parent.find( 'input[name="_wpnonce_epda_ajax_action"]' ).val()
			};

			epda_send_ajax( postData, function() {
				location.reload();
			} );
		});

		// ADD-ON PLUGINS + OUR OTHER PLUGINS - PREVIEW POPUP
		 (function(){
			//Open Popup larger Image
			epda.find( '.featured_img' ).on( 'click', function( e ){

				e.preventDefault();
				e.stopPropagation();

				epda.find( '.epda-image-zoom' ).remove();

				var img_src;
				var img_tag = $( this ).find( 'img' );
				if ( img_tag.length > 1 ) {
					img_src = $(img_tag[0]).is(':visible') ? $(img_tag[0]).attr('src') :
							( $(img_tag[1]).is(':visible') ? $(img_tag[1]).attr('src') : $(img_tag[2]).attr('src') );

				} else {
					img_src = $( this ).find( 'img' ).attr( 'src' );
				}

				$( this ).after('' +
					'<div id="epda_image_zoom" class="epda-image-zoom">' +
					'<img src="' + img_src + '" class="epda-image-zoom">' +
					'<span class="close icon_close"></span>'+
					'</div>' + '');

				//Close Plugin Preview Popup
				$('html, body').on('click.epda', function(){
					$( '#epda_image_zoom' ).remove();
					$('html, body').off('click.epda');
				});
			});
		})();

		//Info Icon for Licenses
		$( '#add_on_panels' ).on( 'click', '.ep_font_icon_info', function(){

			$( this ).parent().find( '.ep_font_icon_info_content').toggle();

		});
	})();

	// When clicking on a link with the following class it will show message with target class only
	$( '.epda-nh__dynamic-notice__toggle' ).on( 'click', function ( e ) {
		e.stopPropagation();
		$( this ).parent().find( '.epda-nh__dynamic-notice__target' ).show();
		return false;
	});

	// Initialize color-picker fields
	$( '.epda-admin__color-field input' ).wpColorPicker({
		change: function( colorEvent, ui) {
			setTimeout( function() {
				$( colorEvent.target).trigger('change');
			}, 50);
		},
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

				if ( typeof callbackParam === 'undefined' ) {
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


	/*************************************************************************************************
	 *
	 *          DIALOGS
	 *
	 ************************************************************************************************/

	/**
	  * Displays a Center Dialog box with a loading icon and text.
	  *
	  * This should only be used for indicating users that loading or saving or processing is in progress, nothing else.
	  * This code is used in these files, any changes here must be done to the following files.
	  *   - admin-plugin-pages.js
	  *   - admin-kb-config-scripts.js
	  *   - admin-kb-wizard-script.js
	  *
	  * @param  {string}    displayType     Show or hide Dialog initially. ( show, remove )
	  * @param  {string}    message         Optional    Message output from database or settings.
	  *
	  * @return {html}                      Removes old dialogs and adds the HTML to the end body tag with optional message.
	  *
	  */
	function epda_loading_Dialog( displayType, message ){

		if( displayType === 'show' ){

			let output =
				'<div class="epda-admin-dialog-box-loading">' +

				//<-- Header -->
				'<div class="epda-admin-dbl__header">' +
				'<div class="epda-admin-dbl-icon epdafa epdafa-hourglass-half"></div>'+
				(message ? '<div class="epda-admin-text">' + message + '</div>' : '' ) +
				'</div>'+

				'</div>' +
				'<div class="epda-admin-dialog-box-overlay"></div>';

			//Add message output at the end of Body Tag
			$( 'body' ).append( output );
		}else if( displayType === 'remove' ){

			// Remove loading dialogs.
			$( '.epda-admin-dialog-box-loading' ).remove();
			$( '.epda-admin-dialog-box-overlay' ).remove();
		}

	}

	// Close Button Message if Close Icon clicked
	$( document ).on( 'click', '.epda-bottom-notice-message .epdafa-window-close', function() {
		let bottom_message = $( this ).closest( '.epda-bottom-notice-message' );
		bottom_message.addClass( 'fadeOutDown' );
		setTimeout( function() {
			bottom_message.html( '' );
		}, 1000);
	} );

	// SHOW INFO MESSAGES
	function epda_admin_notification( $title, $message , $type ) {
		return '<div class="epda-bottom-notice-message">' +
			'<div class="contents">' +
			'<span class="' + $type + '">' +
			($title ? '<h4>' + $title + '</h4>' : '' ) +
			($message ? '<p>' + $message + '</p>': '') +
			'</span>' +
			'</div>' +
			'<div class="epda-close-notice epdafa epdafa-window-close"></div>' +
			'</div>';
	}

	//Dismiss ongoing notice
	$(document).on( 'click', '.epda-notice-dismiss', function( event ) {
		event.preventDefault();
		$('.notice-'+$(this).data('notice-id')).slideUp();
		var postData = {
			action: 'epda_dismiss_ongoing_notice',
			epda_dismiss_id: $(this).data('notice-id')
		};
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: ajaxurl,
			data: postData
		});
	} );

	function clear_bottom_notifications() {
		var bottom_message = $('body').find('.epda-bottom-notice-message');
		if ( bottom_message.length ) {
			bottom_message.addClass( 'fadeOutDown' );
			setTimeout( function() {
				bottom_message.html( '' );
			}, 1000);
		}
	}

	function clear_message_after_set_time(){

		var epda_timeout;
		if( $('.epda-bottom-notice-message .contents' ).length > 0 ) {
			clearTimeout(epda_timeout);

			//Add fadeout class to notice after set amount of time has passed.
			epda_timeout = setTimeout(function () {
				clear_bottom_notifications();
			} , 10000);
		}
	}
	clear_message_after_set_time();
});