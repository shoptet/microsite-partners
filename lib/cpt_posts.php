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


	/**
	 * Post Type: Poptávky.
	 */

	$labels = array(
		"name" => __( "Poptávky", "custom-post-type-ui" ),
		"singular_name" => __( "Poptávka", "custom-post-type-ui" ),
		"menu_name" => __( "Poptávky", "custom-post-type-ui" ),
		"all_items" => __( "Všechny poptávky", "custom-post-type-ui" ),
		"add_new" => __( "Přidat novou", "custom-post-type-ui" ),
		"add_new_item" => __( "Přidat novou poptávku", "custom-post-type-ui" ),
		"edit_item" => __( "Upravit poptávku", "custom-post-type-ui" ),
		"new_item" => __( "Nová poptávka", "custom-post-type-ui" ),
		"view_item" => __( "Zobrazit poptávku", "custom-post-type-ui" ),
		"view_items" => __( "Zobrazit poptávky", "custom-post-type-ui" ),
		"search_items" => __( "Vyhledat poptávku", "custom-post-type-ui" ),
	);

	$args = array(
		"label" => __( "Poptávky", "custom-post-type-ui" ),
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => true,
		"show_ui" => true,
		"delete_with_user" => false,
		"show_in_rest" => true,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"has_archive" => 'poptavky',
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"exclude_from_search" => true,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"rewrite" => array( "slug" => "poptavka", "with_front" => true ),
		"query_var" => true,
		"menu_icon" => "dashicons-clipboard",
		"supports" => array( "title", "editor", "revisions" ),
		"taxonomies" => array( "category_requests" ),
	);

	register_post_type( "request", $args );
}

add_action( 'init', 'cptui_register_my_cpts' );
