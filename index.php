<?php
/*
Plugin Name: MF Custom Lists
Plugin URI: https://github.com/frostkom/mf_custom_lists
Description: 
Version: 3.1.1
Author: Martin Fors
Author URI: http://frostkom.se
Text Domain: lang_custom_lists
Domain Path: /lang

GitHub Plugin URI: frostkom/mf_custom_lists
*/

include_once("include/classes.php");
include_once("include/functions.php");

add_action('init', 'init_custom_lists', 1);
add_action('widgets_init', 'widgets_custom_lists');

if(is_admin())
{
	register_activation_hook(__FILE__, 'activate_custom_lists');
	register_uninstall_hook(__FILE__, 'uninstall_custom_lists');

	add_action('admin_menu', 'menu_custom_lists');
	add_action('rwmb_meta_boxes', 'meta_boxes_custom_lists');

	add_action('restrict_manage_posts', 'post_filter_select_custom_lists');
	add_action('pre_get_posts', 'post_filter_query_custom_lists');

	add_filter('manage_mf_custom_lists_posts_columns', 'column_header_custom_list', 5);
	add_action('manage_mf_custom_lists_posts_custom_column', 'column_cell_custom_list', 5, 2);

	add_filter('manage_mf_custom_item_posts_columns', 'column_header_custom_item', 5);
	add_action('manage_mf_custom_item_posts_custom_column', 'column_cell_custom_item', 5, 2);

	add_filter('count_shortcode_button', 'count_shortcode_button_custom_lists');
	add_filter('get_shortcode_output', 'get_shortcode_output_custom_lists');
	add_filter('get_shortcode_list', 'get_shortcode_list_custom_lists');

	$obj_custom_list = new mf_custom_list();

	add_action('post_updated', array($obj_custom_list, 'post_updated'), 10, 3);
}

else
{
	$obj_custom_list = new mf_custom_list();

	add_shortcode('mf_custom_list', array($obj_custom_list, 'render_shortcode'));
}

load_plugin_textdomain('lang_custom_lists', false, dirname(plugin_basename(__FILE__)).'/lang/');

function activate_custom_lists()
{
	require_plugin("meta-box/meta-box.php", "Meta Box");
}

function uninstall_custom_lists()
{
	mf_uninstall_plugin(array(
		'post_types' => array('mf_custom_lists', 'mf_custom_item'),
	));
}