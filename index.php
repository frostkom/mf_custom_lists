<?php
/*
Plugin Name: MF Custom Lists
Plugin URI: https://github.com/frostkom/mf_custom_lists
Description: 
Version: 1.5.3
Author: Martin Fors
Author URI: http://frostkom.se
Text Domain: lang_custom_lists
Domain Path: /lang

GitHub Plugin URI: frostkom/mf_custom_lists
*/

include_once("include/functions.php");

add_action('init', 'init_custom_lists');

if(is_admin())
{
	register_activation_hook(__FILE__, 'activate_custom_lists');

	add_action('admin_menu', 'menu_custom_lists');
	add_action('rwmb_meta_boxes', 'meta_boxes_custom_lists');
	add_action('rwmb_enqueue_scripts', 'meta_boxes_script_custom_lists');

	add_filter('manage_mf_custom_lists_posts_columns', 'column_header_custom_list', 5);
	add_action('manage_mf_custom_lists_posts_custom_column', 'column_cell_custom_list', 5, 2);

	add_filter('manage_mf_custom_item_posts_columns', 'column_header_custom_item', 5);
	add_action('manage_mf_custom_item_posts_custom_column', 'column_cell_custom_item', 5, 2);
}

else
{
	add_shortcode('mf_custom_list', 'shortcode_custom_lists');
}


load_plugin_textdomain('lang_custom_lists', false, dirname(plugin_basename(__FILE__)).'/lang/');

function activate_custom_lists()
{
	require_plugin("meta-box/meta-box.php", "Meta Box");
}