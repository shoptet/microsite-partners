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

  static public function getAllToNotify( $term ) {

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
