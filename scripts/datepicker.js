// Enable the SG Start Date datepicker widget
(function ($) {
$(document).ready(function () {

$('#sg_start_date').datepicker({
	// Restrict start date to current date or any date thereafter (a start date
	// cannot be in the past)
	minDate: 0
});

});
}( window.jQuery ));
