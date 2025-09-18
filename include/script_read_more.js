jQuery(function($)
{
	jQuery.fn.shorten = function(options)
	{
		var settings = jQuery.extend(
		{
			'ellipsis': "&hellip;",
			'showChars': 255,
			'moreText': script_custom_lists.read_more
		}, options);

		return this.each(function()
		{
			var self = jQuery(this),
				text_start = self.text().slice(0, settings.showChars),
				text_end = self.text().slice(settings.showChars);

			if(text_end.length > 0)
			{
				self.addClass('shorten-shortened').html(text_start + "<span class='shorten-clipped hide'>" + text_end + "</span><span class='shorten-ellipsis form_button wp-block-button'>" + settings.ellipsis + "<div><a href='#' class='shorten-more-link wp-block-button__link'>" + settings.moreText + "</a></div></span>");
			}
		});
	};

	$(".widget.custom_list ul.custom_list_read_more li .text").shorten({'showChars': 100});

	$(document).on('click', ".shorten-more-link", function(e)
	{
		var dom_ellipsis = jQuery(e.currentTarget).parents(".shorten-ellipsis");

		dom_ellipsis.addClass('hide').siblings(".shorten-clipped").removeClass('hide').animate(
		{
			opacity: 1
		}, 500, function()
		{
			dom_ellipsis.parent(".shorten-shortened").removeClass('shorten-shortened');
		});

		return false;
	});
});