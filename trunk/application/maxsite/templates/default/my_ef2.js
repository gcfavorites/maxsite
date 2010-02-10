
jQuery(document).ready(function($)
{
	$('#sub-container a').hover(
		function()
		{
			$(this).effect("highlight", { color: "#86A7CA" }, 800);
		}
	);

})(jQuery);