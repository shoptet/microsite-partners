<?php

function cptui_register_my_taxes() {

	/**
	 * Taxonomy: Kategorie profesionálů.
	 */

	$labels = array(
		'name' => __( 'Kategorie profesionálů', 'shp-partneri' ),
		'singular_name' => __( 'Kategorie profesionálů', 'shp-partneri' ),
		'menu_name' => __( 'Kategorie', 'shp-partneri' ),
		'add_new_item' => __( 'Přidat novou kategorii', 'shp-partneri' ),
	);

	$args = array(
		'label' => __( 'Kategorie profesionálů', 'shp-partneri' ),
		'labels' => $labels,
		'public' => true,
		'hierarchical' => true,
		'show_ui' => true,
		'show_in_menu' => true,
		'show_in_nav_menus' => true,
		'sort' => true,
		'query_var' => true,
		'rewrite' => array( 'slug' => __( 'profesionalove', 'shp-partneri' ), 'with_front' => true, ),
		'show_admin_column' => false,
		'show_in_rest' => false,
		'rest_base' => '',
		'show_in_quick_edit' => false,
	);
	register_taxonomy( 'category_professionals', array( 'profesionalove' ), $args );

	/**
	 * Taxonomy: Kategorie napojení.
	 */

	$labels = array(
		'name' => __( 'Kategorie napojení', 'shp-partneri' ),
		'singular_name' => __( 'Kategorie napojení', 'shp-partneri' ),
		'menu_name' => __( 'Kategorie', 'shp-partneri' ),
		'add_new_item' => __( 'Přidat novou kategorii', 'shp-partneri' ),
	);

	$args = array(
		'label' => __( 'Kategorie napojení', 'shp-partneri' ),
		'labels' => $labels,
		'public' => true,
		'hierarchical' => true,
		'label' => 'Kategorie napojení',
		'show_ui' => true,
		'show_in_menu' => true,
		'show_in_nav_menus' => true,
		'query_var' => true,
		'rewrite' => array( 'slug' => 'napojeni', 'with_front' => true, ),
		'show_admin_column' => false,
		'show_in_rest' => false,
		'rest_base' => '',
		'show_in_quick_edit' => false,
	);
	register_taxonomy( 'category_plugins', array( 'napojeni' ), $args );

	/**
	 * Taxonomy: Kategorie nástrojů.
	 */

	$labels = array(
		'name' => __( 'Kategorie nástrojů', 'shp-partneri' ),
		'singular_name' => __( 'Kategorie nástrojů', 'shp-partneri' ),
		'menu_name' => __( 'Kategorie', 'shp-partneri' ),
		'add_new_item' => __( 'Přidat novou kategorii', 'shp-partneri' ),
	);

	$args = array(
		'label' => __( 'Kategorie nástrojů', 'shp-partneri' ),
		'labels' => $labels,
		'public' => true,
		'hierarchical' => true,
		'label' => 'Kategorie nástrojů',
		'show_ui' => true,
		'show_in_menu' => true,
		'show_in_nav_menus' => true,
		'query_var' => true,
		'rewrite' => array( 'slug' => 'nastroje', 'with_front' => true, ),
		'show_admin_column' => false,
		'show_in_rest' => false,
		'rest_base' => '',
		'show_in_quick_edit' => false,
	);
	register_taxonomy( 'category_tools', array( 'nastroje' ), $args );

	/**
	 * Taxonomy: Kategorie poptávek.
	 */

	$labels = array(
		'name' => __( 'Kategorie poptávek', 'custom-post-type-ui' ),
		'singular_name' => __( 'Kategorie poptávek', 'custom-post-type-ui' ),
		'menu_name' => __( 'Kategorie', 'custom-post-type-ui' ),
		'all_items' => __( 'Všechny kategorie', 'custom-post-type-ui' ),
		'edit_item' => __( 'Upravit kategorii', 'custom-post-type-ui' ),
		'view_item' => __( 'Zobrazit kategorii', 'custom-post-type-ui' ),
		'update_item' => __( 'Upravit název kategorie', 'custom-post-type-ui' ),
		'add_new_item' => __( 'Přidat novou kategorii', 'custom-post-type-ui' ),
		'new_item_name' => __( 'Nové jméno kategorie', 'custom-post-type-ui' ),
	);

	$args = array(
		'label' => __( 'Kategorie poptávek', 'custom-post-type-ui' ),
		'labels' => $labels,
		'public' => true,
		'publicly_queryable' => true,
		'hierarchical' => false,
		'show_ui' => true,
		'show_in_menu' => true,
		'show_in_nav_menus' => true,
		'query_var' => true,
		'rewrite' => array( 'slug' => __( 'poptavky', 'shp-partneri' ), 'with_front' => true, ),
		'show_admin_column' => false,
		'show_in_rest' => false,
		'rest_base' => '',
		'show_in_quick_edit' => false,
	);
	register_taxonomy( 'category_requests', array( 'request' ), $args );
}

add_action( 'init', 'cptui_register_my_taxes' );
