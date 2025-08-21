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
				self.addClass('shorten-shortened').html(text_start + "<span class='shorten-clipped hide'>" + text_end + "</span><span class='shorten-ellipsis form_button wp-block-button'>" + settings.ellipsis + "<a href='#' class='shorten-more-link'>" + settings.moreText + settings.ellipsis + "</a></span>");
			}
		});
	};

	$(".custom_list.custom_list_read_more li p:last-of-type").shorten({'showChars': 100});
});