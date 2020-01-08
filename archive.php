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

$context['terms'] = Timber::get_terms( ProfessionalPost::TAXONOMY );

$context['regions_by_country'] = get_regions_by_country( ProfessionalPost::POST_TYPE );
$context['checked_regions'] = ( isset($_GET[ 'region' ]) && is_array($_GET[ 'region' ]) ) ? $_GET[ 'region' ] : [];
$context['blank_template_creator'] = isset( $_GET[ 'blank_template_creator' ] ) ? $_GET[ 'blank_template_creator' ] : null;
$context['term'] = $term;
$context['term_title'] = $termTitle;
$context['posts'] = $posts = new Timber\PostQuery();
$context['wp_title'] = $termTitle;
$context['breadcrumbs'] = array(
  $termTitle => $term->link );

$context['pagination'] = Timber::get_pagination();

$context['canonical']['link'] = ($context['pagination']['current'] == 1) ? $term->link :  $term->link . 'strana/' . $context['pagination']['current'];

$context['meta_description'] = $term->description;

Timber::render( 'archive.twig', $context );
