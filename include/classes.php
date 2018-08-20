<?php

class mf_custom_list
{
	function __construct()
	{
		$this->meta_prefix = "mf_custom_lists_";
	}

	function get_order_for_select()
	{
		return array(
			'numerical' => __("Numerical", 'lang_custom_lists'),
			'alphabetic' => __("Alphabetical", 'lang_custom_lists'),
			'random' => __("Random", 'lang_custom_lists'),
		);
	}
	
	function get_styles_for_select()
	{
		return array(
			'' => "-- ".__("Choose Here", 'lang_custom_lists')." --",
			'about_us' => __("About Us", 'lang_custom_lists'),
			'flags' => __("Flags", 'lang_custom_lists'),
			'horizontal' => __("Horizontal", 'lang_custom_lists'),
			'logos' => __("Logos", 'lang_custom_lists'),
			'one_col' => __("One Column", 'lang_custom_lists'),
			'people' => __("People", 'lang_custom_lists'),
			'screenshots' => __("Screenshots", 'lang_custom_lists'),
			'vertical' => __("Vertical", 'lang_custom_lists'),
		);
	}

	function init()
	{
		$labels = array(
			'name' => _x(__("Custom Lists", 'lang_custom_lists'), 'post type general name'),
			'singular_name' => _x(__("Custom List", 'lang_custom_lists'), 'post type singular name'),
			'menu_name' => __("Custom Lists", 'lang_custom_lists')
		);

		$args = array(
			'labels' => $labels,
			'public' => true,
			'show_ui' => true,
			'show_in_menu' => false,
			'show_in_nav_menus' => false,
			'exclude_from_search' => true,
			'supports' => array('title'),
			'hierarchical' => false,
			'has_archive' => false,
		);

		register_post_type('mf_custom_lists', $args);

		$labels = array(
			'name' => _x(__("Items", 'lang_custom_lists'), 'post type general name'),
			'singular_name' => _x(__("Item", 'lang_custom_lists'), 'post type singular name'),
			'menu_name' => __("Items", 'lang_custom_lists')
		);

		$args = array(
			'labels' => $labels,
			'public' => false,
			'show_ui' => true,
			'show_in_menu' => false,
			'show_in_nav_menus' => false,
			'exclude_from_search' => true,
			'supports' => array('title', 'editor', 'custom-fields'),
			'hierarchical' => true,
			'has_archive' => false,
		);

		register_post_type('mf_custom_item', $args);
	}

	function admin_menu()
	{
		$menu_start = "edit.php?post_type=mf_custom_lists";
		$menu_capability = override_capability(array('page' => $menu_start, 'default' => 'edit_pages'));

		$menu_title = __("Custom Lists", 'lang_custom_lists');
		add_menu_page("", $menu_title, $menu_capability, $menu_start, '', 'dashicons-list-view', 21);

		$menu_title = __("Lists", 'lang_custom_lists');
		add_submenu_page($menu_start, $menu_title, $menu_title, $menu_capability, $menu_start);

		$arr_data = array();
		get_post_children(array('post_type' => 'mf_custom_lists'), $arr_data);

		if(count($arr_data) > 0)
		{
			$menu_title = __("Items", 'lang_custom_lists');
			add_submenu_page($menu_start, $menu_title, $menu_title, $menu_capability, "edit.php?post_type=mf_custom_item");

			$menu_title = __("Add New", 'lang_custom_lists');
			add_submenu_page($menu_start, $menu_title, $menu_title, $menu_capability, "post-new.php?post_type=mf_custom_item");
		}
	}

