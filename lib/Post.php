<?php

class Post
{
  const POST_TYPE = 'post';

  protected $post_id;

  function __construct( $post_id ) {
    $this->post_id = $post_id;
  }

  public function getID() {
    return $this->post_id;
  }

  public function setMeta( $key, $value ) {
    update_post_meta( $this->post_id, $key, $value );
  }

  public function getMeta( $key, $single = true ) {
    return get_post_meta( $this->post_id, $key, $single );
  }

  public function setStatus( $status ) {
    $postarr = [
      'ID' => $this->getID(),
      'post_status' => $status,
    ];
    wp_update_post( $postarr );
  } 

}
