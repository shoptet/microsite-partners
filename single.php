<?php
/**
 * The Template for displaying all single posts
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */

include_once 'helpers.php';

$context = Timber::get_context();
$post = Timber::query_post();

$context['post'] = $post;
$context['terms'] = Helpers\get_post_terms($post);
$context['similar_posts'] = Helpers\get_similiar_posts($post, 3);

$context['description_title'] = Helpers\get_post_description_title($post);
$context['benefit_title'] = Helpers\get_post_benefit_title($post);

$context['breadcrumbs'] = array(
	$post->title => $post->link,
);

$context['meta_description'] = $post->description;

Timber::render( 'single.twig', $context );
