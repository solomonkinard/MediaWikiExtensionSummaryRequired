/**
 * Solomon Kinard
 */
$(document).ready(function() {
	// Ensure the summary field is updated before saving page
    $("#wpSave").click(function() {
        // check for empty summary
        if ($("#wpSummary").val().length == 0) {
	        // don't submit form
		    alert("Error:\n\nA summary is required before clicking \"Save Page\".");
	        return false;
        }
    });
});
