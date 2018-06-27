<?php

function widgets_custom_lists()
{
	register_widget('widget_custom_lists');
}

function count_shortcode_button_custom_lists($count)
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

function get_shortcode_output_custom_lists($out)
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

function get_shortcode_list_custom_lists($data)
{
	global $wpdb;

	$meta_prefix = "mf_custom_lists_";

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

				$result = $wpdb->get_results($wpdb->prepare("SELECT post_id FROM ".$wpdb->postmeta." WHERE meta_key = %s AND meta_value = '%d'", $meta_prefix.'list_id', $list_id));

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

function init_custom_lists()
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

function menu_custom_lists()
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

function post_filter_select_custom_lists()
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

function post_filter_query_custom_lists($wp_query)
{
    global $post_type, $pagenow;

	$meta_prefix = "mf_custom_lists_";

    if($pagenow == 'edit.php' && $post_type == 'mf_custom_item')
	{
		//$strFilterCustomList = get_or_set_table_filter(array('key' => 'strFilterCustomList'));
		$strFilterCustomList = check_var('strFilterCustomList');

		if($strFilterCustomList != '')
		{
			$wp_query->query_vars['meta_query'] = array(
				array(
					'key' => $meta_prefix.'list_id',
					'value' => $strFilterCustomList,
					'compare' => '=',
				),
			);
		}
	}
}

function column_header_custom_list($cols)
{
	unset($cols['date']);

	$cols['items'] = __("Items", 'lang_custom_lists');
	$cols['shortcode'] = __("Shortcode", 'lang_custom_lists');
	$cols['date'] = __("Date", 'lang_custom_lists');

	return $cols;
}

function column_cell_custom_list($col, $id)
{
	global $wpdb;

	$meta_prefix = "mf_custom_lists_";

	switch($col)
	{
		case 'items':
			$item_amount = $wpdb->get_var($wpdb->prepare("SELECT COUNT(meta_value) FROM ".$wpdb->postmeta." WHERE meta_key = %s AND meta_value = '%d'", $meta_prefix.'list_id', $id));

			echo "<a href='".admin_url("edit.php?post_type=mf_custom_item&strFilterCustomList=".$id)."'>".$item_amount."</a>
			<div class='row-actions'>
				<a href='".admin_url("post-new.php?post_type=mf_custom_item&list_id=".$id)."'>".__("Add New", 'lang_custom_lists')."</a>
			</div>";
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

function column_header_custom_item($cols)
{
	unset($cols['date']);

	$cols['list_id'] = __("List", 'lang_custom_lists');
	$cols['date'] = __("Date", 'lang_custom_lists');

	return $cols;
}

function column_cell_custom_item($col, $id)
{
	$meta_prefix = "mf_custom_lists_";

	switch($col)
	{
		case 'list_id':
			$parent_id = get_post_meta($id, $meta_prefix.'list_id', true);
			$parent_title = get_the_title($parent_id);

			$edit_url = "post.php?post=".$parent_id."&action=edit";

			echo "<a href='".$edit_url."'>".$parent_title."</a>
			<div class='row-actions'>
				<span class='edit'><a href='".$edit_url."'>".__("Edit", 'lang_custom_lists')."</a></span>
			</div>";
		break;
	}
}

function get_order_for_select()
{
	return array(
		'numerical' => __("Numerical", 'lang_custom_lists'),
		'alphabetic' => __("Alphabetical", 'lang_custom_lists'),
		'random' => __("Random", 'lang_custom_lists'),
	);
}