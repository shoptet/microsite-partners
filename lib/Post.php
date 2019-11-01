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

  public function getAuthTokenURL( $args ) {
    $auth_token = $this->getMeta( '_auth_token' );
    if( ! $auth_token ) {
      try {
        throw new Exception( 'No auth token found for post with ID ' . $this->getID() );
      } catch ( Exception $e ) {
        error_log( $e->getMessage() );
      }
      return false;
    }
    $query_args = '';
    foreach( $args as $key => $value ) {
      $query_args .= '&' . $key . '=' . $value;
    }
    return get_site_url( null, '?token=' . $auth_token . '&post_type=' . $this::POST_TYPE . $query_args );
  }

  public function setAuthToken() {
    $auth_token = bin2hex( openssl_random_pseudo_bytes(16) );
    $added = add_post_meta( $this->getID(), '_auth_token', $auth_token, true );
    return $added;
  }

  static public function getByAuthToken( $auth_token, $post_type ) {
    $query = new WP_Query( [
      'post_type' => $post_type,
      'posts_per_page' => 1,
      'post_status' => 'any',
      'fields' => 'ids',
      'meta_query' => [
        [
          'key' => '_auth_token',
          'value' => $auth_token,
        ],
      ],
    ] );

    if( empty( $query->posts ) ) return null;

    $request_post_id = $query->posts[0];
    return new Post( $request_post_id );
  }

}
