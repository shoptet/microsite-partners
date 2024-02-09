<?php
/**
 * Template Name: Narrow with header
 */

$context = Timber::get_context();
$post = new TimberPost();
$context['post'] = $post;

Timber::render( 'page-narrow-header.twig', $context );
