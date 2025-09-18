jQuery(function($)
{
	$(document).on('change', ".widget.custom_list select", function()
	{
		var dom_obj = $(this),
			parent_id = dom_obj.val(),
			dom_obj_widget = dom_obj.parents(".widget.custom_list"),
			dom_obj_list = dom_obj_widget.find("ul");

		dom_obj_list.children("li").removeClass('hide');

		if(parent_id > 0)
		{
			dom_obj_list.children("li:not(.parent_" + parent_id + ")").addClass('hide');
		}
	});
});