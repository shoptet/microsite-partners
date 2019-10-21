<?php
/**
 * The Template for displaying all single posts
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */

$context = Timber::get_context();
$post = Timber::query_post();

$context['post'] = $post;

$context['breadcrumbs'][ __( 'Poptávky', 'shp-partneri' ) ] = get_post_type_archive_link( 'request' );

if ( $terms = $post->terms('category_requests') ) {
  $term = $terms[0];
  $context['term'] = $term;
  $context['breadcrumbs'][ $term->name ] = $term->link;
}

$context['breadcrumbs'][ $post->title ] = $post->link;


Timber::render( 'single-request.twig', $context );
