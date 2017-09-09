<?php

class mf_custom_list
{
	function __construct(){}

	function render_shortcode($atts)
	{
		global $wpdb, $meta_prefix_cl;

		extract(shortcode_atts(array(
			'id' => '',
			'amount' => 0,
			'order' => '',
		), $atts));

		$out = "";

		$meta_prefix_cl = "mf_custom_lists_";

		$result = $wpdb->get_results($wpdb->prepare("SELECT ID, post_name, post_excerpt, post_content FROM ".$wpdb->posts." WHERE post_type = 'mf_custom_lists' AND post_status = 'publish' AND ID = '%d'", $id));

		foreach($result as $r)
		{
			$parent_id = $r->ID;
			$post_name = $r->post_name;
			$parent_content = $r->post_content;
			$parent_excerpt = $r->post_excerpt;

			$parent_container = get_post_meta($parent_id, $meta_prefix_cl.'container', true);
			$parent_items = get_post_meta($parent_id, $meta_prefix_cl.'items', true);

			if($parent_container == '')
			{
				$parent_container = $parent_content;
			}

			if($parent_items == '')
			{
				$parent_items = $parent_excerpt;
			}

			if(preg_match("/\[children\]/i", $parent_container))
			{
				$child_order = get_post_meta($parent_id, $meta_prefix_cl.'order', true);

				if($order != '')
				{
					$child_order = $order;
				}

				$out_children = "";

				switch($child_order)
				{
					case 'alphabetic':
						$query_order = " ORDER BY post_title ASC";
					break;

					case 'numerical':
						$query_order = " ORDER BY menu_order ASC";
					break;

					case 'random':
						$query_order = " ORDER BY RAND()";
					break;
				}

				$result2 = $wpdb->get_results($wpdb->prepare("SELECT ID, post_content FROM ".$wpdb->posts." INNER JOIN ".$wpdb->postmeta." ON ".$wpdb->posts.".ID = ".$wpdb->postmeta.".post_id AND meta_key = '".$meta_prefix_cl."list_id' WHERE post_type = 'mf_custom_item' AND post_status = 'publish' AND meta_value = '%d'".$query_order." LIMIT 0, %d", $parent_id, ($amount > 0 ? $amount : 100)));

				foreach($result2 as $r)
				{
					$child_id = $r->ID;

					if($parent_items != '')
					{
						$child_content = $parent_items;
					}

					else
					{
						$child_content = $r->post_content;
					}

					$out_children .= preg_replace_callback(
						"/\[(.*?)\]/i",
						function($match) use ($child_id)
						{
							global $wpdb, $meta_prefix_cl;

							$out = "";

							if($match[1] == "list_title")
							{
								$child_title = get_the_title($child_id);

								$out .= $child_title;
							}

							else if($match[1] == "list_text")
							{
								$child_text = $wpdb->get_var($wpdb->prepare("SELECT post_content FROM ".$wpdb->posts." WHERE post_status = 'publish' AND ID = '%d'", $child_id));

								$out .= $child_text;
							}

							else if($match[1] == "list_image")
							{
								$child_image_id = get_post_meta($child_id, $meta_prefix_cl.'image', true);

								if($child_image_id > 0)
								{
									$out .= "<div class='image'>".render_image_tag(array('id' => $child_image_id))."</div>";
								}
							}

							else if($match[1] == "list_link")
							{
								$child_page = get_post_meta($child_id, $meta_prefix_cl.'page', true);

								if(intval($child_page) > 0)
								{
									$out .= get_permalink($child_page);
								}

								else
								{
									$child_link = get_post_meta($child_id, $meta_prefix_cl.'link', true);

									if($child_link == '')
									{
										$child_link = "#";
									}

									$out .= $child_link;
								}
							}

							else
							{
								$out .= get_post_meta($child_id, $match[1], true);
							}

							return $out;
						},
						$child_content
					);
				}

				$out .= str_replace("[children]", $out_children, $parent_container);
			}

			else
			{
				$out .= $parent_container;
			}

			$out = str_replace("[parent_class]", " class='custom_list custom_list_".$post_name."'", $out);
		}

		//return apply_filters('the_content', $out);
		return $out;
	}
}

class widget_custom_lists extends WP_Widget
{
	function __construct()
	{
		$widget_ops = array(
			'classname' => 'custom_list',
			'description' => __("Display a custom list that you have created", 'lang_custom_lists')
		);

		$control_ops = array('id_base' => 'custom-list-widget');

		parent::__construct('custom-list-widget', __("Custom List", 'lang_custom_lists'), $widget_ops, $control_ops);
	}

	function widget($args, $instance)
	{
		global $wpdb;

		extract($args);

		if($instance['list_id'] > 0)
		{
			echo $before_widget;

				if($instance['list_heading'] != '')
				{
					echo $before_title
						.$instance['list_heading']
					.$after_title;
				}

				$obj_custom_list = new mf_custom_list();

				echo $obj_custom_list->render_shortcode(array('id' => $instance['list_id'], 'amount' => $instance['list_amount'], 'order' => $instance['list_order']));

			echo $after_widget;
		}
	}

	function update($new_instance, $old_instance)
	{
		$instance = $old_instance;

		$instance['list_heading'] = strip_tags($new_instance['list_heading']);
		$instance['list_id'] = strip_tags($new_instance['list_id']);
		$instance['list_amount'] = strip_tags($new_instance['list_amount']);
		$instance['list_order'] = strip_tags($new_instance['list_order']);

		return $instance;
	}

	function form($instance)
	{
		global $wpdb;

		$defaults = array(
			'list_heading' => '',
			'list_id' => '',
			'list_amount' => 0,
			'list_order' => 'numerical',
		);
		$instance = wp_parse_args((array)$instance, $defaults);

		echo "<div class='mf_form'>"
			.show_textfield(array('name' => $this->get_field_name('list_heading'), 'text' => __("Heading", 'lang_custom_lists'), 'value' => $instance['list_heading']))
			.show_select(array('data' => get_posts_for_select(array('post_type' => "mf_custom_lists")), 'name' => $this->get_field_name('list_id'), 'text' => __("List", 'lang_custom_lists'), 'value' => $instance['list_id']))
			."<div class='flex_flow'>"
				.show_textfield(array('type' => 'number', 'name' => $this->get_field_name('list_amount'), 'text' => __("Amount", 'lang_custom_lists'), 'value' => $instance['list_amount']))
				.show_select(array('data' => get_order_for_select(), 'name' => $this->get_field_name('list_order'), 'text' => __("Order", 'lang_custom_lists'), 'value' => $instance['list_order']))
			."</div>
		</div>";
	}
}