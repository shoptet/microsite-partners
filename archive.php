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

$post_query_args = array_merge( $wp_query->query_vars, array(
  'posts_per_page' => '11',
  'meta_key' => 'verifiedLevel',
  'orderby' => array(
      'verifiedLevel' => 'DESC',
      'title'    => 'ASC'
    )
));

$context = Timber::get_context();

$term = new TimberTerm();
$termTitle = ( $term->title ? $term->title : $term->name);

$context['term'] = $term;
$context['term_title'] = $termTitle;
$context['posts'] = $posts = new Timber\PostQuery($post_query_args);
$context['wp_title'] = $termTitle;
$context['breadcrumbs'] = array(
  $termTitle => $term->link );

$context['pagination'] = Timber::get_pagination();

$context['canonical']['link'] = ($context['pagination']['current'] == 1) ? $term->link :  $term->link . 'strana/' . $context['pagination']['current'];

$context['meta_description'] = $term->description;

//print_r($context);

Timber::render( 'archive.twig', $context );
