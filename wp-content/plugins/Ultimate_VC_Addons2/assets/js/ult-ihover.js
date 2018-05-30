(function(){
    jQuery(document).ready(function () {
 		ult_ihover_init();
		jQuery(document).ajaxComplete(function(e, xhr, settings){
			ult_ihover_init();
		});
		function ult_ihover_init() {
			jQuery('.ult-ih-list').each(function(){

                var Shape = jQuery(this).attr('data-shape');
                var Height  = jQuery(this).attr('data-height');
                var Width   = jQuery(this).attr('data-width');

                jQuery(this).find('li').each(function(){

                    // Shape
                    jQuery(this).find('.ult-ih-item').addClass('ult-ih-' + Shape);

                    //  Dimensions
                    jQuery(this).css({'height': Height,'width': Width});
                    jQuery(this).find('.ult-ih-item, .ult-ih-img').css({'height': Height,'width': Width});
                });
            });
		}
    });
})(jQuery);