	function meta_boxes($meta_boxes)
	{
		global $wpdb;

		$meta_boxes[] = array(
			'id' => 'structure',
			'title' => __("Structure", 'lang_custom_lists'),
			'post_types' => array('mf_custom_lists'),
			//'context' => 'side',
			'priority' => 'low',
			'fields' => array(
				array(
					'name' => __("Container", 'lang_custom_lists'),
					'id' => $this->meta_prefix.'container',
					'type' => 'text',
					'std' => "<ul[parent_class]>[children]</ul>",
				),
				array(
					'name' => __("Items", 'lang_custom_lists'),
					'id' => $this->meta_prefix.'items',
					'type' => 'textarea',
					'std' => "<li><h2><a href='[list_link]'>[list_title]</a></h2>[list_image][list_text]</li>",
				),
				array(
					'name' => __("Style", 'lang_custom_lists'),
					'id' => $this->meta_prefix.'style',
					'type' => 'select',
					'options' => $this->get_styles_for_select(),
				),
				array(
					'name' => __("Custom Style", 'lang_custom_lists'),
					'id' => $this->meta_prefix.'custom_style',
					'type' => 'textarea',
					'std' => "",
				),
			)
		);

		$meta_boxes[] = array(
			'id' => 'settings',
			'title' => __("Settings", 'lang_custom_lists'),
			'post_types' => array('mf_custom_lists'),
			'context' => 'side',
			'priority' => 'low',
			'fields' => array(
				array(
					'name' => __("Order", 'lang_custom_lists'),
					'id' => $this->meta_prefix.'order',
					'type' => 'select',
					'options' => $this->get_order_for_select(),
					'std' => 'numerical',
				),
				array(
					'name' => __("Read More", 'lang_custom_lists'),
					'id' => $this->meta_prefix.'read_more',
					'type' => 'select',
					'options' => get_yes_no_for_select(),
					'std' => 'no',
				),
				array(
					'name' => __("Columns", 'lang_custom_lists')." (".__("Desktop", 'lang_custom_lists').")",
					'id' => $this->meta_prefix.'columns_desktop',
					'type' => 'number',
					'attributes' => array(
						'min' => 1,
						'max' => 4,
					),
				),
				array(
					'name' => __("Columns", 'lang_custom_lists')." (".__("Tablet", 'lang_custom_lists').")",
					'id' => $this->meta_prefix.'columns_tablet',
					'type' => 'number',
					'attributes' => array(
						'min' => 1,
						'max' => 3,
					),
				),
				array(
					'name' => __("Columns", 'lang_custom_lists')." (".__("Mobile", 'lang_custom_lists').")",
					'id' => $this->meta_prefix.'columns_mobile',
					'type' => 'number',
					'attributes' => array(
						'min' => 1,
						'max' => 2,
					),
				),
			)
		);

		$default_list_id = '';

		if($default_list_id == '')
		{
			$default_list_id = check_var('list_id', 'int');
		}

		if($default_list_id == '')
		{
			$default_list_id = $wpdb->get_var($wpdb->prepare("SELECT meta_value FROM ".$wpdb->postmeta." WHERE meta_key = %s ORDER BY meta_id DESC LIMIT 0, 1", $this->meta_prefix.'list_id'));
		}

		if($default_list_id == '')
		{
			$default_list_id = $wpdb->get_var($wpdb->prepare("SELECT ID FROM ".$wpdb->posts." WHERE post_type = %s AND post_status = %s ORDER BY post_modified DESC LIMIT 0, 1", 'mf_custom_lists', 'publish'));
		}

		$meta_boxes[] = array(
			'id' => 'settings',
			'title' => __("Settings", 'lang_custom_lists'),
			'post_types' => array('mf_custom_item'),
			'context' => 'side',
			'priority' => 'low',
			'fields' => array(
				array(
					'name' => __("List", 'lang_custom_lists'),
					'id' => $this->meta_prefix.'list_id',
					'type' => 'select',
					'options' => get_posts_for_select(array('post_type' => "mf_custom_lists")),
					'std' => $default_list_id,
				),
				array(
					'name' => __("Image", 'lang_custom_lists'),
					'id' => $this->meta_prefix.'image',
					'type' => 'file_advanced',
				),
				array(
					'name' => __("Page", 'lang_custom_lists')." <a href='".admin_url("post-new.php?post_type=page")."'><i class='fa fa-lg fa-plus'></i></a>",
					'id' => $this->meta_prefix.'page',
					'type' => 'page',
					'attributes' => array(
						'condition_type' => 'show_if',
						'condition_field' => $this->meta_prefix.'link',
					),
				),
				array(
					'name' => __("External Link", 'lang_custom_lists'),
					'id' => $this->meta_prefix.'link',
					'type' => 'url',
					'attributes' => array(
						'condition_type' => 'show_if',
						'condition_field' => $this->meta_prefix.'page',
					),
				),
			)
		);

		return $meta_boxes;
	}

