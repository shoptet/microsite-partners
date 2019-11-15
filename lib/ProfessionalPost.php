<?php

class ProfessionalPost extends Post
{
  const POST_TYPE = 'profesionalove';
  const TAXONOMY = 'category_professionals';

  function __construct( $post_id ) {
    parent::__construct( $post_id );
  }

  public function isUnsubscribedFromCategory( $term_id )
  {
    $unsubscribed_categories = $this->getMeta( 'unsubscribed_categories' );
    $is_unsubscribed = (
      is_array( $unsubscribed_categories ) &&
      in_array( $term_id, $unsubscribed_categories )
    );
    return $is_unsubscribed;
  }

  public function getRatingStars()
  {
    $rating_stars = [];
    for ( $i = 1; $i <= 5; $i++ ) {
      $rating_stars[$i] = 0;
    }
    foreach ( $this->getComments() as $comment ) {
      if ( $rating = get_comment_meta( $comment->comment_ID, 'rating', true ) ) {
        $rating = intval( $rating );
        $rating_stars[ $rating ]++;
      }
    }
    return $rating_stars;
  }

  public function getAverageRating()
  {
    $rating_sum = 0;
    $comments_count = 0;
    foreach ( $this->getComments() as $comment ) {
      if ( $rating = get_comment_meta( $comment->comment_ID, 'rating', true ) ) {
        $rating_sum += intval( $rating );
        $comments_count++;
      }
    }
    $average_rating = 0;
    if ( $comments_count > 0 ) {
      $average_rating = round( $rating_sum / $comments_count );
    }
    return $average_rating;
  }

  static public function getAllToNotify( $term )
  {

    // Get all professionals in category
    $query = new WP_Query( [
      'post_type' => self::POST_TYPE,
      'posts_per_page' => -1,
      'post_status' => 'publish',
      'fields' => 'ids',
      'tax_query' => [
        [
          'taxonomy' => self::TAXONOMY,
          'terms' => $term->term_id,
        ],
      ],
    ] );
    
    // Filter professionals by mailing settings
    $professionals = [];
    foreach( $query->posts as $p_id ) {
      $p = new ProfessionalPost( $p_id );
      if ( ! $p->getMeta( 'disable_mailing' ) && ! $p->isUnsubscribedFromCategory( $term->term_id ) ) {
        $professionals[] = $p;
      }
    }

    return $professionals;
  }

}
