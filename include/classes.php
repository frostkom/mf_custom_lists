<?php

class mf_custom_list
{
	var $post_type = 'mf_custom_lists';
	var $post_type_item = 'mf_custom_item';
	var $meta_prefix;

	function __construct()
	{
		$this->meta_prefix = $this->post_type.'_';
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
			'faq' => __("FAQ", 'lang_custom_lists'),
			//'flags' => __("Flags", 'lang_custom_lists'),
			//'flex' => __("Flex", 'lang_custom_lists'),
			//'horizontal' => __("Horizontal", 'lang_custom_lists'),
			'logos' => __("Logos", 'lang_custom_lists'),
			//'logos_v2' => __("Logos", 'lang_custom_lists')." v2",
			//'one_col' => __("One Column", 'lang_custom_lists'),
			'people' => __("People", 'lang_custom_lists'),
			'screenshots' => __("Screenshots", 'lang_custom_lists'),
			//'slider' => __("Slider", 'lang_custom_lists'),
			'testimonials' => __("Testimonials", 'lang_custom_lists'),
			//'vertical' => __("Vertical", 'lang_custom_lists'),
		);
	}

	function display_list($data)
	{
		global $wpdb, $has_image;

		$out = "";

		$plugin_include_url = plugin_dir_url(__FILE__);

		$has_image = false;

		$result = $wpdb->get_results($wpdb->prepare("SELECT ID, post_name, post_excerpt, post_content FROM ".$wpdb->posts." WHERE post_type = %s AND post_status = %s AND ID = '%d'", $this->post_type, 'publish', $data['list_id']));

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
			$parent_columns_gap = get_post_meta_or_default($parent_id, $this->meta_prefix.'columns_gap', true, 5);

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
				if($data['list_order'] != '')
				{
					$child_order = $data['list_order'];
				}

				else
				{
					$child_order = get_post_meta($parent_id, $this->meta_prefix.'order', true);
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

				$result2 = $wpdb->get_results($wpdb->prepare("SELECT ID, post_content FROM ".$wpdb->posts." INNER JOIN ".$wpdb->postmeta." ON ".$wpdb->posts.".ID = ".$wpdb->postmeta.".post_id AND meta_key = '".$this->meta_prefix."list_id' WHERE post_type = %s AND post_status = %s AND meta_value = '%d'".$query_order." LIMIT 0, %d", $this->post_type_item, 'publish', $parent_id, ($data['list_amount'] > 0 ? $data['list_amount'] : 100)));

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
							global $wpdb, $has_image;

							$out = "";

							switch($match[1])
							{
								case 'list_id':
									$out .= $child_id;
								break;

								case 'list_icon':
									$child_icon = get_post_meta($child_id, $this->meta_prefix.'icon', true);

									if($child_icon != '')
									{
										$out .= "<i class='".$child_icon."'></i>";
									}
								break;

								case 'list_title':
									$child_title = get_the_title($child_id);

									$out .= $child_title;
								break;

								case 'list_text':
									$child_text = $wpdb->get_var($wpdb->prepare("SELECT post_content FROM ".$wpdb->posts." WHERE post_status = %s AND ID = '%d'", 'publish', $child_id));

									$child_text = apply_filters('the_content', $child_text);

									if(strpos($child_text, "</p>") === false) // If not used, the first item does not get <p> for some reason...
									{
										$child_text = "<p>".$child_text."</p>";
									}

									$out .= $child_text;
								break;

								case 'list_excerpt':
									$child_text = $wpdb->get_var($wpdb->prepare("SELECT post_excerpt FROM ".$wpdb->posts." WHERE post_status = %s AND ID = '%d'", 'publish', $child_id));

									$out .= apply_filters('the_content', $child_text);
								break;

								case 'list_image':
								case 'list_image_no_link':
									$child_image_id = get_post_meta($child_id, $this->meta_prefix.'image', true);

									$image_url = $image_tag = "";

									if($child_image_id > 0)
									{
										$has_image = true;

										$arr_image = wp_get_attachment_image_src($child_image_id, 'full');

										if(is_array($arr_image))
										{
											$image_url = $arr_image[0];
										}

										$image_tag = render_image_tag(array('id' => $child_image_id, 'size' => 'large'));
									}

									else if(function_exists('get_image_fallback'))
									{
										$image_tag = get_image_fallback();
									}

									if($image_tag != '')
									{
										$out .= "<div class='image ".$match[1]."'>";

											if($image_url != '' && $match[1] == 'list_image')
											{
												$out .= "<a href='".$image_url."'>";
											}

												$out .= $image_tag;

											if($image_url != '' && $match[1] == 'list_image')
											{
												$out .= "</a>";
											}

										$out .= "</div>";
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

				if($out_children != '')
				{
					$out .= str_replace("[children]", $out_children, $parent_container);
				}
			}

			else if($parent_container != '')
			{
				$out .= $parent_container;
			}

			if($out != '')
			{
				switch($parent_style)
				{
					case 'faq':
						mf_enqueue_script('script_custom_lists', $plugin_include_url."script_faq.js");
					break;
				}

				$parent_class = "custom_list";
				$parent_class_selector = "";

				if($post_name != '')
				{
					$parent_class .= " custom_list_".$post_name;
					$parent_class_selector .= ".custom_list_".$post_name;
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

					mf_enqueue_script('script_custom_lists', $plugin_include_url."script_read_more.js");
				}

				$out = str_replace("[parent_class]", " class='".$parent_class."'", $out);

				$parent_custom_style = str_replace("[parent_class]", $parent_class_selector, $parent_custom_style);

				if($parent_columns_gap > 0)
				{
					if($parent_columns_desktop > 0)
					{
						$parent_custom_style .= $parent_class_selector." li
						{
							width: ".((100 - ($parent_columns_gap * ($parent_columns_desktop - 1))) / $parent_columns_desktop)."%;
						}";
					}

					$setting_breakpoint_tablet = apply_filters('get_styles_content', '', 'max_width');

					if($setting_breakpoint_tablet != '')
					{
						preg_match('/^([0-9]*\.?[0-9]+)([a-zA-Z%]+)$/', $setting_breakpoint_tablet, $matches);

						$setting_breakpoint_tablet = $matches[1];
						$setting_breakpoint_suffix = $matches[2];

						$setting_breakpoint_mobile = ($setting_breakpoint_tablet * .775);
					}

					else
					{
						$setting_breakpoint_tablet = get_option_or_default('setting_custom_list_tablet_breakpoint', 1100);
						$setting_breakpoint_mobile = get_option_or_default('setting_custom_list_mobile_breakpoint', 900);

						$setting_breakpoint_suffix = "px";
					}

					if($parent_columns_tablet > 0)
					{
						$parent_custom_style .= "@media screen and (max-width: ".$setting_breakpoint_tablet.$setting_breakpoint_suffix.")
						{
							".$parent_class_selector." li
							{
								width: ".((100 - ($parent_columns_gap * ($parent_columns_tablet - 1))) / $parent_columns_tablet)."%;
							}
						}";
					}

					if($parent_columns_mobile > 0)
					{
						$parent_custom_style .= "@media screen and (max-width: ".$setting_breakpoint_mobile.$setting_breakpoint_suffix.")
						{
							".$parent_class_selector." li
							{
								width: ".((100 - ($parent_columns_gap * ($parent_columns_mobile - 1))) / $parent_columns_mobile)."%;
							}
						}";
					}

					$parent_custom_style .= $parent_class_selector."
					{
						gap: ".$parent_columns_gap."%;
					}";
				}

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

	function block_render_callback($attributes)
	{
		if(!isset($attributes['list_id'])){			$attributes['list_id'] = 0;}
		if(!isset($attributes['list_order'])){		$attributes['list_order'] = "";}
		if(!isset($attributes['list_amount'])){		$attributes['list_amount'] = 0;}

		$out = "";

		if($attributes['list_id'] > 0)
		{
			$out .= "<div".parse_block_attributes(array('class' => "widget custom_list", 'attributes' => $attributes)).">
				<div class='section'>"
					.$this->display_list($attributes)
				."</div>
			</div>";
		}

		return $out;
	}

	function init()
	{
		// Post types
		#######################
		register_post_type($this->post_type, array(
			'labels' => array(
				'name' => __("Custom Lists", 'lang_custom_lists'),
				'singular_name' => __("Custom List", 'lang_custom_lists'),
				'menu_name' => __("Custom Lists", 'lang_custom_lists')
			),
			'public' => false, // Previously true but changed to hide in sitemap.xml
			'show_ui' => true,
			'show_in_menu' => false,
			'show_in_nav_menus' => false,
			'exclude_from_search' => true,
			'supports' => array('title'),
			'hierarchical' => false,
			'has_archive' => false,
		));

		register_post_type($this->post_type_item, array(
			'labels' => array(
				'name' => __("Items", 'lang_custom_lists'),
				'singular_name' => __("Item", 'lang_custom_lists'),
				'menu_name' => __("Items", 'lang_custom_lists')
			),
			'public' => false,
			'show_ui' => true,
			'show_in_menu' => false,
			'show_in_nav_menus' => false,
			'exclude_from_search' => true,
			'supports' => array('title', 'editor', 'excerpt'), //, 'custom-fields'
			'hierarchical' => true,
			'has_archive' => false,
		));
		#######################

		// Blocks
		#######################
		$plugin_include_url = plugin_dir_url(__FILE__);
		$plugin_version = get_plugin_version(__FILE__);

		wp_register_script('script_custom_lists_block_wp', $plugin_include_url."block/script_wp.js", array('wp-blocks', 'wp-element', 'wp-components', 'wp-editor', 'wp-block-editor'), $plugin_version, true);

		$arr_data = array();
		get_post_children(array('add_choose_here' => true, 'post_type' => $this->post_type), $arr_data);

		wp_localize_script('script_custom_lists_block_wp', 'script_custom_lists_block_wp', array(
			'block_title' => __("Custom List", 'lang_custom_lists'),
			'block_description' => __("Display a Custom List", 'lang_custom_lists'),
			'list_id_label' => __("List", 'lang_custom_lists'),
			'list_id' => $arr_data,
			'list_amount_label' => __("Amount", 'lang_custom_lists'),
			'list_order_label' => __("Order", 'lang_custom_lists'),
			'list_order' => $this->get_order_for_select(),
		));

		register_block_type('mf/customlists', array(
			'editor_script' => 'script_custom_lists_block_wp',
			'editor_style' => 'style_base_block_wp',
			'render_callback' => array($this, 'block_render_callback'),
			//'style' => 'style_base_block_wp',
		));
		#######################
	}

	function admin_menu()
	{
		$menu_start = "edit.php?post_type=".$this->post_type;
		$menu_capability = 'edit_pages';

		$menu_title = __("Custom Lists", 'lang_custom_lists');
		add_menu_page("", $menu_title, $menu_capability, $menu_start, '', 'dashicons-list-view', 21);

		$menu_title = __("Lists", 'lang_custom_lists');
		add_submenu_page($menu_start, $menu_title, $menu_title, $menu_capability, $menu_start);

		$menu_title = __("Add New", 'lang_custom_lists');
		add_submenu_page($menu_start, $menu_title, " - ".$menu_title, $menu_capability, "post-new.php?post_type=".$this->post_type);

		if(does_post_exists(array('post_type' => $this->post_type)))
		{
			$menu_title = __("Items", 'lang_custom_lists');
			add_submenu_page($menu_start, $menu_title, $menu_title, $menu_capability, "edit.php?post_type=".$this->post_type_item);

			$menu_title = __("Add New", 'lang_custom_lists');
			add_submenu_page($menu_start, $menu_title, " - ".$menu_title, $menu_capability, "post-new.php?post_type=".$this->post_type_item);
		}
	}

	function settings_custom_list()
	{
		if(apply_filters('get_styles_content', '', 'max_width') == '')
		{
			$options_area = __FUNCTION__;

			add_settings_section($options_area, "", array($this, $options_area."_callback"), BASE_OPTIONS_PAGE);

			$arr_settings = array();
			$arr_settings['setting_custom_list_tablet_breakpoint'] = __("Tablet Breakpoint", 'lang_custom_lists');
			$arr_settings['setting_custom_list_mobile_breakpoint'] = __("Mobile Breakpoint", 'lang_custom_lists');

			show_settings_fields(array('area' => $options_area, 'object' => $this, 'settings' => $arr_settings));
		}

		else
		{
			delete_option('setting_custom_list_tablet_breakpoint');
			delete_option('setting_custom_list_mobile_breakpoint');
		}
	}

	function settings_custom_list_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);

		echo settings_header($setting_key, __("Custom Lists", 'lang_custom_lists'));
	}

	function setting_custom_list_tablet_breakpoint_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);
		$option = get_option_or_default($setting_key, 1100);

		echo show_textfield(array('type' => 'number', 'name' => $setting_key, 'value' => $option));
	}

	function setting_custom_list_mobile_breakpoint_callback()
	{
		$setting_key = get_setting_key(__FUNCTION__);
		$option = get_option_or_default($setting_key, 900);

		echo show_textfield(array('type' => 'number', 'name' => $setting_key, 'value' => $option));
	}

	function filter_sites_table_pages($arr_pages)
	{
		$arr_pages[$this->post_type] = array(
			'icon' => "fas fa-list",
			'title' => __("Lists", 'lang_custom_lists'),
		);

		return $arr_pages;
	}

	function meta_items()
	{
		global $post_id;

		return $this->get_list_items(array('display_container' => false, 'class' => 'meta_list', 'list_id' => $post_id));
	}

	function display_post_states($post_states, $post)
	{
		global $wpdb;

		$result = $wpdb->get_results($wpdb->prepare("SELECT post_title FROM ".$wpdb->posts." INNER JOIN ".$wpdb->postmeta." ON ".$wpdb->posts.".ID = ".$wpdb->postmeta.".post_id WHERE meta_key = %s AND meta_value = '%d'", $this->meta_prefix.'page', $post->ID));

		if($wpdb->num_rows > 0)
		{
			$post_titles = "";

			foreach($result as $r)
			{
				$post_titles .= ($post_titles != '' ? ", " : "").$r->post_title;
			}

			$post_states[$this->meta_prefix.'page'] = sprintf(__("Link from %s", 'lang_custom_lists'), $post_titles);
		}

		return $post_states;
	}

	function rwmb_meta_boxes($meta_boxes)
	{
		global $wpdb, $obj_base;

		if(!isset($obj_base))
		{
			$obj_base = new mf_base();
		}

		$meta_boxes[] = array(
			'id' => $this->meta_prefix.'structure',
			'title' => __("Structure", 'lang_custom_lists'),
			'post_types' => array($this->post_type),
			//'context' => 'side',
			'priority' => 'low',
			'fields' => array(
				array(
					'name' => __("Container", 'lang_custom_lists'),
					'id' => $this->meta_prefix.'container',
					'type' => 'text',
					'std' => "<ul[parent_class]>[children]</ul>",
					'sanitize_callback' => 'none',
				),
				array(
					'name' => __("Items", 'lang_custom_lists'),
					'id' => $this->meta_prefix.'items',
					'type' => 'textarea',
					'std' => "<li>[list_icon]<h4><a href='[list_link]'>[list_title]</a></h4>[list_excerpt][list_image][list_text]</li>",
					'sanitize_callback' => 'none',
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
					'placeholder' => "[parent_class] li",
				),
			)
		);

		$meta_boxes[] = array(
			'id' => $this->meta_prefix.'settings',
			'title' => __("Settings", 'lang_custom_lists'),
			'post_types' => array($this->post_type),
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
					'std' => 4,
					'attributes' => array(
						'min' => 1,
						'max' => 5,
					),
				),
				array(
					'name' => __("Columns", 'lang_custom_lists')." (".__("Tablet", 'lang_custom_lists').")",
					'id' => $this->meta_prefix.'columns_tablet',
					'type' => 'number',
					'std' => 3,
					'attributes' => array(
						'min' => 1,
						'max' => 5,
					),
				),
				array(
					'name' => __("Columns", 'lang_custom_lists')." (".__("Mobile", 'lang_custom_lists').")",
					'id' => $this->meta_prefix.'columns_mobile',
					'type' => 'number',
					'std' => 2,
					'attributes' => array(
						'min' => 1,
						'max' => 5,
					),
				),
				array(
					'name' => __("Gap", 'lang_custom_lists')." (%)",
					'id' => $this->meta_prefix.'columns_gap',
					'type' => 'number',
					'std' => 5,
					'attributes' => array(
						'min' => 1,
						'max' => 20,
					),
				),
			)
		);

		$default_list_id = '';

		$post_id = check_var('post');

		if($post_id > 0)
		{
			$meta_boxes[] = array(
				'id' => $this->meta_prefix.'items',
				'title' => __("Items", 'lang_custom_lists'),
				'post_types' => array($this->post_type),
				//'context' => 'side',
				'priority' => 'low',
				'fields' => array(
					array(
						'id' => $this->meta_prefix.'items',
						'type' => 'custom_html',
						'callback' => array($this, 'meta_items'),
					),
				)
			);
		}

		else
		{
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
				$default_list_id = $wpdb->get_var($wpdb->prepare("SELECT ID FROM ".$wpdb->posts." WHERE post_type = %s AND post_status = %s ORDER BY post_modified DESC LIMIT 0, 1", $this->post_type, 'publish'));
			}
		}

		$arr_data = array();
		get_post_children(array('add_choose_here' => false, 'post_type' => $this->post_type), $arr_data);

		$meta_boxes[] = array(
			'id' => $this->meta_prefix.'settings',
			'title' => __("Settings", 'lang_custom_lists'),
			'post_types' => array($this->post_type_item),
			'context' => 'side',
			'priority' => 'low',
			'fields' => array(
				array(
					'name' => __("List", 'lang_custom_lists'),
					'id' => $this->meta_prefix.'list_id',
					'type' => 'select',
					'options' => $arr_data,
					'multiple' => true,
					'attributes' => array(
						'required' => 'true',
						'size' => get_select_size(array('count' => count($arr_data))),
					),
					'std' => $default_list_id,
				),
				array(
					'name' => __("Icon", 'lang_custom_lists'),
					'id' => $this->meta_prefix.'icon',
					'type' => 'select',
					'options' => $obj_base->get_icons_for_select(),
				),
				array(
					'name' => __("Image", 'lang_custom_lists'),
					'id' => $this->meta_prefix.'image',
					'type' => 'file_advanced',
					'max_file_uploads' => 1,
					'mime_type' => 'image',
				),
				array(
					'name' => __("Page", 'lang_custom_lists')." <a href='".admin_url("post-new.php?post_type=page")."'><i class='fa fa-plus-circle fa-lg'></i></a>",
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
		global $post_type;

		if($post_type == $this->post_type_item)
		{
			$strFilterCustomList = check_var('strFilterCustomList');

			$arr_data = array();
			get_post_children(array('post_type' => $this->post_type, 'post_status' => '', 'add_choose_here' => true), $arr_data);

			if(count($arr_data) > 2)
			{
				echo show_select(array('data' => $arr_data, 'name' => 'strFilterCustomList', 'value' => $strFilterCustomList));
			}
		}
	}

	function pre_get_posts($wp_query)
	{
		global $post_type, $pagenow;

		if($pagenow == 'edit.php' && $post_type == $this->post_type_item)
		{
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
		global $post_type;

		unset($cols['date']);

		switch($post_type)
		{
			case $this->post_type:
				$cols['items'] = __("Items", 'lang_custom_lists');
				$cols['style'] = __("Style", 'lang_custom_lists');
				$cols['columns'] = __("Columns", 'lang_custom_lists');
				//$cols['shortcode'] = __("Shortcode", 'lang_custom_lists');
			break;

			case $this->post_type_item:
				$cols['list_id'] = __("List", 'lang_custom_lists');
			break;
		}

		return $cols;
	}

	function column_cell($col, $post_id)
	{
		global $wpdb, $post;

		switch($post->post_type)
		{
			case $this->post_type:
				switch($col)
				{
					case 'items':
						$item_amount = $wpdb->get_var($wpdb->prepare("SELECT COUNT(meta_value) FROM ".$wpdb->posts." INNER JOIN ".$wpdb->postmeta." ON ".$wpdb->posts.".ID = ".$wpdb->postmeta.".post_id WHERE post_status != %s AND meta_key = %s AND meta_value = '%d'", 'trash', $this->meta_prefix.'list_id', $post_id));

						echo "<a href='".admin_url("edit.php?post_type=".$this->post_type_item."&strFilterCustomList=".$post_id)."'>".$item_amount."</a>
						<div class='row-actions'>
							<a href='".admin_url("post-new.php?post_type=".$this->post_type_item."&list_id=".$post_id)."'>".__("Add New", 'lang_custom_lists')."</a>
						</div>";
					break;

					case 'style':
						$post_meta = get_post_meta($post_id, $this->meta_prefix.$col, true);

						if($post_meta != '')
						{
							echo $this->get_styles_for_select()[$post_meta];
						}
					break;

					case 'columns':
						$post_meta_columns_desktop = get_post_meta($post_id, $this->meta_prefix.'columns_desktop', true);
						$post_meta_columns_tablet = get_post_meta($post_id, $this->meta_prefix.'columns_tablet', true);
						$post_meta_columns_mobile = get_post_meta($post_id, $this->meta_prefix.'columns_mobile', true);

						if($post_meta_columns_desktop > 0)
						{
							echo "<span title='".__("Desktop", 'lang_custom_lists')."'>".$post_meta_columns_desktop."</span>";
						}

						if($post_meta_columns_tablet > 0)
						{
							if($post_meta_columns_desktop > 0)
							{
								echo " / ";
							}

							echo "<span title='".__("Tablet", 'lang_custom_lists')."'>".$post_meta_columns_tablet."</span>";
						}

						if($post_meta_columns_mobile > 0)
						{
							if($post_meta_columns_desktop > 0 || $post_meta_columns_tablet > 0)
							{
								echo " / ";
							}

							echo "<span title='".__("Mobile", 'lang_custom_lists')."'>".$post_meta_columns_mobile."</span>";
						}
					break;

					/*case 'shortcode':
						$shortcode = "[mf_custom_list id=".$post_id."]";

						echo show_textfield(array('value' => $shortcode, 'readonly' => true, 'xtra' => "onclick='this.select()'"))
						."<div class='row-actions'>
							<a href='".admin_url("post-new.php?post_type=page&content=".$shortcode)."'>".__("Add New Page", 'lang_custom_lists')."</a>
						</div>";
					break;*/
				}
			break;

			case $this->post_type_item:
				switch($col)
				{
					case 'list_id':
						$out = "";

						$arr_parent_id = get_post_meta($post_id, $this->meta_prefix.$col, false);

						if(is_array($arr_parent_id))
						{
							foreach($arr_parent_id as $parent_id)
							{
								$out .= ($out != '' ? ", " : "");

								if(get_post_status($parent_id) == 'publish')
								{
									$out .= "<a href='".admin_url("post.php?post=".$parent_id."&action=edit")."'>";
								}

									$out .= get_the_title($parent_id);

								if(get_post_status($parent_id) == 'publish')
								{
									$out .= "</a>";
								}
							}
						}

						echo $out;
					break;
				}
			break;
		}
	}

	function get_list_items($data)
	{
		global $wpdb;

		if(!isset($data['display_container'])){		$data['display_container'] = true;}
		if(!isset($data['class'])){					$data['class'] = "";}

		$out = "";

		$result = $wpdb->get_results($wpdb->prepare("SELECT post_id FROM ".$wpdb->postmeta." WHERE meta_key = %s AND meta_value = '%d'", $this->meta_prefix.'list_id', $data['list_id']));

		if($wpdb->num_rows > 0)
		{
			$out .= "<ul".($data['class'] != '' ? " class='".$data['class']."'" : "").">";

				foreach($result as $r)
				{
					$object_id = $r->post_id;

					$out .= "<li><a href='".admin_url("post.php?post=".$object_id."&action=edit")."'>".get_the_title($object_id)."</a></li>";
				}

			$out .= "</ul>";
		}

		else
		{
			if($data['display_container'] == true)
			{
				$out .= "<li>";
			}

				$out .= "<a href='".admin_url("post-new.php?post_type=".$this->post_type_item)."'>".__("Add New", 'lang_custom_lists')."</a>";

			if($data['display_container'] == true)
			{
				$out .= "</li>";
			}
		}

		return $out;
	}

	function wp_trash_post($post_id)
	{
		global $wpdb;

		if(get_post_type($post_id) == $this->post_type)
		{
			$result = $wpdb->get_results($wpdb->prepare("SELECT ID FROM ".$wpdb->posts." INNER JOIN ".$wpdb->postmeta." ON ".$wpdb->posts.".ID = ".$wpdb->postmeta.".post_id WHERE post_type = %s AND meta_key = '".$this->meta_prefix."list_id' AND meta_value = '%d'", $this->post_type_item, $post_id));

			foreach($result as $r)
			{
				do_log(__FUNCTION__.": Trash <a href='".admin_url("post.php?post=".$r->ID."&action=edit")."'>#".$r->ID."</a> if it only is connected to one list");

				//wp_trash_post($r->ID);
			}
		}
	}

	function filter_last_updated_post_types($array, $type)
	{
		if($type == 'auto')
		{
			$array[] = $this->post_type;
			$array[] = $this->post_type_item;
		}

		return $array;
	}

	function wp_head()
	{
		$plugin_include_url = plugin_dir_url(__FILE__);

		mf_enqueue_style('style_custom_lists', $plugin_include_url."style.css");
	}

	function shortcode_custom_list($atts)
	{
		global $post;

		$out = "";

		do_log(__FUNCTION__.": Add a block instead (#".$post->ID.", ".var_export($atts, true).")", 'publish', false);

		return $out;
	}

	function filter_is_file_used($arr_used)
	{
		global $wpdb;

		$result = $wpdb->get_results($wpdb->prepare("SELECT ID FROM ".$wpdb->posts." INNER JOIN ".$wpdb->postmeta." ON ".$wpdb->posts.".ID = ".$wpdb->postmeta.".post_id WHERE post_status = %s AND meta_key = %s AND meta_value = %s", 'publish', $this->meta_prefix.'image', $arr_used['id']));
		$rows = $wpdb->num_rows;

		if($rows > 0)
		{
			$arr_used['amount'] += $rows;

			foreach($result as $r)
			{
				if($arr_used['example'] != '')
				{
					break;
				}

				$arr_used['example'] = admin_url("post.php?action=edit&post=".$r->ID);
			}
		}

		return $arr_used;
	}

	function widgets_init()
	{
		if(wp_is_block_theme() == false)
		{
			register_widget('widget_custom_lists');
		}
	}
}

class widget_custom_lists extends WP_Widget
{
	var $widget_ops;
	var $arr_default = array(
		'list_heading' => '',
		'list_content' => '',
		'list_id' => '',
		'list_amount' => 0,
		'list_order' => 'numerical',
	);

	function __construct()
	{
		$this->widget_ops = array(
			'classname' => 'custom_list',
			'description' => __("Display a Custom List", 'lang_custom_lists'),
		);

		parent::__construct(str_replace("_", "-", $this->widget_ops['classname']).'-widget', __("Custom List", 'lang_custom_lists'), $this->widget_ops);
	}

	function widget($args, $attributes)
	{
		do_log(__CLASS__."->".__FUNCTION__."(): Add a block instead", 'publish', false);

		global $obj_custom_list;

		extract($args);
		$attributes = wp_parse_args((array)$attributes, $this->arr_default);

		if($attributes['list_id'] > 0)
		{
			if(!isset($obj_custom_list))
			{
				$obj_custom_list = new mf_custom_list();
			}

			$out_temp = $obj_custom_list->display_list($attributes);

			if($out_temp != '')
			{
				echo apply_filters('filter_before_widget', $before_widget);

					if($attributes['list_heading'] != '')
					{
						$attributes['list_heading'] = apply_filters('widget_title', $attributes['list_heading'], $attributes, $this->id_base);

						echo $before_title
							.$attributes['list_heading']
						.$after_title;
					}

					echo "<div class='section'>";

						if($attributes['list_content'] != '')
						{
							echo apply_filters('the_content', $attributes['list_content']);
						}

						echo $out_temp
					."</div>"
				.$after_widget;
			}
		}
	}

	function update($new_attributes, $old_attributes)
	{
		$attributes = $old_attributes;
		$new_attributes = wp_parse_args((array)$new_attributes, $this->arr_default);

		$attributes['list_heading'] = sanitize_text_field($new_attributes['list_heading']);
		$attributes['list_content'] = sanitize_text_field($new_attributes['list_content']);
		$attributes['list_id'] = sanitize_text_field($new_attributes['list_id']);
		$attributes['list_amount'] = sanitize_text_field($new_attributes['list_amount']);
		$attributes['list_order'] = sanitize_text_field($new_attributes['list_order']);

		return $attributes;
	}

	function form($attributes)
	{
		$attributes = wp_parse_args((array)$attributes, $this->arr_default);

		$obj_custom_list = new mf_custom_list();

		$arr_data = array();
		get_post_children(array('add_choose_here' => true, 'post_type' => $obj_custom_list->post_type), $arr_data);

		echo "<div class='mf_form'>"
			.show_textfield(array('name' => $this->get_field_name('list_heading'), 'text' => __("Heading", 'lang_custom_lists'), 'value' => $attributes['list_heading'], 'xtra' => " id='".$this->widget_ops['classname']."-title'"))
			.show_textarea(array('name' => $this->get_field_name('list_content'), 'text' => __("Content", 'lang_custom_lists'), 'value' => $attributes['list_content']))
			.show_select(array('data' => $arr_data, 'name' => $this->get_field_name('list_id'), 'text' => __("List", 'lang_custom_lists'), 'value' => $attributes['list_id'], 'suffix' => get_option_page_suffix(array('post_type' => $obj_custom_list->post_type, 'value' => $attributes['list_id'])), 'allow_hidden_field' => false))
			."<div class='flex_flow'>"
				.show_textfield(array('type' => 'number', 'name' => $this->get_field_name('list_amount'), 'text' => __("Amount", 'lang_custom_lists'), 'value' => $attributes['list_amount']))
				.show_select(array('data' => $obj_custom_list->get_order_for_select(), 'name' => $this->get_field_name('list_order'), 'text' => __("Order", 'lang_custom_lists'), 'value' => $attributes['list_order']))
			."</div>
		</div>";
	}
}