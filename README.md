# WordPress Like button
This plugin allows you to add a like button to posts.

The like counter will be stored as a post meta containing the user ID of the logged in user or a user hash. 
Guest user is unique based on the ip and browser.
A user can vote only once.


# Installation
1. Download the plugin
2. Activate the plugin
3. Add the like button to the single template with this filter:
    ```
    <?= apply_filters( 'add_page_likes_button', get_the_ID(), '' ) ?>
    ```
4. Load the like buttons on the overview page with javascript:
    ```javascript
    jQuery( '.archive .gallery-item' ).each( function () {
		    var item_url = $( 'a', this ).attr( 'href' ),
		         target   = '.gallery-item[data-id=' + $( this ).attr( 'data-id' ) + '] .title';
		    init_like_counter( item_url, target );
    } );

    ```
    