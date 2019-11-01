<?php

class RequestArchive
{

  protected $wp_query;

  const POSTS_PER_PAGE = 10;

  function __construct()
  {
    add_action( 'pre_get_posts', [ $this, 'init' ] );
  }

  function init( &$wp_query ) {
    if( ! $this->isArchiveQuery( $wp_query ) ) return;
    $this->wp_query = $wp_query;
    $this->wp_query->set( 'posts_per_page', self::POSTS_PER_PAGE );
    $this->initOrdering();
  }

  function isArchiveQuery( &$wp_query ) {
    return
      ! is_admin() &&
      $wp_query->is_main_query() &&
      (
        $wp_query->get( 'post_type' ) == RequestPost::POST_TYPE ||
        $wp_query->is_tax( RequestPost::TAXONOMY )
      );
  }

  function initOrdering()
  {
    // Set default ordering
    $this->wp_query->set( 'meta_key', 'is_shoptet' );
    $this->wp_query->set( 'orderby', [ 'meta_value_num' => 'desc', 'post_date' => 'desc' ] );

    // Get query
    $query = false;
    if( isset( $_GET[ 'orderby' ] ) ) {
      $query = explode( '_', $_GET[ 'orderby' ] );
    }

    // Sort by date ascending
    if( $query != [ 'date', 'desc' ] ) {
      $this->wp_query->set( 'orderby', [ 'meta_value_num' => 'desc', 'post_date' => $query[1] ] );
    }

    // Filter by publish posts
    if( isset( $_GET[ 'filterby' ] ) && 'active' == $_GET[ 'filterby' ] ) {
      $this->wp_query->set( 'post_status', 'publish' );
    }
  }

}