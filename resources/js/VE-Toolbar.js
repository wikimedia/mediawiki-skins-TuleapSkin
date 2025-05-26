( function ( mw, $ ) {
	let $visualFloatingVE, $sourceFloatingVE;
	$( window ).on( 'scroll', () => {
		// eslint-disable-next-line no-jquery/no-global-selector
		if ( $( '.ve-init-target-visual' ).length || $( '.ve-init-target-source' ).length ) {
			// eslint-disable-next-line no-jquery/no-global-selector
			$visualFloatingVE = $( '.ve-init-target-visual > .ve-ui-toolbar-floating' );
			// eslint-disable-next-line no-jquery/no-global-selector
			$sourceFloatingVE = $( '.ve-init-target-source > .ve-ui-toolbar-floating' );

			if ( $visualFloatingVE.length || $sourceFloatingVE.length ) {
				// eslint-disable-next-line no-jquery/no-global-selector
				$( '.fixed-container' ).addClass( 've-fixed-btn' );
			} else {
				// eslint-disable-next-line no-jquery/no-global-selector
				$( '.fixed-container' ).removeClass( 've-fixed-btn' );
			}
		}
	} );

}( mediaWiki, jQuery ) );
