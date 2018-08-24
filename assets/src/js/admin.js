(function ( $ ) {
	'use strict';

	/**
	 * All of the code for our admin-specific JavaScript source
	 * should reside in this file.
	 *
	 * Note that this assume you're going to use jQuery, so it prepares
	 * the $ function reference to be used within the scope of this
	 * function.
	 *
	 * From here, we are able to define handlers for when the DOM is
	 * ready:
	 *
	 * $(function() {
     *
     * });
	 *
	 * Or when the window is loaded:
	 *
	 * $( window ).load(function() {
     *
     * });
	 *
	 * ...and so on.
	 */
	$( function () {

		// Hide/Show fields when redirect to value changes.
		$( '#jj4t3_redirect_to' ).change( function () {

			switch ( $( this ).val() ) {

				case 'page':
					$( '#custom_page' ).show();
					$( '#custom_url' ).hide();
					break;

				case 'link':
					$( '#custom_url' ).show();
					$( '#custom_page' ).hide();
					break;

				default:
					$( '#custom_page' ).hide();
					$( '#custom_url' ).hide();
					break;
			}
		} );

		// open custom redirect form modal.
		$( '.jj4t3_redirect_thickbox' ).on( 'click', function () {

			var data = {
				'action': 'jj4t3_redirect_thickbox',
				'url_404': $( this ).attr( 'url_404' ),
				'nonce': $( this ).attr( 'wpnonce' )
			};

			/** global: ajaxurl */
			$.post( ajaxurl, data, function ( response ) {

				/** global: jj4t3strings (available from localization) */
				tb_show( jj4t3strings.redirect, '#TB_inline?width=700&height=370&inlineId=jj4t3-redirect-modal' );

				$( '#jj4t3_redirect_404' ).val( response.url_404 );
				$( '#jj4t3_redirect_404_text' ).html( response.url_404 );
				$( '#jj4t3_redirect_url' ).val( response.url );
				$( '#jj4t3_custom_redirect_type' ).val( response.type );

				jj4t3Check( 'jj4t3_custom_redirect_redirect', response.redirect );
				jj4t3Check( 'jj4t3_custom_redirect_log', response.log );
				jj4t3Check( 'jj4t3_custom_redirect_alert', response.alert );
			} );
		} );

		// Save custom redirect value.
		$( '#jj4t3_custom_redirect_submit' ).on( 'click', function () {

			$( this ).addClass( 'disabled' );

			$( '.jj4t3-spinner' ).css( 'visibility', 'visible' );

			// Form data.
			var data = $( '#jj4t3_custom_redirect_form' ).serialize();

			/** global: ajaxurl */
			$.post( ajaxurl, data, function ( response ) {

				// Close the modal.
				tb_remove();
				$( '#jj4t3_custom_redirect_submit' ).removeClass( 'disabled' );
				$( '.j4t3-spinner' ).css( "visibility", 'hidden' );

				// Redirect after update.
				location.reload();
			} );
		} );

		/**
		 * Set checkbox checked/not checked.
		 *
		 * @param object selecter Current selector element.
		 * @param mixed val Value.
		 */
		var jj4t3Check = function ( name, val ) {

			$( 'input[name=' + name + '][value=' + val + ']' ).prop( 'checked', true );
		}
	} );

})( jQuery );