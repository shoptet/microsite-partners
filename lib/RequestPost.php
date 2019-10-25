<?php

class RequestPost extends Post
{
  const POST_TYPE = 'request';
  const TAXONOMY = 'category_requests';

  function __construct( $post_id ) {
    parent::__construct( $post_id );
  }

  public function getTerm()
  {
    $term = false;
    if ( $terms = wp_get_post_terms( $this->post_id, self::TAXONOMY ) ) {
      $term = $terms[0];
    }
    return $term;
  }

  public function getExpirationURL() {
    $onboarding_token = $this->getMeta( '_expiration_token' );
    return get_site_url( null, '?request_expiration_token=' . $onboarding_token );;
  }

  static public function getByExpirationToken( $expiration_token ) {
    $query = new WP_Query( [
      'post_type' => self::POST_TYPE,
      'posts_per_page' => 1,
      'post_status' => 'any',
      'fields' => 'ids',
      'meta_query' => [
        [
          'key' => '_expiration_token',
          'value' => $expiration_token,
        ],
      ],
    ] );

    if ( empty( $query->posts ) ) return null;

    $request_post_id = $query->posts[0];
    return new RequestPost( $request_post_id );
  }

}
