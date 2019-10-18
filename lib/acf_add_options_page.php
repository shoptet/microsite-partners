<?php

if( function_exists('acf_add_options_page') ) {

	acf_add_options_sub_page(array(
		'page_title' 	=> 'Nastavení profesionálů',
		'menu_title'	=> 'Nastavení',
		'menu_slug' 	=> 'professional-settings',
		'capability'	=> 'edit_posts',
		'parent_slug' => 'edit.php?post_type=profesionalove',
	));

	acf_add_options_sub_page(array(
		'page_title' 	=> 'Nastavení napojení',
		'menu_title'	=> 'Nastavení',
		'menu_slug' 	=> 'plugin-settings',
		'capability'	=> 'edit_posts',
		'parent_slug' => 'edit.php?post_type=napojeni',
	));

	acf_add_options_sub_page(array(
		'page_title' 	=> 'Nastavení nástrojů',
		'menu_title'	=> 'Nastavení',
		'menu_slug' 	=> 'tool-settings',
		'capability'	=> 'edit_posts',
		'parent_slug' => 'edit.php?post_type=nastroje',
	));

	acf_add_options_sub_page(array(
		'page_title' 	=> 'Nastavení poptávek',
		'menu_title'	=> 'Nastavení',
		'menu_slug' 	=> 'requests-settings',
		'capability'	=> 'edit_posts',
		'parent_slug' => 'edit.php?post_type=request',
	));

	acf_add_options_page(array(
		'menu_title' 	=> 'Šablona',
		'menu_slug' 	=> 'theme-settings',
		'capability'	=> 'edit_posts',
		'position'    => 61,
		'icon_url'    => 'dashicons-welcome-widgets-menus',
	));

	acf_add_options_sub_page(array(
		'page_title' 	=> 'Nastavení šablony',
		'menu_title' 	=> 'Nastavení',
		'parent_slug' => 'theme-settings',
	));

	acf_add_options_sub_page(array(
		'page_title' 	=> 'Obecné správa',
		'menu_title' 	=> 'Obecné',
		'parent_slug' => 'theme-settings',
	));

	acf_add_options_sub_page(array(
		'page_title' 	=> 'Nastavení patičky webu',
		'menu_title' 	=> 'Patička',
		'parent_slug' => 'theme-settings',
	));

}
