<?php
/**
 * Template Name: Partners
 */

global $wp_query;

$context = Timber::get_context();

$term = new TimberTerm();
$termTitle = ( $term->title ? $term->title : $term->name);

$context['category_professionals'] = Timber::get_terms('category_professionals');
$count_category_professionals = wp_count_posts('profesionalove')->publish;
$context['show_category_professionals'] = (bool) $count_category_professionals;

$context['breadcrumbs'] = array(
  $context['wp_title'] => $context['wp_title'],
);

Timber::render( 'page-profesionalove.twig', $context );
