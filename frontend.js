
// jQuery Ready State
jQuery( document ).ready( function( $ ) {

} );

// Validate Email Address String
function wpbme_is_email( email ) {
	var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	return regex.test( email );
}
