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

$context['category_plugins'] = Timber::get_terms('category_plugins');
$count_category_plugins = wp_count_posts('napojeni')->publish;
$context['show_category_plugins'] = (bool) $count_category_plugins;

$context['breadcrumbs'] = array(
  $context['wp_title'] => $context['wp_title'],
);

Timber::render( 'page-napojeni.twig', $context );
