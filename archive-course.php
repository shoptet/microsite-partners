<?php

if (!is_user_logged_in()) {
  auth_redirect();
}

global $wp_query;

$context = Timber::get_context();
$options = get_fields('options');

$context['posts'] = $posts = new Timber\PostQuery();

$terms = Timber::get_terms([
  'taxonomy' => 'category_courses',
  'orderby' => 'name',
  'order' => 'ASC',
  'hide_empty' => true,
]);

$context['terms'] = $terms;
$archive_link = get_post_type_archive_link( 'course' );
$context['breadcrumbs'][ __( 'Webináře', 'shp-partneri' ) ] = $archive_link;
$context['pagination'] = Timber::get_pagination(['mid_size'=>1]);

if( is_tax() ) {
  $term = new Timber\Term();
  $context['term'] = $term;
  $context['title'] = sprintf( __( '%s webináře', 'shp-partneri' ), $term->name );
  $context['breadcrumbs'][ $term->name ] = $term->link;
  $context['canonical']['link'] = ($context['pagination']['current'] == 1) ? $term->link :  $term->link . __( 'strana', 'shp-partneri' ) . '/' . $context['pagination']['current'] . '/';
  $context['meta_description'] = $term->description;
  $context['description'] = $term->description;
} else {
  $context['title'] = __( 'Webináře', 'shp-partneri' );
  $context['canonical']['link'] = ($context['pagination']['current'] == 1) ? $archive_link : $archive_link . __( 'strana', 'shp-partneri' ) . '/' . $context['pagination']['current'] . '/';
  $context['description'] = isset($options['webinar_archive_description']) ? $options['webinar_archive_description'] : '' ;
  $context['meta_description'] = $context['description'];
}

$context['posts_count'] = $wp_query->post_count;
$context['posts_found'] = $wp_query->found_posts;

Timber::render( 'archive-course.twig', $context );