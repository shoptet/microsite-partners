<?php

class RequestPost extends Post
{
  const POST_TYPE = 'request';
  const TAXONOMY = 'category_requests';

  function __construct( $post_id ) {
    parent::__construct( $post_id );
  }

  public function getTerms()
  {
    return wp_get_post_terms( $this->post_id, self::TAXONOMY );
  }

  public function getFuturePreviewLink()
  {
    $slug = $this->getSlug();
    return get_site_url( null, 'future-request/' . $slug . '/' );
  }

}
