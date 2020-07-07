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

$context['terms'] = Timber::get_terms( RequestPost::TAXONOMY );
$archive_link = get_post_type_archive_link( 'request' );
$context['breadcrumbs'][ __( 'Poptávky', 'shp-partneri' ) ] = $archive_link;
$context['pagination'] = Timber::get_pagination();

if( is_tax() ) {
  $term = new Timber\Term();
  $context['term'] = $term;
  $context['title'] = sprintf( __( '%s poptávky', 'shp-partneri' ), $term->name );
  $context['breadcrumbs'][ $term->name ] = $term->link;
  $context['canonical']['link'] = ($context['pagination']['current'] == 1) ? $term->link :  $term->link . __( 'strana', 'shp-partneri' ) . '/' . $context['pagination']['current'];
  $context['meta_description'] = $term->description;
  $context['description'] = $term->description;
} else {
  $context['title'] = __( 'Přehled poptávek', 'shp-partneri' );
  $context['canonical']['link'] = ($context['pagination']['current'] == 1) ? $archive_link : $archive_link . __( 'strana', 'shp-partneri' ) . '/' . $context['pagination']['current'];
  $context['description'] = __( 'Víte co potřebujete, ale nechcete hledat konkrétního Shoptet Partnera? Zadejte si poptávku zdarma a jen čekejte na první nabídky.', 'shp-partneri' );
  $context['meta_description'] = $context['description'];
}

$context['posts_count'] = $wp_query->post_count;
$context['posts_found'] = $wp_query->found_posts;

$context['order_choices'] = [
  'date_desc' => __( 'Nejnovějších', 'shp-partneri' ),
  'date_asc' => __( 'Nejstarších', 'shp-partneri' ),
];
$context['orderby'] = isset( $_GET[ 'orderby' ] ) ? $_GET[ 'orderby' ] : null;
$context['filterby'] = isset( $_GET[ 'filterby' ] ) ? $_GET[ 'filterby' ] : null;

Timber::render( 'archive-request.twig', $context );
