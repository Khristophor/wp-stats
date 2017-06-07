(function( $ ) {
	'use strict';

	function getStats() {
		$.ajax( {
			url: window.location.pathname + 'wp-json/cw/v1/stats',
		} )
			.done( function ( data ) {
				var countHtml = '';
				var popularHtml = '';
				$.each( data, function ( index, stat ) {
					if( stat.type === 'count' ) {
						countHtml += '<p><strong>' + stat.label + ':</strong> ' + stat.value + '</p>' + "\n";
					}
					if( stat.type === 'popular' ) {
						popularHtml += '<p><strong>' + stat.label + ':</strong> <a href="' + stat.url + '">' + stat.value + '</a></p>' + "\n";
						if( stat.tagline.length > 0 ) {
							popularHtml += '<p class="aside">' + stat.tagline + '</p>';
						}
					}
				} );
				if( countHtml.length > 0 ) {
					$( '.stat-count' ).html( countHtml );
				}
				if( popularHtml.length > 0 ) {
					$( '.stat-popular' ).html( popularHtml );
				}
			} );
	}

	$( window ).on( 'load', function() {
		window.setInterval( getStats, 5000 );
	});

})( jQuery );
