<?php
global $wp_query;
$wp_query->set_404();
status_header(404);
nocache_headers();

$context = Timber::get_context();
$page_title = __( 'Stránka nenalezena', 'shp-partneri' );

$context['breadcrumbs'] = array(
    $page_title => '',
);

Timber::render('404.twig', $context);
exit;