	function restrict_manage_posts()
	{
		global $post_type, $wpdb;

		if($post_type == 'mf_custom_item')
		{
			//$strFilterCustomList = get_or_set_table_filter(array('key' => 'strFilterCustomList', 'save' => true));
			$strFilterCustomList = check_var('strFilterCustomList');

			$arr_data = array();
			get_post_children(array('post_type' => 'mf_custom_lists', 'post_status' => '', 'add_choose_here' => true), $arr_data);

			if(count($arr_data) > 2)
			{
				echo show_select(array('data' => $arr_data, 'name' => 'strFilterCustomList', 'value' => $strFilterCustomList));
			}
		}
	}

	function pre_get_posts($wp_query)
	{
		global $post_type, $pagenow;

		if($pagenow == 'edit.php' && $post_type == 'mf_custom_item')
		{
			//$strFilterCustomList = get_or_set_table_filter(array('key' => 'strFilterCustomList'));
			$strFilterCustomList = check_var('strFilterCustomList');

			if($strFilterCustomList != '')
			{
				$wp_query->query_vars['meta_query'] = array(
					array(
						'key' => $this->meta_prefix.'list_id',
						'value' => $strFilterCustomList,
						'compare' => '=',
					),
				);
			}
		}
	}

	function column_header($cols)
	{
		unset($cols['date']);

		$cols['items'] = __("Items", 'lang_custom_lists');
		$cols['style'] = __("Style", 'lang_custom_lists');
		$cols['shortcode'] = __("Shortcode", 'lang_custom_lists');
		$cols['date'] = __("Date", 'lang_custom_lists');

		return $cols;
	}

	function column_cell($col, $id)
	{
		global $wpdb;

		switch($col)
		{
			case 'items':
				$item_amount = $wpdb->get_var($wpdb->prepare("SELECT COUNT(meta_value) FROM ".$wpdb->postmeta." WHERE meta_key = %s AND meta_value = '%d'", $this->meta_prefix.'list_id', $id));

				echo "<a href='".admin_url("edit.php?post_type=mf_custom_item&strFilterCustomList=".$id)."'>".$item_amount."</a>
				<div class='row-actions'>
					<a href='".admin_url("post-new.php?post_type=mf_custom_item&list_id=".$id)."'>".__("Add New", 'lang_custom_lists')."</a>
				</div>";
			break;

			case 'style':
				$post_meta = get_post_meta($id, $this->meta_prefix.$col, true);

				if($post_meta != '')
				{
					echo $this->get_styles_for_select()[$post_meta];
				}
			break;

			case 'shortcode':
				$shortcode = "[mf_custom_list id=".$id."]";

				echo show_textfield(array('value' => $shortcode, 'xtra' => "readonly onclick='this.select()'"))
				."<div class='row-actions'>
					<a href='".admin_url("post-new.php?content=".$shortcode)."'>".__("Add New Post", 'lang_custom_lists')."</a> | <a href='".admin_url("post-new.php?post_type=page&content=".$shortcode)."'>".__("Add New Page", 'lang_custom_lists')."</a>
				</div>";
			break;
		}
	}

