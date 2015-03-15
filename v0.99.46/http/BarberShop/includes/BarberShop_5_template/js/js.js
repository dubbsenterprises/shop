$(document).ready(function() {
	$("#commentForm").validate();

    /* Initiate BX Slider for main feature slider */
    $('.bxslider').bxSlider({
        auto: true,
        mode: 'fade',
        autoControls: false
    });

    /* Initiate BX Slider for testimonils slider */
    $('.testi').bxSlider({
        auto: true,
        //mode: 'fade',
        autoControls: false

    });

    /* Initiate lightbox Jquery */
    $("a[rel^='prettyPhoto']").prettyPhoto();

	$.ajax({
		type: "POST",
		url: "get-tweet.php",
		success: function(data) {
		    $("#twitter").html(data);
		}
	});
});