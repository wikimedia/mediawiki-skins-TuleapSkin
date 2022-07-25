( function ( mw, $ ) {
	$( document ).click( function ( e ) {
		var $dropdown, $parent,
			$dropdownMenu = $( '.mw-tlp-skin-btn-dropdown-menu' );
		if ( $( e.target ).hasClass( 'mw-tlp-dropdown-btn' ) ) {
			$dropdown = $( e.target.nextElementSibling );
			if ( $dropdown.length < 1 ) {
				$parent = $( e.target.offsetParent );
				$dropdown = $( $parent ).children().last();
			}

			if ( $dropdown.hasClass( 'dropdown-shown' ) ) {
				$dropdown.removeClass( 'dropdown-shown' );
			} else {
				if ( $dropdownMenu.hasClass( 'dropdown-shown' ) ) {
					$dropdownMenu.removeClass( 'dropdown-shown' );
				}
				$dropdown.addClass( 'dropdown-shown' );
			}
		} else {
			if ( $dropdownMenu.hasClass( 'dropdown-shown' ) ) {
				$dropdownMenu.removeClass( 'dropdown-shown' );
			}
		}
	} );
}( mediaWiki, jQuery ) );
