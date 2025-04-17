<?php

class FacetedSearch
{

  protected $wp_query;

  const POSTS_PER_PAGE = 11;

  public function __construct( &$wp_query )
  {
    $this->wp_query = $wp_query;
    $this->wp_query->set( 'posts_per_page', self::POSTS_PER_PAGE );
  }

  protected function getMetaQuery()
  {
    $meta_query = $this->wp_query->get( 'meta_query' );

    if( $meta_query == '' ) {
      $meta_query = [];
    }

    return $meta_query;
  }

  public function filterBySearchQuery()
  {
    if( isset( $_GET[ 's' ] ) && ! empty( $_GET[ 's' ] ) ) {
      $this->wp_query->set( 's', $_GET[ 's' ] );
    }
  }

  public function filterByMetaQuery( $key, $relation = 'AND', $compare = '=' )
  {
    $meta_query = $this->getMetaQuery();

    if( ! isset( $_GET[ $key ] ) ) return;
    
    if( is_array( $_GET[ $key ] ) ) {
      $result = [ 'relation' => $relation ];
      foreach( $_GET[ $key ] as $value ) {
        $result[] = [
          'key' => $key,
          'value' => $value,
          'compare' => $compare,
        ];
      }
    } elseif( ! empty( $_GET[ $key ] ) ) {
      $result[] = [
        'key' => $key,
        'value' => $_GET[ $key ],
        'compare' => $compare,
      ];
    }
    $meta_query[] = $result;

    $this->wp_query->set( 'meta_query', $meta_query );
  }

  public function filterByTaxQuery( $taxonomy )
  {
    if( ! $this->wp_query->is_tax( $taxonomy ) && isset( $_GET[ 'category' ] ) && is_array( $_GET[ 'category' ] ) ) {
      $tax_query[] = [
        'taxonomy' => $taxonomy,
        'terms' => $_GET[ 'category' ],
        'operator'	=> 'IN',
      ];
      $this->wp_query->set( 'tax_query', $tax_query );
    }
  }

  public function order()
  {
    // Use meta_key for ordering so that WP_Query automatically joins the postmeta table
    // with the alias "mt1" for the verifiedLevel meta key.
    $this->wp_query->set('meta_key', 'verifiedLevel');

    // Define ordering:
    //   - "meta_value" orders by the verifiedLevel meta value (as a string) in DESC order.
    //   - "top_level_count" is a placeholder for the count of approved top-level comments.
    //   - "avg_rating" is a placeholder for the computed average rating from comment meta.
    //   - "title" orders the posts by title in ASC order.
    $this->wp_query->set('orderby', [
      'meta_value'      => 'DESC',
      'top_level_count' => 'DESC',
      'avg_rating'      => 'DESC',
      'title'           => 'ASC'
    ]);

    // Add a filter to modify the SQL query and append the "top_level_count" field.
    add_filter('posts_clauses', [$this, 'modify_posts_clauses_for_ordering'], 10, 2);
  }

  public function modify_posts_clauses_for_ordering($clauses, $query) {
    global $wpdb;
    $orderby = $query->get('orderby');
    // Check if our custom ordering keys are set.
    if ( isset($orderby['top_level_count']) && isset($orderby['avg_rating']) ) {
      // Append a field alias "top_level_count" that counts approved top-level comments.
      $clauses['fields'] .= ", (
        SELECT COUNT(*) 
        FROM {$wpdb->comments} c 
        WHERE c.comment_post_ID = {$wpdb->posts}.ID 
          AND c.comment_parent = 0 
          AND c.comment_approved = '1'
      ) AS top_level_count";

      // Append a field alias "avg_rating" that computes the average rating (as a decimal)
      // from comment meta 'rating'. It converts the meta value to a number for averaging.
      $clauses['fields'] .= ", (
        SELECT AVG(CAST(cm.meta_value AS DECIMAL(10,2)))
        FROM {$wpdb->comments} c 
        INNER JOIN {$wpdb->commentmeta} cm ON c.comment_ID = cm.comment_id
        WHERE c.comment_post_ID = {$wpdb->posts}.ID 
          AND cm.meta_key = 'rating'
          AND c.comment_approved = '1'
          AND c.comment_parent = 0
      ) AS avg_rating";

      // Build the ORDER BY clause:
      //   - First, order by the verifiedLevel meta value from the postmeta table.
      //   - Then, order by top_level_count in descending order.
      //   - Next, order by avg_rating in descending order.
      //   - Finally, order by the post title in ascending order.
      $clauses['orderby'] = "{$wpdb->postmeta}.meta_value DESC, top_level_count DESC, avg_rating DESC, {$wpdb->posts}.post_title ASC";
    }
    return $clauses;
  }

}