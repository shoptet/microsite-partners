<?php
/**
 * Template Name: Affiliate
 */

$context = Timber::get_context();
$post = new TimberPost();
$context['post'] = $post;

Timber::render( 'page-affiliate.twig', $context );
