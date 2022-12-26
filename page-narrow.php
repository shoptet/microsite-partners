<?php
/**
 * Template Name: Narrow
 */

$context = Timber::get_context();
$post = new TimberPost();
$context['post'] = $post;

Timber::render( 'page-narrow.twig', $context );
