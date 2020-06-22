<?php

if( function_exists('acf_add_options_page') ) {

	acf_add_options_sub_page(array(
		'page_title' 	=> __( 'Nastavení profesionálů', 'shp-partneri' ),
		'menu_title'	=> __( 'Nastavení', 'shp-partneri' ),
		'menu_slug' 	=> 'professional-settings',
		'capability'	=> 'edit_posts',
		'parent_slug' => 'edit.php?post_type=profesionalove',
	));

	acf_add_options_sub_page(array(
		'page_title' 	=> __( 'Nastavení napojení', 'shp-partneri' ),
		'menu_title'	=> __( 'Nastavení', 'shp-partneri' ),
		'menu_slug' 	=> 'plugin-settings',
		'capability'	=> 'edit_posts',
		'parent_slug' => 'edit.php?post_type=napojeni',
	));

	acf_add_options_sub_page(array(
		'page_title' 	=> __( 'Nastavení nástrojů', 'shp-partneri' ),
		'menu_title'	=> __( 'Nastavení', 'shp-partneri' ),
		'menu_slug' 	=> 'tool-settings',
		'capability'	=> 'edit_posts',
		'parent_slug' => 'edit.php?post_type=nastroje',
	));

	acf_add_options_sub_page(array(
		'page_title' 	=> __( 'Nastavení mailingu poptávek', 'shp-partneri' ),
		'menu_title'	=> __( 'Mailing', 'shp-partneri' ),
		'menu_slug' 	=> 'requests-mailing',
		'capability'	=> 'edit_posts',
		'parent_slug' => 'edit.php?post_type=request',
	));

	acf_add_options_page(array(
		'menu_title' 	=> __( 'Šablona', 'shp-partneri' ),
		'menu_slug' 	=> 'theme-settings',
		'capability'	=> 'edit_posts',
		'position'    => 61,
		'icon_url'    => 'dashicons-welcome-widgets-menus',
	));

	acf_add_options_sub_page(array(
		'page_title' 	=> __( 'Nastavení šablony', 'shp-partneri' ),
		'menu_title' 	=> __( 'Nastavení', 'shp-partneri' ),
		'parent_slug' => 'theme-settings',
	));

	acf_add_options_sub_page(array(
		'page_title' 	=> __( 'Obecná správa', 'shp-partneri' ),
		'menu_title' 	=> __( 'Obecné', 'shp-partneri' ),
		'parent_slug' => 'theme-settings',
	));

	acf_add_options_sub_page(array(
		'page_title' 	=> __( 'Nastavení patičky webu', 'shp-partneri' ),
		'menu_title' 	=> __( 'Patička', 'shp-partneri' ),
		'parent_slug' => 'theme-settings',
	));

}
