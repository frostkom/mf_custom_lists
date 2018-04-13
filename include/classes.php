<?php

class mf_custom_list
{
	function __construct()
	{
		$this->meta_prefix = "mf_custom_lists_";
	}

	function wp_head()
	{
		mf_enqueue_style('style_custom_lists', plugin_dir_url(__FILE__)."style.css", get_plugin_version(__FILE__));
	}

	function delete_post($post_id)
	{
		global $wpdb, $post_type;

		if($post_type == 'mf_custom_lists')
		{
			$result = $wpdb->get_results($wpdb->prepare("SELECT ID FROM ".$wpdb->posts." INNER JOIN ".$wpdb->postmeta." ON ".$wpdb->posts.".ID = ".$wpdb->postmeta.".post_id WHERE post_type = 'mf_custom_item' AND meta_key = '".$this->meta_prefix."list_id' AND meta_value = '%d'", $post_id));

			foreach($result as $r)
			{
				wp_trash_post($r->ID);
			}
		}
	}

	function render_shortcode($atts)
	{
		global $wpdb;

		extract(shortcode_atts(array(
			'id' => '',
			'amount' => 0,
			'order' => '',
		), $atts));

		$out = "";

		$result = $wpdb->get_results($wpdb->prepare("SELECT ID, post_name, post_excerpt, post_content FROM ".$wpdb->posts." WHERE post_type = 'mf_custom_lists' AND post_status = 'publish' AND ID = '%d'", $id));

		foreach($result as $r)
		{
			$parent_id = $r->ID;
			$post_name = $r->post_name;
			$parent_content = $r->post_content;
			$parent_excerpt = $r->post_excerpt;

			$parent_container = get_post_meta($parent_id, $this->meta_prefix.'container', true);
			$parent_items = get_post_meta($parent_id, $this->meta_prefix.'items', true);

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
				$child_order = get_post_meta($parent_id, $this->meta_prefix.'order', true);

				if($order != '')
				{
					$child_order = $order;
				}

				$out_children = "";

				switch($child_order)
				{
					default:
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

				$result2 = $wpdb->get_results($wpdb->prepare("SELECT ID, post_content FROM ".$wpdb->posts." INNER JOIN ".$wpdb->postmeta." ON ".$wpdb->posts.".ID = ".$wpdb->postmeta.".post_id AND meta_key = '".$this->meta_prefix."list_id' WHERE post_type = 'mf_custom_item' AND post_status = 'publish' AND meta_value = '%d'".$query_order." LIMIT 0, %d", $parent_id, ($amount > 0 ? $amount : 100)));

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

					$child_content = str_replace("<p>[list_text]</p>", "[list_text]", $child_content); //When apply_filters() on list_text was added, this had to be corrected

					$out_children .= preg_replace_callback(
						"/\[(.*?)\]/i",
						function($match) use ($child_id)
						{
							global $wpdb;

							$out = "";

							if($match[1] == "list_title")
							{
								$child_title = get_the_title($child_id);

								$out .= $child_title;
							}

							else if($match[1] == "list_text")
							{
								$child_text = $wpdb->get_var($wpdb->prepare("SELECT post_content FROM ".$wpdb->posts." WHERE post_status = 'publish' AND ID = '%d'", $child_id));

								$out .= apply_filters('the_content', $child_text);
							}

							else if($match[1] == "list_image")
							{
								$child_image_id = get_post_meta($child_id, $this->meta_prefix.'image', true);

								if($child_image_id > 0)
								{
									$out .= "<div class='image'>".render_image_tag(array('id' => $child_image_id))."</div>";
								}
							}

							else if($match[1] == "list_link")
							{
								$child_page = get_post_meta($child_id, $this->meta_prefix.'page', true);

								if(intval($child_page) > 0)
								{
									$out .= get_permalink($child_page);
								}

								else
								{
									$child_link = get_post_meta($child_id, $this->meta_prefix.'link', true);

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

		$this->arr_default = array(
			'list_heading' => '',
			'list_id' => '',
			'list_amount' => 0,
			'list_order' => 'numerical',
		);

		parent::__construct('custom-list-widget', __("Custom List", 'lang_custom_lists'), $widget_ops);
	}

	function widget($args, $instance)
	{
		extract($args);

		$instance = wp_parse_args((array)$instance, $this->arr_default);

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

				echo "<div class='section'>"
					.$obj_custom_list->render_shortcode(array('id' => $instance['list_id'], 'amount' => $instance['list_amount'], 'order' => $instance['list_order']))
				."</div>"
			.$after_widget;
		}
	}

	function update($new_instance, $old_instance)
	{
		$instance = $old_instance;

		$new_instance = wp_parse_args((array)$new_instance, $this->arr_default);

		$instance['list_heading'] = sanitize_text_field($new_instance['list_heading']);
		$instance['list_id'] = sanitize_text_field($new_instance['list_id']);
		$instance['list_amount'] = sanitize_text_field($new_instance['list_amount']);
		$instance['list_order'] = sanitize_text_field($new_instance['list_order']);

		return $instance;
	}

	function form($instance)
	{
		$instance = wp_parse_args((array)$instance, $this->arr_default);

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