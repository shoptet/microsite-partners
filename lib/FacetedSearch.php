<?php

class FacetedSearch
{

  protected $wp_query;

  protected const POSTS_PER_PAGE = 13;

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
    $this->wp_query->set( 'meta_key', 'verifiedLevel' );
    $this->wp_query->set( 'orderby', [
      'verifiedLevel' => 'DESC',
      'title' => 'ASC'
    ] );
  }

}