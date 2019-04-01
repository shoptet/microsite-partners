<?php

function cptui_register_my_taxes() {

	/**
	 * Taxonomy: Kategorie profesionálů.
	 */

	$labels = array(
		"name" => __( "Kategorie profesionálů", "" ),
		"singular_name" => __( "Kategorie profesionálů", "" ),
		"menu_name" => __( "Kategorie", "" ),
		"add_new_item" => __( "Přidat novou kategorii", "" ),
	);

	$args = array(
		"label" => __( "Kategorie profesionálů", "" ),
		"labels" => $labels,
		"public" => true,
		"hierarchical" => true,
		"label" => "Kategorie profesionálů",
		"show_ui" => true,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"sort" => true,
		"query_var" => true,
		"rewrite" => array( 'slug' => 'profesionalove', 'with_front' => true, ),
		"show_admin_column" => false,
		"show_in_rest" => false,
		"rest_base" => "",
		"show_in_quick_edit" => false,
	);
	register_taxonomy( "category_professionals", array( "profesionalove" ), $args );

	/**
	 * Taxonomy: Kategorie napojení.
	 */

	$labels = array(
		"name" => __( "Kategorie napojení", "" ),
		"singular_name" => __( "Kategorie napojení", "" ),
		"menu_name" => __( "Kategorie", "" ),
		"add_new_item" => __( "Přidat novou kategorii", "" ),
	);

	$args = array(
		"label" => __( "Kategorie napojení", "" ),
		"labels" => $labels,
		"public" => true,
		"hierarchical" => true,
		"label" => "Kategorie napojení",
		"show_ui" => true,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"query_var" => true,
		"rewrite" => array( 'slug' => 'napojeni', 'with_front' => true, ),
		"show_admin_column" => false,
		"show_in_rest" => false,
		"rest_base" => "",
		"show_in_quick_edit" => false,
	);
	register_taxonomy( "category_plugins", array( "napojeni" ), $args );

	/**
	 * Taxonomy: Kategorie nástrojů.
	 */

	$labels = array(
		"name" => __( "Kategorie nástrojů", "" ),
		"singular_name" => __( "Kategorie nástrojů", "" ),
		"menu_name" => __( "Kategorie", "" ),
		"add_new_item" => __( "Přidat novou kategorii", "" ),
	);

	$args = array(
		"label" => __( "Kategorie nástrojů", "" ),
		"labels" => $labels,
		"public" => true,
		"hierarchical" => true,
		"label" => "Kategorie nástrojů",
		"show_ui" => true,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"query_var" => true,
		"rewrite" => array( 'slug' => 'nastroje', 'with_front' => true, ),
		"show_admin_column" => false,
		"show_in_rest" => false,
		"rest_base" => "",
		"show_in_quick_edit" => false,
	);
	register_taxonomy( "category_tools", array( "nastroje" ), $args );
}

add_action( 'init', 'cptui_register_my_taxes' );
