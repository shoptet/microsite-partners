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

$term = new TimberTerm();
$termTitle = ( $term->title ? $term->title : $term->name);

$context['category_tools'] = Timber::get_terms('category_tools');
$count_category_tools = wp_count_posts('nastroje')->publish;
$context['show_category_tools'] = (bool) $count_category_tools;

$context['hero_text'] = $hero_text;

$context['meta_description'] = strip_tags($hero_text);
$context['breadcrumbs'] = array(
  $context['wp_title'] => $context['wp_title'],
);

Timber::render( 'page-nastroje.twig', $context );
