<?php
/*
Plugin Name: MF Custom Lists
Plugin URI: https://github.com/frostkom/mf_custom_lists
Description: 
Version: 3.6.14
Licence: GPLv2 or later
Author: Martin Fors
Author URI: https://frostkom.se
Text Domain: lang_custom_lists
Domain Path: /lang

Depends: Meta Box, MF Base
GitHub Plugin URI: frostkom/mf_custom_lists
*/

if(function_exists('is_plugin_active') && is_plugin_active("mf_base/index.php"))
{
	include_once("include/classes.php");

	$obj_custom_list = new mf_custom_list();

	add_action('init', array($obj_custom_list, 'init'), 1);

	if(is_admin())
	{
		register_activation_hook(__FILE__, 'activate_custom_lists');
		register_uninstall_hook(__FILE__, 'uninstall_custom_lists');

		add_action('admin_menu', array($obj_custom_list, 'admin_menu'));
		add_action('rwmb_meta_boxes', array($obj_custom_list, 'rwmb_meta_boxes'));

		add_action('restrict_manage_posts', array($obj_custom_list, 'restrict_manage_posts'));
		add_action('pre_get_posts', array($obj_custom_list, 'pre_get_posts'));

		add_filter('manage_'.$obj_custom_list->post_type.'_posts_columns', array($obj_custom_list, 'column_header'), 5);
		add_action('manage_'.$obj_custom_list->post_type.'_posts_custom_column', array($obj_custom_list, 'column_cell'), 5, 2);

		add_filter('manage_'.$obj_custom_list->post_type_item.'_posts_columns', array($obj_custom_list, 'column_header'), 5);
		add_action('manage_'.$obj_custom_list->post_type_item.'_posts_custom_column', array($obj_custom_list, 'column_cell'), 5, 2);

		add_filter('count_shortcode_button', array($obj_custom_list, 'count_shortcode_button'));
		add_filter('get_shortcode_output', array($obj_custom_list, 'get_shortcode_output'));
		add_filter('get_shortcode_list', array($obj_custom_list, 'get_shortcode_list'));

		add_action('wp_trash_post', array($obj_custom_list, 'wp_trash_post'));

		add_filter('filter_last_updated_post_types', array($obj_custom_list, 'filter_last_updated_post_types'), 10, 2);
	}

	else
	{
		add_action('wp_head', array($obj_custom_list, 'wp_head'), 0);

		add_shortcode('mf_custom_list', array($obj_custom_list, 'shortcode_custom_list'));
	}

	add_filter('filter_is_file_used', array($obj_custom_list, 'filter_is_file_used'));

	add_action('widgets_init', array($obj_custom_list, 'widgets_init'));

	load_plugin_textdomain('lang_custom_lists', false, dirname(plugin_basename(__FILE__))."/lang/");

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
}