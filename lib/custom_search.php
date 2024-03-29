<?php
/**
 * Extend WordPress search to include custom fields
 *
 * via https://adambalee.com/search-wordpress-by-custom-fields-without-a-plugin/
 */

/**
 * Join posts and postmeta tables
 *
 * http://codex.wordpress.org/Plugin_API/Filter_Reference/posts_join
 */
function cf_search_join( $join ) {
  global $wpdb;

  if ( !is_admin() && is_search() ) {
    $join .=' LEFT JOIN '.$wpdb->postmeta. ' ON '. $wpdb->posts . '.ID = ' . $wpdb->postmeta . '.post_id ';
  }

  return $join;
}
add_filter('posts_join', 'cf_search_join' );

/**
 * Modify the search query with posts_where
 *
 * http://codex.wordpress.org/Plugin_API/Filter_Reference/posts_where
 */
function cf_search_where( $where ) {
  global $pagenow, $wpdb;

  if ( !is_admin() && is_search() ) {
    $where = preg_replace(
      "/\(\s*".$wpdb->posts.".post_title\s+LIKE\s*(\'[^\']+\')\s*\)/",
      "(".$wpdb->posts.".post_title LIKE $1)
      OR (
        (
          (".$wpdb->postmeta.".meta_key = 'description')
          OR
          (".$wpdb->postmeta.".meta_key = 'benefit')
        )
        AND
        (".$wpdb->postmeta.".meta_value LIKE $1)
      )", $where );
  }

  return $where;
}
add_filter( 'posts_where', 'cf_search_where' );

/**
 * Prevent duplicates
 *
 * http://codex.wordpress.org/Plugin_API/Filter_Reference/posts_distinct
 */
function cf_search_distinct( $where ) {
  global $wpdb;

  if ( !is_admin() && is_search() ) {
    return "DISTINCT";
  }

  return $where;
}
add_filter( 'posts_distinct', 'cf_search_distinct' );


function search_filter($query) {

	if (!is_admin() && is_search() && $query->is_main_query()) {
		// Enlarge number of post in search results
		$query->set('posts_per_page', 12);
		$query->set('post_status', 'publish');
		// Include only custom posts
		$query->set('post_type', array(
			'profesionalove',
			'napojeni',
			'nastroje',
		));
	}

	return $query;
}
add_filter('pre_get_posts','search_filter');
