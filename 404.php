<?php
/**
 * The template for displaying 404 pages (Not Found)
 *
 * Methods for TimberHelper can be found in the /functions sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */

$context = Timber::get_context();

$page_title = __( 'Page not found' );

$context['breadcrumbs'] = array(
	$page_title => '',
);

Timber::render( '404.twig', $context );
