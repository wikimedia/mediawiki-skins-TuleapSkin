( function ( mw, $ ) {
	$( document ).on( 'click', function ( e ) {
		var $dropdown, $parent,
			// eslint-disable-next-line no-jquery/no-global-selector
			$dropdownMenu = $( '.mw-tlp-skin-btn-dropdown-menu' ),
			target = e.target;

		// eslint-disable-next-line no-jquery/no-class-state
		if ( $( target ).hasClass( 'mw-tlp-dropdown-btn' ) ) {
			$dropdown = $( target.nextElementSibling );
			if ( $dropdown.length < 1 ) {
				$parent = target.offsetParent;
				$dropdown = $( $parent ).children().last();
			}

			// eslint-disable-next-line no-jquery/no-class-state
			if ( $dropdown.hasClass( 'dropdown-shown' ) ) {
				$dropdown.removeClass( 'dropdown-shown' );
			} else {
				// eslint-disable-next-line no-jquery/no-class-state
				if ( $dropdownMenu.hasClass( 'dropdown-shown' ) ) {
					$dropdownMenu.removeClass( 'dropdown-shown' );
				}
				$dropdown.addClass( 'dropdown-shown' );
			}
		} else {
			// eslint-disable-next-line no-jquery/no-class-state
			if ( $dropdownMenu.hasClass( 'dropdown-shown' ) ) {
				$dropdownMenu.removeClass( 'dropdown-shown' );
			}
		}
	} );
}( mediaWiki, jQuery ) );
