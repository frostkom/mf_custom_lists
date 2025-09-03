<?php
/*
Plugin Name: MF Custom Lists
Plugin URI: https://github.com/frostkom/mf_custom_lists
Description:
Version: 3.10.10
Licence: GPLv2 or later
Author: Martin Fors
Author URI: https://martinfors.se
Text Domain: lang_custom_lists
Domain Path: /lang

Requires Plugins: meta-box
*/

if(!function_exists('is_plugin_active') || function_exists('is_plugin_active') && is_plugin_active("mf_base/index.php"))
{
	include_once("include/classes.php");

	$obj_custom_list = new mf_custom_list();

	add_action('cron_base', array($obj_custom_list, 'cron_base'), mt_rand(2, 10));

	add_action('enqueue_block_editor_assets', array($obj_custom_list, 'enqueue_block_editor_assets'));
	add_action('init', array($obj_custom_list, 'init'), 1);

	if(is_admin())
	{
		register_uninstall_hook(__FILE__, 'uninstall_custom_lists');

		add_action('admin_menu', array($obj_custom_list, 'admin_menu'));
		//add_action('admin_init', array($obj_custom_list, 'settings_custom_list'));

		add_filter('filter_sites_table_pages', array($obj_custom_list, 'filter_sites_table_pages'));

		add_filter('display_post_states', array($obj_custom_list, 'display_post_states'), 10, 2);

		add_action('rwmb_meta_boxes', array($obj_custom_list, 'rwmb_meta_boxes'));

		add_action('restrict_manage_posts', array($obj_custom_list, 'restrict_manage_posts'));
		add_action('pre_get_posts', array($obj_custom_list, 'pre_get_posts'));

		add_filter('manage_'.$obj_custom_list->post_type.'_posts_columns', array($obj_custom_list, 'column_header'), 5);
		add_action('manage_'.$obj_custom_list->post_type.'_posts_custom_column', array($obj_custom_list, 'column_cell'), 5, 2);

		add_filter('manage_'.$obj_custom_list->post_type_item.'_posts_columns', array($obj_custom_list, 'column_header'), 5);
		add_action('manage_'.$obj_custom_list->post_type_item.'_posts_custom_column', array($obj_custom_list, 'column_cell'), 5, 2);

		add_action('wp_trash_post', array($obj_custom_list, 'wp_trash_post'));

		add_filter('filter_last_updated_post_types', array($obj_custom_list, 'filter_last_updated_post_types'), 10, 2);
	}

	add_filter('filter_is_file_used', array($obj_custom_list, 'filter_is_file_used'));

	add_action('widgets_init', array($obj_custom_list, 'widgets_init'));

	load_plugin_textdomain('lang_custom_lists', false, dirname(plugin_basename(__FILE__))."/lang/");

	function uninstall_custom_lists()
	{
		include_once("include/classes.php");

		$obj_custom_list = new mf_custom_list();

		mf_uninstall_plugin(array(
			'post_types' => array($obj_custom_list->post_type, $obj_custom_list->post_type_item),
		));
	}
}