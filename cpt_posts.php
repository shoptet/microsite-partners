<?php

function cptui_register_my_cpts() {

	/**
	 * Post Type: Profesionálové.
	 */

	$labels = array(
		"name" => __( "Profesionálové", "" ),
		"singular_name" => __( "Profesionál", "" ),
		"menu_name" => __( "Profesionálové", "" ),
		"all_items" => __( "Vše", "" ),
		"add_new" => __( "Vytvořit", "" ),
		"add_new_item" => __( "Přidat nového profesionála", "" ),
		"edit_item" => __( "Upravit profil profesionála", "" ),
		"new_item" => __( "Nový profesionál", "" ),
		"view_item" => __( "Zobrazit profesionála", "" ),
		"view_items" => __( "Zobrazit profesionály", "" ),
		"search_items" => __( "Vyhledat profesionála", "" ),
		"not_found" => __( "Nebyl nalezen žádný profesionál", "" ),
	);

	$args = array(
		"label" => __( "Profesionálové", "" ),
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => false,
		"rest_base" => "",
		"has_archive" => false,
		"show_in_menu" => true,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"query_var" => true,
		"menu_position" => 25,
		"menu_icon" => "dashicons-businessman",
		"supports" => array( "title", "thumbnail", "comments" ),
	);

	register_post_type( "profesionalove", $args );

	/**
	 * Post Type: Napojení.
	 */

	$labels = array(
		"name" => __( "Napojení", "" ),
		"singular_name" => __( "Napojení", "" ),
		"menu_name" => __( "Napojení", "" ),
		"all_items" => __( "Vše", "" ),
		"add_new" => __( "Vytvořit", "" ),
		"add_new_item" => __( "Přidat nové napojení", "" ),
		"edit_item" => __( "Upravit profil napojení", "" ),
		"new_item" => __( "Nové napojení", "" ),
		"view_item" => __( "Zobrazit napojení", "" ),
		"view_items" => __( "Zobrazit napojení", "" ),
		"search_items" => __( "Vyhledat napojení", "" ),
		"not_found" => __( "Nebylo nazelezno žádné napojení", "" ),
	);

	$args = array(
		"label" => __( "Napojení", "" ),
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => false,
		"rest_base" => "",
		"has_archive" => false,
		"show_in_menu" => true,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"query_var" => true,
		"menu_position" => 26,
		"menu_icon" => "dashicons-admin-links",
		"supports" => array( "title", "thumbnail" ),
	);

	register_post_type( "napojeni", $args );

	/**
	 * Post Type: Nástroje.
	 */

	$labels = array(
		"name" => __( "Nástroje", "" ),
		"singular_name" => __( "Nástroj", "" ),
		"menu_name" => __( "Nástroje", "" ),
		"all_items" => __( "Vše", "" ),
		"add_new" => __( "Vytvořit", "" ),
		"add_new_item" => __( "Přidat nový nástroj", "" ),
		"edit_item" => __( "Upravit profil nástroje", "" ),
		"new_item" => __( "Nový nástroj", "" ),
		"view_item" => __( "Zobrazit nástroj", "" ),
		"view_items" => __( "Zobrazit nástroje", "" ),
		"search_items" => __( "Vyhledat nástroj", "" ),
		"not_found" => __( "Nebyl nalezen žádný nástroj", "" ),
	);

	$args = array(
		"label" => __( "Nástroje", "" ),
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => false,
		"rest_base" => "",
		"has_archive" => false,
		"show_in_menu" => true,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"query_var" => true,
		"menu_position" => 27,
		"menu_icon" => "dashicons-hammer",
		"supports" => array( "title", "thumbnail" ),
	);

	register_post_type( "nastroje", $args );
}

add_action( 'init', 'cptui_register_my_cpts' );
