// jQuery.noConflict();
jQuery(document).ready(function($)
{

	// если нужно по hover
	// $('ul.is_link > li').hover(
	
	$('#sub-container a').hover(
		function()
		{
			//$(this).effect("bounce", { direction: "right", distance: 30, times: 2 }, 200);
			//$(this).effect("shake", { direction: "up", distance: 3, times: 1 }, 100);
			$(this).effect("highlight", { color: "#86A7CA" }, 800);
		}
	);

})(jQuery);