(function()
{
	var el = wp.element.createElement,
		registerBlockType = wp.blocks.registerBlockType,
		SelectControl = wp.components.SelectControl,
		TextControl = wp.components.TextControl;

	registerBlockType('mf/customlists',
	{
		title: script_custom_lists_block_wp.block_title,
		description: script_custom_lists_block_wp.block_description,
		icon: 'format-gallery',
		category: 'widgets',
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
			},
			"__experimentalBorder":
			{
				"radius": true
			}
		},
		edit: function(props)
		{
			return el(
				'div',
				{className: 'wp_mf_block_container'},
				[
					el(
						InspectorControls,
						'div',
						el(
							TextControl,
							{
								label: script_custom_lists_block_wp.list_heading_label,
								type: 'text',
								value: props.attributes.list_heading,
								onChange: function(value)
								{
									props.setAttributes({list_heading: value});
								}
							}
						),
						el(
							TextControl,
							{
								label: script_custom_lists_block_wp.list_content_label,
								type: 'text',
								value: props.attributes.list_content,
								onChange: function(value)
								{
									props.setAttributes({list_content: value});
								}
							}
						),
						el(
							SelectControl,
							{
								label: script_custom_lists_block_wp.list_id_label,
								value: props.attributes.list_id,
								options: convert_php_array_to_block_js(script_custom_lists_block_wp.list_id),
								onChange: function(value)
								{
									props.setAttributes({list_id: value});
								}
							}
						),
						el(
							TextControl,
							{
								label: script_custom_lists_block_wp.list_amount_label,
								type: 'number',
								value: props.attributes.list_amount,
								onChange: function(value)
								{
									props.setAttributes({list_amount: value});
								}
							}
						),
						el(
							SelectControl,
							{
								label: script_custom_lists_block_wp.list_order_label,
								value: props.attributes.list_order,
								options: convert_php_array_to_block_js(script_custom_lists_block_wp.list_order),
								onChange: function(value)
								{
									props.setAttributes({list_order: value});
								}
							}
						)
					),
					el(
						'strong',
						{className: props.className},
						script_custom_lists_block_wp.block_title
					)
				]
			);
		},
		save: function()
		{
			return null;
		}
	});
})();