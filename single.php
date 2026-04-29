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

if ($post->meta_description) {
	$context['meta_description'] = $post->meta_description;
}

$comments = $post->comments( 0, 'wp' );
$comments_count = count( $comments );
$comments_per_page = max( 1, intval( get_option( 'comments_per_page' ) ) );
$rating_filter = filter_input(
	INPUT_GET,
	'rating',
	FILTER_VALIDATE_INT,
	[
		'options' => [
			'min_range' => 1,
			'max_range' => 5,
		],
	]
);
$rating_filter = ( $rating_filter ? intval( $rating_filter ) : null );
$filtered_comments = [];

foreach ( $comments as $comment ) {
	$rating = ( isset( $comment->rating ) ? intval( $comment->rating ) : 0 );
	if ( ! $rating_filter || $rating === $rating_filter ) {
		$filtered_comments[] = $comment;
	}
}

$filtered_comments_count = count( $filtered_comments );
$comments_total_pages = (int) ceil( $filtered_comments_count / $comments_per_page );
$query_cpage = max( 1, intval( get_query_var( 'cpage' ) ) );
$comments_current_page = ( $query_cpage ? $query_cpage : 1 );
if ( $comments_total_pages > 0 && $comments_current_page > $comments_total_pages ) {
	$comments_current_page = $comments_total_pages;
}
$comments_offset = ( $comments_per_page * ( $comments_current_page - 1 ) );

$get_comments_page_link = function( $page ) use ( $post, $rating_filter ) {
	$link = ( $page > 1 ? trailingslashit( $post->link ) . 'comment-page-' . $page . '/' : $post->link );
	if ( $rating_filter ) {
		$link = add_query_arg( 'rating', $rating_filter, $link );
	}
	return $link . '#comments';
};

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
	$link = $get_comments_page_link( $i );

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
	$pagination['prev'] = [ 'link' => $get_comments_page_link( $prev_page_number ) ];
}

// Set next comment page link
if ( $comments_current_page < $comments_total_pages ) {
	$pagination['next'] = [ 'link' => $get_comments_page_link( $comments_current_page + 1 ) ];
}

$rating_filter_links = [];
for ( $i = 1; $i <= 5; $i++ ) {
	$rating_filter_links[$i] = [
		'link' => add_query_arg( 'rating', $i, $post->link ) . '#comments',
		'active' => ( $rating_filter === $i ),
	];
}

$context['comments_offset'] = $comments_offset;
$context['comments_length'] = $comments_per_page;
$context['comments_count'] = $comments_count;
$context['filtered_comments'] = $filtered_comments;
$context['filtered_comments_count'] = $filtered_comments_count;
$context['rating_filter'] = $rating_filter;
$context['rating_filter_links'] = $rating_filter_links;
$context['rating_filter_reset_link'] = $post->link . '#comments';
$context['pagination'] = $pagination;

$professional_post = new ProfessionalPost( $post->ID );
$context['rating_stars'] = $professional_post->getRatingStars();
$context['average_rating'] = $professional_post->getAverageRating();

Timber::render( 'single.twig', $context );
