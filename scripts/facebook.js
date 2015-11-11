// Connect to Facebook API for use with Like button
(function ($) {
$(document).ready(function () {

// Inject API scripts if Facebook Like button container is present on page
if ( 0 !== $( '.fb-like' ).length ) {
	$( 'body' ).before( '<div id="fb-root"></div>' );
	(function(d, s, id) {
		var js, fjs = d.getElementsByTagName(s)[0];
		if (d.getElementById(id)) {
			return;
		}
		js = d.createElement(s);
		js.id = id;
		js.src = 'http://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.4';
		fjs.parentNode.insertBefore(js, fjs);
	}( document, 'script', 'facebook-jssdk' ));
}

});
}( window.jQuery ));
