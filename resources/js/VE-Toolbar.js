( function ( mw, $ ) {
	$( window ).scroll( function () {
		if ( $( '.ve-init-target-visual' ).length || $( '.ve-init-target-source' ).length ) {
			var $visualFloatingVE = $( '.ve-init-target-visual > .ve-ui-toolbar-floating' ),
				$sourceFloatingVE = $( '.ve-init-target-source > .ve-ui-toolbar-floating' );

			if ( $visualFloatingVE.length || $sourceFloatingVE.length ) {
				$( '.fixed-container' ).addClass( 've-fixed-btn' );
			} else {
				$( '.fixed-container' ).removeClass( 've-fixed-btn' );
			}
		}
	} );

}( mediaWiki, jQuery ) );
