/*
 * Main JS file for ZF application
 */

$(document).ready( function() {

	/*
	 * Site menu
	 */
	$( ".menu .dropdown" ).click( function() {
		$( "ul", this ).toggleClass( "active" );
		$( this ).toggleClass( "active" );
	});

});
