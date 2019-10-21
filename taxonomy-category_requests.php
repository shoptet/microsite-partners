<?php
/**
 * The template for displaying Archive pages.
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since   Timber 0.2
 */

global $wp_query;

$context = Timber::get_context();

$term = new Timber\Term();

$context['terms'] = Timber::get_terms('category_requests', [
  'hide_empty' => true,
]);

$context['posts'] = new Timber\PostQuery( [
  'post_type' => 'request',
  'posts_per_page' => -1,
  'tax_query' => [
    [
      'taxonomy' => 'category_requests',
      'terms' => $term->id,
    ],
  ],
] );

$context['breadcrumbs'][ __( 'Poptávky', 'shp-partneri' ) ] = get_post_type_archive_link( 'request' );
$context['breadcrumbs'][ $term->name ] = $term->link;

$context['title'] = $term->name . __( ' poptávky', 'shp-partneri' );

$context['term'] = $term;

$context['pagination'] = Timber::get_pagination();

$context['canonical']['link'] = ($context['pagination']['current'] == 1) ? $term->link :  $term->link . 'strana/' . $context['pagination']['current'];

$context['meta_description'] = $term->description;
$context['description'] = $term->description;

Timber::render( 'archive-request.twig', $context );
