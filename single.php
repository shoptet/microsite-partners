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
$context['terms'] = get_post_terms($post);
$context['similar_posts'] = get_similiar_posts($post, 3);

$context['description_title'] = get_post_description_title($post);
$context['benefit_title'] = get_post_benefit_title($post);

$context['breadcrumbs'] = array(
	$post->title => $post->link,
);

$context['meta_description'] = $post->description;

$rating_stars = [];
for ( $i = 1; $i <= 5; $i++ ) {
	$rating_stars[$i] = 0;
}

$comments_count = count($post->comments);
$comments_per_page = intval(get_option('comments_per_page'));
$comments_total_pages = ceil( $comments_count / $comments_per_page );
$query_cpage = intval(get_query_var('cpage'));
$comments_current_page = ( $query_cpage ? $query_cpage : 1 );
$comments_offset = ( $comments_per_page * ( $comments_current_page - 1 ) );

$pagination = [
	'total' => $comments_total_pages,
	'pages' => [],
	'prev' => null,
	'next' => null,
];

for ( $i = 1; $i <= $comments_total_pages; $i++ ) {
	$pagination['pages'][] = [
		'link' => ( $i > 1 ? $post->link . 'comment-page-' . $i . '#comments' : $post->link ),
		'title' => $i,
		'current' => ( $i === $comments_current_page ),
	];
}

// Set previous comment page link
if ( $comments_current_page > 1 ) {
	$prev_page_number = ( $comments_current_page - 1 );
	$pagination['prev'] = [ 'link' => ( $prev_page_number > 1 ? $post->link . 'comment-page-' . $prev_page_number . '#comments' : $post->link ) ];
}

// Set next comment page link
if ( $comments_current_page < $comments_total_pages ) {
	$pagination['next'] = [ 'link' => $post->link . 'comment-page-' . ( $comments_current_page + 1  ) . '#comments' ];
}

$context['comments_offset'] = $comments_offset;
$context['comments_length'] = $comments_per_page;
$context['pagination'] = $pagination;

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
