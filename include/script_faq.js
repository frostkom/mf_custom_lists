jQuery(function($)
{
	$(".widget.custom_list .custom_list_style_faq li h4").each(function()
	{
		var dom_obj = $(this);

		if(dom_obj.children(".fa").length == 0)
		{
			dom_obj.prepend("<i class='fa fa-plus fa-xs'></i>");
		}
	});

	$(document).on('click', ".widget.custom_list .custom_list_style_faq li h4", function(e)
	{
		$(this).siblings("p, ul").slideToggle();
        $(this).children(".fa").toggleClass('fa-plus fa-minus');

		e.preventDefault();
		return false;
	});
});