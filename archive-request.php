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

$context['posts'] = $posts = new Timber\PostQuery();

$context['terms'] = Timber::get_terms('category_requests', [
  'hide_empty' => true,
]);

$archive_link = get_post_type_archive_link( 'request' );

$context['breadcrumbs'][ __( 'Poptávky', 'shp-partneri' ) ] = $archive_link;

$context['pagination'] = Timber::get_pagination();

$context['canonical']['link'] = ($context['pagination']['current'] == 1) ? $archive_link : $archive_link . 'strana/' . $context['pagination']['current'];

$context['meta_description'] = '';

$context['description'] = __( 'Víte co potřebujete, ale nechcete hledat konkrétního Shoptet Partnera? Zadejte si poptávku zdarma a jen čekejte na první nabídky.', 'shp-partneri' );

Timber::render( 'archive-request.twig', $context );
