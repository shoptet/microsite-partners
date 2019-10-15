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

include_once 'helpers.php';

$context = Timber::get_context();
$post = Timber::query_post();

$context['post'] = $post;

if ( $terms = $post->terms('category_requests') ) {
  $context['term'] = $terms[0];
}

Timber::render( 'single-request.twig', $context );