	function column_header_item($cols)
	{
		unset($cols['date']);

		$cols['list_id'] = __("List", 'lang_custom_lists');
		$cols['date'] = __("Date", 'lang_custom_lists');

		return $cols;
	}

	function column_cell_item($col, $id)
	{
		switch($col)
		{
			case 'list_id':
				$parent_id = get_post_meta($id, $this->meta_prefix.$col, true);
				$parent_title = get_the_title($parent_id);

				$edit_url = "post.php?post=".$parent_id."&action=edit";

				echo "<a href='".$edit_url."'>".$parent_title."</a>
				<div class='row-actions'>
					<span class='edit'><a href='".$edit_url."'>".__("Edit", 'lang_custom_lists')."</a></span>
				</div>";
			break;
		}
	}

	function count_shortcode_button($count)
	{
		if($count == 0)
		{
			$templates = get_posts(array(
				'post_type' => 'mf_custom_lists',
				'posts_per_page' => 1,
				'post_status' => 'publish'
			));

			if(count($templates) > 0)
			{
				$count++;
			}
		}

		return $count;
	}

	function get_shortcode_output($out)
	{
		$arr_data = array();
		get_post_children(array('add_choose_here' => true, 'post_type' => 'mf_custom_lists'), $arr_data);

		if(count($arr_data) > 1)
		{
			$out .= "<h3>".__("Choose a List", 'lang_custom_lists')."</h3>"
			.show_select(array('data' => $arr_data, 'xtra' => "rel='mf_custom_list'"));
		}

		return $out;
	}

