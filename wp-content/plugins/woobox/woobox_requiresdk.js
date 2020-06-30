// adds woobox sdk just after opening body tag (if not already added)
jQuery(document).ready(function($) {
	if($("#woobox-root").length > 0) return; // already exists
	if($(".woobox-offer").length === 0) return; // not being used on this post/page
	sdk = "<div id='woobox-root'></div>";
	sdk += "<script>(function(d, s, id) {";
	sdk += "  var js, fjs = d.getElementsByTagName(s)[0];";
	sdk += "  if (d.getElementById(id)) return;";
	sdk += "  js = d.createElement(s); js.id = id;";
	sdk += "  js.src = '//cdn.woobox.com/js/plugins/woo.js?v=1.3';";
	sdk += "  fjs.parentNode.insertBefore(js, fjs);";
	sdk += "}(document, 'script', 'woobox-sdk'));";
	$("body").prepend(sdk);
});
		
