<?php
/* Via https://someweblog.com/wordpress-custom-taxonomy-with-same-slug-as-custom-post-type/ */

/*
 * Replace Taxonomy slug with Post Type slug in url
 * Version: 1.1
 */
function taxonomy_slug_rewrite($wp_rewrite) {
    $rules = array();
    // get all custom taxonomies
    $taxonomies = get_taxonomies(array('_builtin' => false), 'objects');
    // get all custom post types
    $post_types = get_post_types(array('public' => true, '_builtin' => false), 'objects');

    foreach ($post_types as $post_type) {
        foreach ($taxonomies as $taxonomy) {

            // go through all post types which this taxonomy is assigned to
            foreach ($taxonomy->object_type as $object_type) {

                // check if taxonomy is registered for this custom type
                if ($object_type == $post_type->name) {

                    // get category objects
                    $terms = get_categories(array('type' => $object_type, 'taxonomy' => $taxonomy->name, 'hide_empty' => 0));

                    // make rules
                    foreach ($terms as $term) {
                        $rules[$post_type->rewrite['slug'] . '/' . $term->slug . '/?$'] = 'index.php?' . $term->taxonomy . '=' . $term->slug;
                    }
                }
            }
        }
    }
    // merge with global rules
    $wp_rewrite->rules = $rules + $wp_rewrite->rules;
}
add_filter('generate_rewrite_rules', 'taxonomy_slug_rewrite');

/*
 * Rewrite Pagination slug rules
 */
function pagination_slug_rewrite() {
  global $wp_rewrite;
  $wp_rewrite->pagination_base = __( 'strana', 'shp-partneri' );
  $wp_rewrite->flush_rules();
}
add_filter( 'init', 'pagination_slug_rewrite', 0 );

/*
 * Flush Rewrite Rules when a post is saved
 */
function flush_permalinks() {
	flush_rewrite_rules();
}
add_action( 'created_term', 'flush_permalinks' );
add_action( 'edited_term', 'flush_permalinks' );
