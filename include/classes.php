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

	function cron_base()
	{
		$obj_cron = new mf_cron();
		$obj_cron->start(__CLASS__);

		if($obj_cron->is_running == false)
		{
			mf_uninstall_plugin(array(
				'options' => array('setting_custom_list_tablet_breakpoint', 'setting_custom_list_mobile_breakpoint'),
			));
		}

		$obj_cron->end();
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
			//'about_us' => __("About Us", 'lang_custom_lists'),
			//'faq' => __("FAQ", 'lang_custom_lists'),
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

	function block_render_callback($attributes)
	{
		global $wpdb, $has_image;

		if(!isset($attributes['list_id'])){			$attributes['list_id'] = [];}
		if(!isset($attributes['list_order'])){		$attributes['list_order'] = "";}

		$out = "";

		if(is_array($attributes['list_id']) && count($attributes['list_id']) > 0 || $attributes['list_id'] > 0)
		{
			$plugin_include_url = plugin_dir_url(__FILE__);

			mf_enqueue_style('style_custom_lists', $plugin_include_url."style.css");

			$out .= "<div".parse_block_attributes(array('class' => "widget custom_list", 'attributes' => $attributes)).">";

				$query_where = "";

				$arr_data = [
					'' => "-- ".__("All", 'lang_custom_lists')." --",
				];

				if(is_array($attributes['list_id']))
				{
					if(count($attributes['list_id']) > 0)
					{
						$query_where .= " AND ID IN (";

							$i = 0;

							foreach($attributes['list_id'] as $list_id)
							{
								$query_where .= ($i > 0 ? ", " : "")."'".esc_sql($list_id)."'";

								$i++;
							}

						$query_where .= ")";
					}

					else // Why does this even happen?
					{
						$query_where .= " AND ID = '0'";
					}
				}

				else
				{
					$query_where .= " AND ID = '".esc_sql($attributes['list_id'])."'";
				}

				$out_parent = $out_children = "";
				$arr_children = [];

				$result = $wpdb->get_results($wpdb->prepare("SELECT ID, post_title, post_excerpt, post_content FROM ".$wpdb->posts." WHERE post_type = %s AND post_status = %s".$query_where." ORDER BY menu_order ASC", $this->post_type, 'publish'));

				foreach($result as $r)
				{
					$parent_id = $r->ID;
					$parent_title = $r->post_title;
					$parent_content = $r->post_content;
					$parent_excerpt = $r->post_excerpt;

					$parent_container = get_post_meta($parent_id, $this->meta_prefix.'container', true);
					$parent_items = get_post_meta($parent_id, $this->meta_prefix.'items', true);
					$parent_custom_style = get_post_meta($parent_id, $this->meta_prefix.'custom_style', true);
					$parent_style = get_post_meta($parent_id, $this->meta_prefix.'style', true);
					$parent_read_more = get_post_meta($parent_id, $this->meta_prefix.'read_more', true);
					/*$parent_columns_desktop = get_post_meta_or_default($parent_id, $this->meta_prefix.'columns_desktop', true, 4);
					$parent_columns_tablet = get_post_meta_or_default($parent_id, $this->meta_prefix.'columns_tablet', true, 3);
					$parent_columns_mobile = get_post_meta_or_default($parent_id, $this->meta_prefix.'columns_mobile', true, 2);
					$parent_columns_gap = get_post_meta_or_default($parent_id, $this->meta_prefix.'columns_gap', true, 5);*/
					$parent_columns_desktop = 4;
					$parent_columns_tablet = 3;
					$parent_columns_mobile = 2;
					$parent_columns_gap = 5;

					if($parent_container == '')
					{
						$parent_container = $parent_content;
					}

					if($parent_items == '')
					{
						$parent_items = $parent_excerpt;
					}

					if($out_parent == '')
					{
						if($parent_container != '')
						{
							$out_parent .= $parent_container;
						}

						if($out_parent != '')
						{
							/*switch($parent_style)
							{
								case 'faq':
									mf_enqueue_script('script_custom_lists', $plugin_include_url."script_faq.js");
								break;
							}*/

							$parent_class = $parent_class_selector = "";

							if($parent_style != '')
							{
								$parent_class .= ($parent_class != '' ? " " : "")."custom_list_style_".$parent_style;
								$parent_class_selector .= ".custom_list_style_".$parent_style;
							}

							if($parent_read_more == 'yes')
							{
								$parent_class .= ($parent_class != '' ? " " : "")."custom_list_read_more";

								mf_enqueue_script('script_custom_lists', $plugin_include_url."script_read_more.js", array(
									'read_more' => __("Read More", 'lang_custom_lists'),
								));
							}

							$out_parent = str_replace("[parent_class]", " class='".$parent_class."'", $out_parent);

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

								$arr_breakpoints = apply_filters('get_layout_breakpoints', ['tablet' => 1200, 'mobile' => 930, 'suffix' => "px"]);

								if($parent_columns_tablet > 0)
								{
									$parent_custom_style .= "@media screen and (max-width: ".$arr_breakpoints['tablet'].$arr_breakpoints['suffix'].")
									{
										".$parent_class_selector." li
										{
											width: ".((100 - ($parent_columns_gap * ($parent_columns_tablet - 1))) / $parent_columns_tablet)."%;
										}
									}";
								}

								if($parent_columns_mobile > 0)
								{
									$parent_custom_style .= "@media screen and (max-width: ".$arr_breakpoints['mobile'].$arr_breakpoints['suffix'].")
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

							$out_parent .= "<style>
								@media all
								{"
									.$parent_custom_style
								."}
							</style>";
						}
					}

					if(preg_match("/\[children\]/i", $parent_container))
					{
						switch($attributes['list_order'])
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

						$query_select = "";

						if($parent_items == '')
						{
							$query_select = ", post_content";
						}

						$result_children = $wpdb->get_results($wpdb->prepare("SELECT ID".$query_select." FROM ".$wpdb->posts." INNER JOIN ".$wpdb->postmeta." ON ".$wpdb->posts.".ID = ".$wpdb->postmeta.".post_id AND meta_key = '".$this->meta_prefix."list_id' WHERE post_type = %s AND post_status = %s AND meta_value = '%d' GROUP BY ID".$query_order, $this->post_type_item, 'publish', $parent_id));

						if($wpdb->num_rows > 0)
						{
							$arr_data[$parent_id] = $parent_title;

							foreach($result_children as $r)
							{
								$child_id = $r->ID;

								if(!in_array($child_id, $arr_children))
								{
									$arr_children[] = $child_id;

									if($parent_items != '')
									{
										$child_content = $parent_items;
									}

									else
									{
										$child_content = $r->post_content;
									}

									if(is_array($attributes['list_id']))
									{
										$parent_class = "";
										$parent_class .= ($parent_class != "" ? " " : "")."parent_".$parent_id;

										$result_parents = $wpdb->get_results($wpdb->prepare("SELECT meta_value FROM ".$wpdb->posts." INNER JOIN ".$wpdb->postmeta." ON ".$wpdb->posts.".ID = ".$wpdb->postmeta.".post_id AND meta_key = '".$this->meta_prefix."list_id' WHERE post_type = %s AND post_status = %s AND ID = '%d' AND meta_value != '%d' GROUP BY meta_value", $this->post_type_item, 'publish', $child_id, $parent_id));

										foreach($result_parents as $r)
										{
											$parent_class .= ($parent_class != "" ? " " : "")."parent_".$r->meta_value;
										}

										if($parent_class != "")
										{
											$child_content = str_replace("<li>", "<li class='".$parent_class."'>", $child_content);
										}
									}

									$out_children .= preg_replace_callback(
										"/\[(.*?)\]/i",
										function($match) use ($child_id)
										{
											global $wpdb;

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

													$out .= "<div class='text'>".$child_text."</div>";
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
														$arr_image = wp_get_attachment_image_src($child_image_id, 'full');

														if(is_array($arr_image))
														{
															$image_url = $arr_image[0];
														}

														$image_tag = render_image_tag(array('id' => $child_image_id, 'size' => 'large'));
													}

													else
													{
														$image_tag = apply_filters('get_image_fallback', "");
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
							}
						}
					}
				}

				if(count($arr_data) > 2)
				{
					$out .= "<form method='post' action='#' class='mf_form'>"
						.show_select(array('data' => $arr_data, 'name' => ''))
					."</form>";

					mf_enqueue_script('script_custom_lists_multiple', $plugin_include_url."script_multiple.js");
				}

				if($out_children != '')
				{
					$out .= str_replace("[children]", $out_children, $out_parent);
				}

				else
				{
					$out .= $out_parent;
				}

			$out .= "</div>";
		}

		return $out;
	}

	function enqueue_block_editor_assets()
	{
		$plugin_include_url = plugin_dir_url(__FILE__);
		$plugin_version = get_plugin_version(__FILE__);

		wp_register_script('script_custom_lists_block_wp', $plugin_include_url."block/script_wp.js", array('wp-blocks', 'wp-element', 'wp-components', 'wp-editor', 'wp-block-editor'), $plugin_version, true);

		$arr_data = [];
		get_post_children(array('add_choose_here' => false, 'post_type' => $this->post_type), $arr_data);

		wp_localize_script('script_custom_lists_block_wp', 'script_custom_lists_block_wp', array(
			'block_title' => __("Custom List", 'lang_custom_lists'),
			'block_description' => __("Display a Custom List", 'lang_custom_lists'),
			'list_id_label' => __("List", 'lang_custom_lists'),
			'list_id' => $arr_data,
			'list_order_label' => __("Order", 'lang_custom_lists'),
			'list_order' => $this->get_order_for_select(),
		));
	}

	function init()
	{
		register_post_type($this->post_type, array(
			'labels' => array(
				'name' => __("Custom Lists", 'lang_custom_lists'),
				'singular_name' => __("Custom List", 'lang_custom_lists'),
				'menu_name' => __("Custom Lists", 'lang_custom_lists'),
				'all_items' => __('List', 'lang_custom_lists'),
				'edit_item' => __('Edit', 'lang_custom_lists'),
				'view_item' => __('View', 'lang_custom_lists'),
				'add_new_item' => __('Add New', 'lang_custom_lists'),
			),
			'public' => false,
			'show_ui' => true,
			'show_in_menu' => false,
			'show_in_nav_menus' => false,
			'show_in_rest' => true,
			'supports' => array('title', 'editor'),
			'hierarchical' => true,
			'has_archive' => false,
		));

		register_post_type($this->post_type_item, array(
			'labels' => array(
				'name' => __("Items", 'lang_custom_lists'),
				'singular_name' => __("Item", 'lang_custom_lists'),
				'menu_name' => __("Items", 'lang_custom_lists'),
				'all_items' => __('List', 'lang_custom_lists'),
				'edit_item' => __('Edit', 'lang_custom_lists'),
				'view_item' => __('View', 'lang_custom_lists'),
				'add_new_item' => __('Add New', 'lang_custom_lists'),
			),
			'public' => false,
			'show_ui' => true,
			'show_in_menu' => false,
			'show_in_nav_menus' => false,
			'show_in_rest' => true,
			'supports' => array('title', 'editor', 'excerpt'),
			'hierarchical' => true,
			'has_archive' => false,
		));

		register_block_type('mf/customlists', array(
			'editor_script' => 'script_custom_lists_block_wp',
			'editor_style' => 'style_base_block_wp',
			'render_callback' => array($this, 'block_render_callback'),
		));
	}

	function admin_menu()
	{
		global $wpdb;

		$menu_start = "edit.php?post_type=".$this->post_type;
		$menu_capability = 'edit_pages';

		$menu_title = __("Custom Lists", 'lang_custom_lists');
		add_menu_page("", $menu_title, $menu_capability, $menu_start, '', 'dashicons-list-view', 21);

		$menu_title = __("Lists", 'lang_custom_lists');
		add_submenu_page($menu_start, $menu_title, $menu_title, $menu_capability, $menu_start);

		$menu_title = __("Add New", 'lang_custom_lists');
		add_submenu_page($menu_start, $menu_title, " - ".$menu_title, $menu_capability, "post-new.php?post_type=".$this->post_type);

		$wpdb->get_results($wpdb->prepare("SELECT ID FROM ".$wpdb->posts." WHERE post_type = %s AND post_status = %s LIMIT 0, 1", $this->post_type, 'publish'));

		if($wpdb->num_rows > 0)
		{
			$menu_title = __("Items", 'lang_custom_lists');
			add_submenu_page($menu_start, $menu_title, $menu_title, $menu_capability, "edit.php?post_type=".$this->post_type_item);

			$menu_title = __("Add New", 'lang_custom_lists');
			add_submenu_page($menu_start, $menu_title, " - ".$menu_title, $menu_capability, "post-new.php?post_type=".$this->post_type_item);
		}
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
					'name' => __("Read More", 'lang_custom_lists'),
					'id' => $this->meta_prefix.'read_more',
					'type' => 'select',
					'options' => get_yes_no_for_select(),
					'std' => 'no',
				),
				/*array(
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
				),*/
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

		$arr_data = [];
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

			$arr_data = [];
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

	function column_header($columns)
	{
		global $post_type;

		unset($columns['date']);

		switch($post_type)
		{
			case $this->post_type:
				$columns['items'] = __("Items", 'lang_custom_lists');
				$columns['style'] = __("Style", 'lang_custom_lists');
			break;

			case $this->post_type_item:
				$columns['list_id'] = __("List", 'lang_custom_lists');
			break;
		}

		return $columns;
	}

	function column_cell($column, $post_id)
	{
		global $wpdb, $post;

		switch($post->post_type)
		{
			case $this->post_type:
				switch($column)
				{
					case 'items':
						$item_amount = $wpdb->get_var($wpdb->prepare("SELECT COUNT(meta_value) FROM ".$wpdb->posts." INNER JOIN ".$wpdb->postmeta." ON ".$wpdb->posts.".ID = ".$wpdb->postmeta.".post_id WHERE post_status != %s AND meta_key = %s AND meta_value = '%d'", 'trash', $this->meta_prefix.'list_id', $post_id));

						echo "<a href='".admin_url("edit.php?post_type=".$this->post_type_item."&strFilterCustomList=".$post_id)."'>".$item_amount."</a>
						<div class='row-actions'>
							<a href='".admin_url("post-new.php?post_type=".$this->post_type_item."&list_id=".$post_id)."'>".__("Add New", 'lang_custom_lists')."</a>
						</div>";
					break;

					case 'style':
						$post_meta = get_post_meta($post_id, $this->meta_prefix.$column, true);

						if($post_meta != '')
						{
							$arr_styles = $this->get_styles_for_select();

							if(isset($arr_styles[$post_meta]))
							{
								echo $arr_styles[$post_meta];
							}

							else
							{
								echo "<em>(".$post_meta.")</em>";
							}
						}
					break;
				}
			break;

			case $this->post_type_item:
				switch($column)
				{
					case 'list_id':
						$out = "";

						$arr_parent_id = get_post_meta($post_id, $this->meta_prefix.$column, false);

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
			$result = $wpdb->get_results($wpdb->prepare("SELECT ID FROM ".$wpdb->posts." INNER JOIN ".$wpdb->postmeta." ON ".$wpdb->posts.".ID = ".$wpdb->postmeta.".post_id WHERE post_type = %s AND meta_key = '".$this->meta_prefix."list_id' AND meta_value = '%d' GROUP BY ID", $this->post_type_item, $post_id));

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

	function filter_is_file_used($arr_used)
	{
		global $wpdb;

		$result = $wpdb->get_results($wpdb->prepare("SELECT ID FROM ".$wpdb->posts." INNER JOIN ".$wpdb->postmeta." ON ".$wpdb->posts.".ID = ".$wpdb->postmeta.".post_id WHERE post_status = %s AND meta_key = %s AND meta_value = %s GROUP BY ID", 'publish', $this->meta_prefix.'image', $arr_used['id']));
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
}