<?php

function init_custom_lists()
{
	$labels = array(
		'name' => _x(__('Custom Lists', 'lang_custom_lists'), 'post type general name'),
		'singular_name' => _x(__('Custom List', 'lang_custom_lists'), 'post type singular name'),
		'menu_name' => __("Custom Lists", 'lang_custom_lists')
	);

	$args = array(
		'labels' => $labels,
		'public' => true,
		'show_in_menu' => false,
		'supports' => array('title'), //, 'editor', 'custom-fields', 'page-attributes'
		'hierarchical' => false,
		'has_archive' => false,
	);

	register_post_type('mf_custom_lists', $args);

	$labels = array(
		'name' => _x(__('Items', 'lang_custom_lists'), 'post type general name'),
		'singular_name' => _x(__('Item', 'lang_custom_lists'), 'post type singular name'),
		'menu_name' => __("Items", 'lang_custom_lists')
	);

	$args = array(
		'labels' => $labels,
		'public' => true,
		'show_in_menu' => false,
		'supports' => array('title', 'editor', 'custom-fields'), //, 'page-attributes'
		'hierarchical' => true,
		'has_archive' => false,
	);

	register_post_type('mf_custom_item', $args);
}

function menu_custom_lists()
{
	$menu_root = 'mf_custom_lists/';
	//$menu_start = $menu_root.'list/index.php';
	$menu_start = "edit.php?post_type=mf_custom_lists";
	$menu_capability = "edit_pages";

	add_menu_page("", __("Custom Lists", 'lang_custom_lists'), $menu_capability, $menu_start, '', 'dashicons-list-view');

	add_submenu_page($menu_start, __("Lists", 'lang_custom_lists'), __("Lists", 'lang_custom_lists'), $menu_capability, 'edit.php?post_type=mf_custom_lists');
	add_submenu_page($menu_start, __("Items", 'lang_custom_lists'), __("Items", 'lang_custom_lists'), $menu_capability, 'edit.php?post_type=mf_custom_item');
}

function column_header_custom_list($cols)
{
	$cols['shortcode'] = __('Shortcode', 'lang_custom_lists');

	return $cols;
}

function column_cell_custom_list($col, $id)
{
	$meta_prefix = "mf_custom_lists_";

	switch($col)
	{
		case 'shortcode':
			$shortcode = "[mf_custom_list id=".$id."]";

			echo $shortcode
			."<div class='row-actions'>
				<a href='".admin_url("post-new.php?post_type=page&content=".$shortcode)."'>".__("Add new page", 'lang_custom_lists')."</a>
			</div>";
		break;
	}
}

function column_header_custom_item($cols)
{
	$cols['list_id'] = __('List', 'lang_custom_lists');

	return $cols;
}

function column_cell_custom_item($col, $id)
{
	$meta_prefix = "mf_custom_lists_";

	switch($col)
	{
		case 'list_id':
			$parent_id = get_post_meta($id, $meta_prefix."list_id", true);
			$parent_title = get_the_title($parent_id);

			echo $parent_title;
		break;
	}
}

function meta_boxes_custom_lists($meta_boxes)
{
	global $wpdb;

	$meta_prefix = "mf_custom_lists_";

	$meta_boxes[] = array(
		'id' => 'structure',
		'title' => __('Structure', 'lang_custom_lists'),
		'pages' => array('mf_custom_lists'),
		//'context' => 'side',
		'priority' => 'low',
		'fields' => array(
			array(
				'name' => __('Container', 'lang_custom_lists'),
				'id' => $meta_prefix."container",
				'type' => 'text',
				'std' => "<ul>[children]</ul>",
			),
			array(
				'name' => __('Items', 'lang_custom_lists'),
				'id' => $meta_prefix."items",
				'type' => 'textarea',
				'std' => "<li><h2><a href='[list_link]'>[list_title]</a></h2>[list_image]<p>[list_text]</p></li>",
			),
		)
	);

	$meta_boxes[] = array(
		'id' => 'settings',
		'title' => __('Settings', 'lang_custom_lists'),
		'pages' => array('mf_custom_item'),
		'context' => 'side',
		'priority' => 'low',
		'fields' => array(
			array(
				'name' => __('List', 'lang_custom_lists'),
				'id' => $meta_prefix."list_id",
				'type' => 'select',
				'options' => get_posts_for_select(array('post_type' => "mf_custom_lists")),
			),
			array(
				'name' => __('Image', 'lang_custom_lists'),
				'id' => $meta_prefix."image",
				'type' => 'thickbox_image',
			),
			array(
				'name' => __('Page', 'lang_custom_lists'),
				'id' => $meta_prefix."page",
				'type' => 'select',
				'options' => get_posts_for_select(array('optgroup' => false)),
				'attributes' => array(
					'condition_type' => 'show_if_empty',
					'condition_field' => $meta_prefix."link",
				),
			),
			array(
				'name' => __('External Link', 'lang_custom_lists'),
				'id' => $meta_prefix."link",
				'type' => 'url',
				'attributes' => array(
					'condition_type' => 'show_if_empty',
					'condition_field' => $meta_prefix."page",
				),
			),
		)
	);

	return $meta_boxes;
}

function meta_boxes_script_custom_lists()
{
	mf_enqueue_script('script_custom_lists_meta', plugin_dir_url(__FILE__)."script_meta.js");
}

function shortcode_scripts_custom_lists()
{
	global $post;

	if(is_single() && is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'mf_custom_list'))
	{
		wp_enqueue_style('style_custom_lists', plugin_dir_url(__FILE__)."style.css");
	}
}

function shortcode_custom_lists($atts)
{
	global $wpdb, $meta_prefix_cl;

	extract(shortcode_atts(array(
		'id' => ''
	), $atts));

	if(!is_single())
	{
		wp_enqueue_style('style_custom_lists', plugin_dir_url(__FILE__)."style.css");
	}

	$out = "";

	$meta_prefix_cl = "mf_custom_lists_";

	$result = $wpdb->get_results($wpdb->prepare("SELECT ID, post_excerpt, post_content FROM ".$wpdb->posts." WHERE post_type = 'mf_custom_lists' AND post_status = 'publish' AND ID = '%d'", $id));

	foreach($result as $r)
	{
		$parent_id = $r->ID;
		$parent_content = $r->post_content;
		$parent_excerpt = $r->post_excerpt;

		$parent_container = get_post_meta($parent_id, $meta_prefix_cl."container", true);
		$parent_items = get_post_meta($parent_id, $meta_prefix_cl."items", true);

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
			$out_children = "";

			$result2 = $wpdb->get_results($wpdb->prepare("SELECT ID, post_content FROM ".$wpdb->posts." INNER JOIN ".$wpdb->postmeta." ON ".$wpdb->posts.".ID = ".$wpdb->postmeta.".post_id AND meta_key = '".$meta_prefix_cl."list_id' WHERE post_type = 'mf_custom_item' AND post_status = 'publish' AND meta_value = '%d' ORDER BY menu_order ASC", $parent_id));

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
							$child_image = get_meta_image_url($child_id, $meta_prefix_cl."image");

							if($child_image != '')
							{
								$child_title = get_the_title($child_id);

								$out .= "<img src='".$child_image."' alt='".$child_title."'>";
							}
						}

						else if($match[1] == "list_link")
						{
							$child_page = get_post_meta($child_id, $meta_prefix_cl."page", true);

							if(intval($child_page) > 0)
							{
								$out .= get_permalink($child_page);
							}

							else
							{
								$child_link = get_post_meta($child_id, $meta_prefix_cl."link", true);

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
	}

	return apply_filters('the_content', $out);
}