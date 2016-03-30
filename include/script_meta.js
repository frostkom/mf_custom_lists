jQuery(function($)
{
	function check_condition(dom_obj)
	{
		var dom_value = dom_obj.val(),
			condition_type = dom_obj.attr('condition_type'),
			condition_field = dom_obj.attr('condition_field'),
			dom_field = $('#' + condition_field),
			dom_parent = dom_field.parents('.rwmb-field');

		if(condition_type == 'show_if_empty')
		{
			if(dom_value == '')
			{
				dom_parent.removeClass('hide');
			}

			else
			{
				dom_field.val('');
				dom_parent.addClass('hide');
			}
		}

		else if(condition_type == 'show_if_value')
		{
			if(dom_value == '')
			{
				dom_field.val('');
				dom_parent.addClass('hide');
			}

			else
			{
				dom_parent.removeClass('hide');
			}
		}
	}

	$('.rwmb-field input[condition_type], .rwmb-field select[condition_type]').each(function()
	{
		check_condition($(this));
	});

	$('.rwmb-field input[condition_type], .rwmb-field select[condition_type]').on('blur change', function()
	{
		check_condition($(this));
	});
});