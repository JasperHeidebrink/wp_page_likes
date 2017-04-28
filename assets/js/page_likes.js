jQuery( document ).ready( function ( $ ) {

	/*
	 * Default storing the of a like
	 */
	$( '.page_like_button' ).click( function ( event ) {

		event.preventDefault();

		trigger_like( $( this ).attr( 'data-page' ), $( this ).attr( 'id' ) );
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
				action   : 'dg_get_like_counter',
				nonce    : DgPageLikes.ajaxNonce,
				item_url : item_url,
				target   : encodeURIComponent( target )
			},
			success  : function ( result ) {
				if ( undefined === result.data.html ) {
					return;
				}

				/*
				 * Update the counter
				 */
				$( result.data.html ).insertAfter( decodeURIComponent( result.data.target ) );
			}
		} );
	};

	/**
	 * Adding like buttons to images
	 *
	 * @param item_url
	 * @param target
	 */
	add_like_button = function ( item_url, target ) {

		$.ajax( {
			type     : 'POST',
			url      : DgPageLikes.ajaxUrl,
			dataType : 'json',
			data     : {
				action   : 'dg_get_like_button',
				nonce    : DgPageLikes.ajaxNonce,
				item_url : item_url,
				target   : encodeURIComponent( target )
			},
			success  : function ( result ) {
				if ( undefined === result.data.html ) {
					return;
				}

				/*
				 * Add the button
				 */
				$( decodeURIComponent( result.data.target ) ).html( result.data.html );
			}
		} );
	};

	/*
	 * Catching the like trigger
	 */
	$( document ).on( 'click', function ( event ) {
		var item = $( '#' + event.target.id );
		if ( item.hasClass( 'page_like_button' ) ) {
			console.log( event.target );
			trigger_like( item.attr( 'data-page' ), item.attr( 'id' ) );
		}

	} );

	/**
	 * Storing the like and update the counter
	 *
	 * @param post_id
	 * @param target
	 */
	function trigger_like( post_id, target ) {
		$.ajax( {
			type     : 'POST',
			url      : DgPageLikes.ajaxUrl,
			dataType : 'json',
			data     : {
				action  : 'dg_like',
				nonce   : DgPageLikes.ajaxNonce,
				post_id : post_id,
				target  : target
			},
			success  : function ( result ) {
				console.log( result );
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
	};

} );
