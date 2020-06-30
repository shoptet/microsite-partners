<?php

function cptui_register_my_cpts() {

	/**
	 * Post Type: Profesionálové.
	 */

	$labels = array(
		'name' => __( 'Profesionálové', 'shp-partneri' ),
		'singular_name' => __( 'Profesionál', 'shp-partneri' ),
		'menu_name' => __( 'Profesionálové', 'shp-partneri' ),
		'all_items' => __( 'Vše', 'shp-partneri' ),
		'add_new' => __( 'Vytvořit', 'shp-partneri' ),
		'add_new_item' => __( 'Přidat nového profesionála', 'shp-partneri' ),
		'edit_item' => __( 'Upravit profil profesionála', 'shp-partneri' ),
		'new_item' => __( 'Nový profesionál', 'shp-partneri' ),
		'view_item' => __( 'Zobrazit profesionála', 'shp-partneri' ),
		'view_items' => __( 'Zobrazit profesionály', 'shp-partneri' ),
		'search_items' => __( 'Vyhledat profesionála', 'shp-partneri' ),
		'not_found' => __( 'Nebyl nalezen žádný profesionál', 'shp-partneri' ),
	);

	$args = array(
		'label' => __( 'Profesionálové', 'shp-partneri' ),
		'labels' => $labels,
		'description' => '',
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true,
		'show_in_rest' => false,
		'rest_base' => '',
		'has_archive' => false,
		'show_in_menu' => true,
		'exclude_from_search' => false,
		'capability_type' => 'post',
		'map_meta_cap' => true,
		'hierarchical' => false,
		'rewrite' => array( 'slug' => __( 'profesionalove', 'shp-partneri' ) ),
		'query_var' => true,
		'menu_position' => 25,
		'menu_icon' => 'dashicons-businessman',
		'supports' => array( 'title', 'thumbnail', 'comments' ),
	);

	register_post_type( 'profesionalove', $args );

	/**
	 * Post Type: Napojení.
	 */

	$labels = array(
		'name' => __( 'Napojení', 'shp-partneri' ),
		'singular_name' => __( 'Napojení', 'shp-partneri' ),
		'menu_name' => __( 'Napojení', 'shp-partneri' ),
		'all_items' => __( 'Vše', 'shp-partneri' ),
		'add_new' => __( 'Vytvořit', 'shp-partneri' ),
		'add_new_item' => __( 'Přidat nové napojení', 'shp-partneri' ),
		'edit_item' => __( 'Upravit profil napojení', 'shp-partneri' ),
		'new_item' => __( 'Nové napojení', 'shp-partneri' ),
		'view_item' => __( 'Zobrazit napojení', 'shp-partneri' ),
		'view_items' => __( 'Zobrazit napojení', 'shp-partneri' ),
		'search_items' => __( 'Vyhledat napojení', 'shp-partneri' ),
		'not_found' => __( 'Nebylo nazelezno žádné napojení', 'shp-partneri' ),
	);

	$args = array(
		'label' => __( 'Napojení', 'shp-partneri' ),
		'labels' => $labels,
		'description' => '',
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true,
		'show_in_rest' => false,
		'rest_base' => '',
		'has_archive' => false,
		'show_in_menu' => false,
		'exclude_from_search' => false,
		'capability_type' => 'post',
		'map_meta_cap' => true,
		'hierarchical' => false,
		'query_var' => true,
		'menu_position' => 26,
		'menu_icon' => 'dashicons-admin-links',
		'supports' => array( 'title', 'thumbnail' ),
	);

	register_post_type( 'napojeni', $args );

	/**
	 * Post Type: Nástroje.
	 */

	$labels = array(
		'name' => __( 'Nástroje', 'shp-partneri' ),
		'singular_name' => __( 'Nástroj', 'shp-partneri' ),
		'menu_name' => __( 'Nástroje', 'shp-partneri' ),
		'all_items' => __( 'Vše', 'shp-partneri' ),
		'add_new' => __( 'Vytvořit', 'shp-partneri' ),
		'add_new_item' => __( 'Přidat nový nástroj', 'shp-partneri' ),
		'edit_item' => __( 'Upravit profil nástroje', 'shp-partneri' ),
		'new_item' => __( 'Nový nástroj', 'shp-partneri' ),
		'view_item' => __( 'Zobrazit nástroj', 'shp-partneri' ),
		'view_items' => __( 'Zobrazit nástroje', 'shp-partneri' ),
		'search_items' => __( 'Vyhledat nástroj', 'shp-partneri' ),
		'not_found' => __( 'Nebyl nalezen žádný nástroj', 'shp-partneri' ),
	);

	$args = array(
		'label' => __( 'Nástroje', 'shp-partneri' ),
		'labels' => $labels,
		'description' => '',
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true,
		'show_in_rest' => false,
		'rest_base' => '',
		'has_archive' => false,
		'show_in_menu' => false,
		'exclude_from_search' => false,
		'capability_type' => 'post',
		'map_meta_cap' => true,
		'hierarchical' => false,
		'query_var' => true,
		'menu_position' => 27,
		'menu_icon' => 'dashicons-hammer',
		'supports' => array( 'title', 'thumbnail' ),
	);

	register_post_type( 'nastroje', $args );


	/**
	 * Post Type: Poptávky.
	 */

	$labels = array(
		'name' => __( 'Poptávky', 'shp-partneri' ),
		'singular_name' => __( 'Poptávka', 'shp-partneri' ),
		'menu_name' => __( 'Poptávky', 'shp-partneri' ),
		'all_items' => __( 'Všechny poptávky', 'shp-partneri' ),
		'add_new' => __( 'Přidat novou', 'shp-partneri' ),
		'add_new_item' => __( 'Přidat novou poptávku', 'shp-partneri' ),
		'edit_item' => __( 'Upravit poptávku', 'shp-partneri' ),
		'new_item' => __( 'Nová poptávka', 'shp-partneri' ),
		'view_item' => __( 'Zobrazit poptávku', 'shp-partneri' ),
		'view_items' => __( 'Zobrazit poptávky', 'shp-partneri' ),
		'search_items' => __( 'Vyhledat poptávku', 'shp-partneri' ),
	);

	$args = array(
		'label' => __( 'Poptávky', 'shp-partneri' ),
		'labels' => $labels,
		'description' => '',
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true,
		'delete_with_user' => false,
		'show_in_rest' => true,
		'rest_base' => '',
		'rest_controller_class' => 'WP_REST_Posts_Controller',
		'has_archive' => 'poptavky',
		'show_in_menu' => true,
		'show_in_nav_menus' => true,
		'exclude_from_search' => false,
		'capability_type' => 'post',
		'map_meta_cap' => true,
		'hierarchical' => false,
		'rewrite' => array( 'slug' => __( 'poptavka', 'shp-partneri' ) ),
		'query_var' => true,
		'menu_icon' => 'dashicons-clipboard',
		'supports' => array( 'title', 'editor', 'revisions' ),
	);

	register_post_type( 'request', $args );
}

add_action( 'init', 'cptui_register_my_cpts' );
