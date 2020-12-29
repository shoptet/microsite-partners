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
	$is_first = ( $i == 1 );
	$is_last = ( $i == $comments_total_pages );
	$is_current = ( $i == $comments_current_page );
	$is_pre_current = ( $i == $comments_current_page - 1 );
	$is_post_current = ( $i == $comments_current_page + 1 );
	$show = ( $comments_total_pages <= 6 || $is_first || $is_last || $is_current || $is_pre_current || $is_post_current );
	$link = ( $i > 1 ? $post->link . 'comment-page-' . $i . '#comments' : $post->link );

	$pagination['pages'][] = [
		'link' => $link,
		'title' => $i,
		'current' => $is_current,
		'hide' => !$show,
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

$professional_post = new ProfessionalPost( $post->ID );
$context['rating_stars'] = $professional_post->getRatingStars();
$context['average_rating'] = $professional_post->getAverageRating();

Timber::render( 'single.twig', $context );
