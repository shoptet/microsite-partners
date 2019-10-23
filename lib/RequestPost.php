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

  public function getAuthorExpireLink() {
    return 'expire_link';
  }

}
