var root_path_url = "http";
jQuery(document).ready(function($) { 
	root_path_url = $('link[rel="start"]').attr("href") + "/";
});

function is_touch_device() {
  return !!('ontouchstart' in window);
}


/***************************************************
	     TOGGLE STYLE
***************************************************/
jQuery(document).ready(function($) {
								
	$(".toggle-container").hide(); 
	$(".trigger").toggle(function(){
		$(this).addClass("active");
		}, function () {
		$(this).removeClass("active");
	});
	$(".trigger").click(function(){
		$(this).next(".toggle-container").slideToggle();
	});
});

/***************************************************
	     ACCORDION
***************************************************/
jQuery(document).ready(function($){	
	$('.trigger-button').click(function() {
		$(".trigger-button").removeClass("active")
	 	$('.accordion').slideUp('normal');
		if($(this).next().is(':hidden') == true) {
			$(this).next().slideDown('normal');
			$(this).addClass("active");
		 } 
	 });
	$('.accordion').hide();
});


/***************************************************
	  Check path
***************************************************/

jQuery(document).ready(function($){
	
	$('.check_path').each(function(){
		var icon_path = $(this).attr("src");
		if (icon_path.substr(0, 10) == "wp-content") {
			$(this).attr("src", root_path_url + ""+ icon_path);
		}
	});
});




