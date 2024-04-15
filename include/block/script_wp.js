(function()
{
	var __ = wp.i18n.__,
		el = wp.element.createElement,
		registerBlockType = wp.blocks.registerBlockType,
		SelectControl = wp.components.SelectControl,
		TextControl = wp.components.TextControl;

	registerBlockType('mf/customlists',
	{
		title: __("Custom List", 'lang_custom_lists'),
		description: __("Display a Custom List", 'lang_custom_lists'),
		icon: 'format-gallery', /* https://developer.wordpress.org/resource/dashicons/ */
		category: 'widgets', /* common, formatting, layout, widgets, embed */
		'attributes':
		{
			'align':
			{
				'type': 'string',
				'default': ''
			},
			'list_heading':
			{
                'type': 'string',
                'default': ''
            },
			'list_content':
			{
                'type': 'string',
                'default': ''
            },
			'list_id':
			{
                'type': 'string',
                'default': ''
            },
			'list_amount':
			{
                'type': 'string',
                'default': ''
            },
			'list_order':
			{
                'type': 'string',
                'default': ''
            }
		},
		'supports':
		{
			'html': false,
			'multiple': false,
			'align': true,
			'spacing':
			{
				'margin': true,
				'padding': true
			},
			'color':
			{
				'background': true,
				'gradients': false,
				'text': true
			},
			'defaultStylePicker': true,
			'typography':
			{
				'fontSize': true,
				'lineHeight': true
			}
		},
		edit: function(props)
		{
			var arr_out = [];

			/* Text */
			/* ################### */
			arr_out.push(el(
				'div',
				{className: "wp_mf_block " + props.className},
				el(
					TextControl,
					{
						label: __("Heading", 'lang_custom_lists'),
						type: 'text',
						value: props.attributes.list_heading,
						/*help: __("Description...", 'lang_custom_lists'),*/
						onChange: function(value)
						{
							props.setAttributes({list_heading: value});
						}
					}
				)
			));
			/* ################### */

			/* Text */
			/* ################### */
			arr_out.push(el(
				'div',
				{className: "wp_mf_block " + props.className},
				el(
					TextControl,
					{
						label: __("Heading", 'lang_custom_lists'),
						type: 'text',
						value: props.attributes.list_content,
						/*help: __("Description...", 'lang_custom_lists'),*/
						onChange: function(value)
						{
							props.setAttributes({list_content: value});
						}
					}
				)
			));
			/* ################### */

			/* Select */
			/* ################### */
			var arr_options = [];

			jQuery.each(script_custom_lists_block_wp.list_id, function(index, value)
			{
				if(index == "")
				{
					index = 0;
				}

				arr_options.push({label: value, value: index});
			});

			arr_out.push(el(
				'div',
				{className: "wp_mf_block " + props.className},
				el(
					SelectControl,
					{
						label: __("List", 'lang_custom_lists'),
						value: props.attributes.list_id,
						options: arr_options,
						onChange: function(value)
						{
							props.setAttributes({list_id: value});
						}
					}
				)
			));
			/* ################### */

			/* Number */
			/* ################### */
			arr_out.push(el(
				'div',
				{className: "wp_mf_block " + props.className},
				el(
					TextControl,
					{
						label: __("Amount", 'lang_custom_lists'),
						type: 'number',
						value: props.attributes.list_amount,
						onChange: function(value)
						{
							props.setAttributes({list_amount: value});
						}
					}
				)
			));
			/* ################### */

			/* Select */
			/* ################### */
			var arr_options = [];

			jQuery.each(script_custom_lists_block_wp.list_order, function(index, value)
			{
				if(index == "")
				{
					index = 0;
				}

				arr_options.push({label: value, value: index});
			});

			arr_out.push(el(
				'div',
				{className: "wp_mf_block " + props.className},
				el(
					SelectControl,
					{
						label: __("Order", 'lang_custom_lists'),
						value: props.attributes.list_order,
						options: arr_options,
						onChange: function(value)
						{
							props.setAttributes({list_order: value});
						}
					}
				)
			));
			/* ################### */

			return arr_out;
		},

		save: function()
		{
			return null;
		}
	});
})();