	function get_shortcode_list($data)
	{
		global $wpdb;

		$post_id = $data[0];
		$content_list = $data[1];

		if($post_id > 0)
		{
			$post_content = mf_get_post_content($post_id);

			$arr_list_id = get_match_all("/\[mf_custom_list id=(.*?)\]/", $post_content, false);

			foreach($arr_list_id[0] as $list_id)
			{
				if($list_id > 0)
				{
					$content_list .= "<li><a href='".admin_url("post.php?post=".$list_id."&action=edit")."'>".get_post_title($list_id)."</a> <span class='grey'>[mf_custom_list id=".$list_id."]</span></li>";

					$result = $wpdb->get_results($wpdb->prepare("SELECT post_id FROM ".$wpdb->postmeta." WHERE meta_key = %s AND meta_value = '%d'", $this->meta_prefix.'list_id', $list_id));

					if($wpdb->num_rows > 0)
					{
						$content_list .= "<ul>";

							foreach($result as $r)
							{
								$object_id = $r->post_id;

								$content_list .= "<li><a href='".admin_url("post.php?post=".$object_id."&action=edit")."'>".get_post_title($object_id)."</a></li>";
							}

						$content_list .= "</ul>";
					}
				}
			}
		}

		return array($post_id, $content_list);
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

	function wp_head()
	{
		$plugin_include_url = plugin_dir_url(__FILE__);
		$plugin_version = get_plugin_version(__FILE__);

		mf_enqueue_style('style_custom_lists', $plugin_include_url."style.css", $plugin_version);
		mf_enqueue_script('script_custom_lists', $plugin_include_url."script.js", $plugin_version);
	}

	function render_shortcode($atts)
	{
		global $wpdb, $has_image;

		extract(shortcode_atts(array(
			'id' => '',
			'amount' => 0,
			'order' => '',
		), $atts));

		$out = "";
		$has_image = false;

		$result = $wpdb->get_results($wpdb->prepare("SELECT ID, post_name, post_excerpt, post_content FROM ".$wpdb->posts." WHERE post_type = 'mf_custom_lists' AND post_status = 'publish' AND ID = '%d'", $id));

		foreach($result as $r)
		{
			$parent_id = $r->ID;
			$post_name = $r->post_name;
			$parent_content = $r->post_content;
			$parent_excerpt = $r->post_excerpt;

			$parent_container = get_post_meta($parent_id, $this->meta_prefix.'container', true);
			$parent_items = get_post_meta($parent_id, $this->meta_prefix.'items', true);
			$parent_custom_style = get_post_meta($parent_id, $this->meta_prefix.'custom_style', true);
			$parent_style = get_post_meta($parent_id, $this->meta_prefix.'style', true);
			$parent_read_more = get_post_meta($parent_id, $this->meta_prefix.'read_more', true);
			$parent_columns_desktop = get_post_meta($parent_id, $this->meta_prefix.'columns_desktop', true);
			$parent_columns_tablet = get_post_meta($parent_id, $this->meta_prefix.'columns_tablet', true);
			$parent_columns_mobile = get_post_meta($parent_id, $this->meta_prefix.'columns_mobile', true);

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
							global $wpdb, $has_image;

							$out = "";

							switch($match[1])
							{
								case 'list_id':
									$out .= $child_id;
								break;

								case 'list_title':
									$child_title = get_the_title($child_id);

									$out .= $child_title;
								break;

								case 'list_text':
									$child_text = $wpdb->get_var($wpdb->prepare("SELECT post_content FROM ".$wpdb->posts." WHERE post_status = 'publish' AND ID = '%d'", $child_id));

									$out .= apply_filters('the_content', $child_text);
								break;

								case 'list_image':
									$child_image_id = get_post_meta($child_id, $this->meta_prefix.'image', true);

									if($child_image_id > 0)
									{
										$has_image = true;

										$out .= "<div class='image'>".render_image_tag(array('id' => $child_image_id, 'size' => 'large'))."</div>";
									}

									else if(function_exists('get_image_fallback'))
									{
										$out .= "<div class='image'>".get_image_fallback()."</div>";
									}
								break;

								case 'list_link':
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
								break;

								default:
									$out .= get_post_meta($child_id, $match[1], true);
								break;
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

			$parent_class = "custom_list";

			if($post_name != '')
			{
				$parent_class .= " custom_list_".$post_name;
			}

			if($parent_style != '')
			{
				$parent_class .= " custom_list_style_".$parent_style;
			}

			if($has_image == true)
			{
				$parent_class .= " custom_list_has_image";
			}

			if($parent_read_more == 'yes')
			{
				$parent_class .= " custom_list_read_more";
			}

			if($parent_columns_desktop > 0)
			{
				$parent_class .= " custom_list_columns_desktop_".$parent_columns_desktop;
			}

			if($parent_columns_tablet > 0)
			{
				$parent_class .= " custom_list_columns_tablet_".$parent_columns_tablet;
			}

			if($parent_columns_mobile > 0)
			{
				$parent_class .= " custom_list_columns_mobile_".$parent_columns_mobile;
			}

			$out = str_replace("[parent_class]", " class='".$parent_class."'", $out);

			if($parent_custom_style != '')
			{
				$out .= "<style>
					@media all
					{"
						.$parent_custom_style
					."}
				</style>";
			}
		}

		return $out;
	}

	function widgets_init()
	{
		register_widget('widget_custom_lists');
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
			.show_textfield(array('name' => $this->get_field_name('list_heading'), 'text' => __("Heading", 'lang_custom_lists'), 'value' => $instance['list_heading'], 'xtra' => " id='list-title'"))
			.show_select(array('data' => get_posts_for_select(array('post_type' => "mf_custom_lists")), 'name' => $this->get_field_name('list_id'), 'text' => __("List", 'lang_custom_lists'), 'value' => $instance['list_id']))
			."<div class='flex_flow'>"
				.show_textfield(array('type' => 'number', 'name' => $this->get_field_name('list_amount'), 'text' => __("Amount", 'lang_custom_lists'), 'value' => $instance['list_amount']))
				.show_select(array('data' => $this->get_order_for_select(), 'name' => $this->get_field_name('list_order'), 'text' => __("Order", 'lang_custom_lists'), 'value' => $instance['list_order']))
			."</div>
		</div>";
	}
}