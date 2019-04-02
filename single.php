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

$rating_stars = [];
for ( $i = 1; $i <= 5; $i++ ) {
	$rating_stars[$i] = 0;
}

// Count comments by rating
foreach ( $post->comments as $comment ) {
	if ( $rating = get_comment_meta( $comment->comment_ID, 'rating', true ) ) {
		$rating = intval( $rating );
		$rating_stars[ $rating ]++;
	}
}

$context['rating_stars'] =  $rating_stars;

$rating_sum = 0;
for ( $i = 1; $i <= 5; $i++ ) {
	$rating_sum += ( $i * $rating_stars[$i] );
}

if ( $comment_count = count( $post->comments ) ) {
	$average_rating = round( $rating_sum / $comment_count );
} else {
	$average_rating = 0;
}

$context['average_rating'] = $average_rating;

Timber::render( 'single.twig', $context );
