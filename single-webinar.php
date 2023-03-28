<?php

$context = Timber::get_context();
$post = Timber::query_post();

$context['post'] = $post;

$external_id = $post->get_field('external_id');
if ($external_id) {
  $context['external_id'] = $external_id;
  $context['og_image'] = get_youtube_thumbnail_url($external_id);
}

$context['breadcrumbs'][ __( 'Webináře', 'shp-partneri' ) ] = get_post_type_archive_link( 'webinar' );

if ($terms = $post->terms()) {
  $term = new Timber\Term( $terms[0] );
  $context['breadcrumbs'][ $term->name ] = $term->link;
}

$context['related_partners'] = [];
if ($related_partners = $post->get_field('related_partners')) {
  foreach ($related_partners as $partner_id) {
    $context['related_partners'][] = new Timber\Post($partner_id);
  }
}

if ($related_blog_posts = $post->get_field('related_blog_posts')) {
  $blog_posts_array = Shoptet\ShoptetExternal::get_blog_posts(['_embed' => 1, 'include' => $related_blog_posts]);
  $related_blog_posts = [];
  foreach($blog_posts_array as $post_array) {
    $related_blog_posts[] = [
      'title' => $post_array['title']['rendered'],
      'text' => $post_array['excerpt']['rendered'],
      'image_url' => isset($post_array['_embedded']['wp:featuredmedia'][0]['media_details']) ? $post_array['_embedded']['wp:featuredmedia'][0]['media_details']['sizes']['medium_large']['source_url'] : null,
      'url' => $post_array['link'],
    ];
  }
  $context['related_blog_posts'] = $related_blog_posts;
}

$context['breadcrumbs'][ truncate($post->title, 70) ] = $post->link;

$context['meta_description'] = $post->content;

Timber::render( 'single-webinar.twig', $context );
