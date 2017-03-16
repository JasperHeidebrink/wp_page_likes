jQuery( document ).ready( function ( $ ) {

	$( ".page_like_button" ).click( function () {

		$.ajax( {
			type     : 'POST',
			url      : DgPageLikes.ajaxUrl,
			dataType : 'json',
			data     : {
				action  : 'dg_like',
				nonce   : DgPageLikes.ajaxNonce,
				post_id : $( this ).attr( 'data-page' ),
				target  : $( this ).attr( 'id' )
			},
			success  : function ( result ) {
				if ( undefined === result.data || undefined === result.data.message ) {
					return;
				}
				/*
				 * Update the counter
				 */
				$( '#' + result.data.target + ' .page_like_counter' ).html( result.data.votes );
				alert( result.data.message );
			}
		} );
	} );

	/**
	 * Loading the gallery like counters
	 *
	 * @param item_url
	 * @param target
	 */
	init_like_counter = function ( item_url, target ) {

		$.ajax( {
			type     : 'POST',
			url      : DgPageLikes.ajaxUrl,
			dataType : 'json',
			data     : {
				action   : 'dg_get_like_button',
				nonce    : DgPageLikes.ajaxNonce,
				item_url : item_url,
				target   : target
			},
			success  : function ( result ) {
				console.log( result );

				if ( undefined === result.data.html ) {
					return;
				}

				/*
				 * Update the counter
				 */
				$( result.data.html ).insertAfter( result.data.target );
			}
		} );
	};
} );
