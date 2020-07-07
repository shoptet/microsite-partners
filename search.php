<?php
/**
 * Search results page
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since   Timber 0.1
 */

global $wp_query;

$context = Timber::get_context();

$query = get_search_query();
$posts = Timber::get_posts();
$posts_count = $wp_query->found_posts;

$terms_args = array(
  'name__like' => $query,
  'hide_empty' => true
);

$terms_professionals = Timber::get_terms(array_merge( $terms_args, array(
  'taxonomy' => 'category_professionals'
)));

$terms_plugins = Timber::get_terms(array_merge( $terms_args, array(
  'taxonomy' => 'category_plugins'
)));

$terms_tools = Timber::get_terms(array_merge( $terms_args, array(
  'taxonomy' => 'category_tools'
)));

$context['wp_title'] = sprintf( __( '%s  – Výsledky vyhledávání', 'shp-partneri' ), $query );
$context['query'] = $query;

if( empty($posts) && empty($terms_professionals) && empty($terms_plugins) && empty($terms_tools) ) {
  $context['has_results'] = false;
}else {
  $context['has_results'] = true;
}

$context['posts'] = $posts;
$context['posts_count'] = $posts_count;
$context['terms_professionals'] = $terms_professionals;
$context['terms_plugins'] = $terms_plugins;
$context['terms_tools'] = $terms_tools;

$context['pagination'] = Timber::get_pagination();

$context['breadcrumbs'] = array(
  __( 'Výsledky vyhledávání', 'shp-partneri' ) => ''
);

Timber::render( 'search.twig', $context );
