<?php

class ShoptetProjectCount
{

  const ENDPOINT_URL = 'https://www.shoptet.cz/projectAction/ShoptetStatisticCounts/Index';
  const CACHE_FILE = 'wp-content/uploads/counters.cached';
  const CACHE_FILE_MODIFY_LIMIT = 3600; // one hour in seconds
  const FETCH_RECURRENCE = 'hourly';

  static function init()
  {
    add_shortcode( 'projectCount' , [ get_called_class(), 'getProjectCount' ] );
    add_action( 'shp/shoptet_project_count/fetch', [ get_called_class(), 'fetch' ] );

    if ( ! wp_next_scheduled( 'shp/shoptet_project_count/fetch' ) ) {
      wp_schedule_event( time(), self::FETCH_RECURRENCE, 'shp/shoptet_project_count/fetch' );
    }
  }

  static function fetch()
  {
    if (
      ! file_exists( self::CACHE_FILE ) || 
      ( time() - filemtime( self::CACHE_FILE ) > self::CACHE_FILE_MODIFY_LIMIT )
    ) {
      $tmp = @file_get_contents( self::ENDPOINT_URL );
      if ( $tmp !== FALSE ) {
        file_put_contents(
          self::CACHE_FILE,
          $tmp
        );
      }
    }
  }

  static function getProjectCount()
  {
    $project_stats = [];
    if( file_exists( self::CACHE_FILE ) ) {
      $content = file_get_contents( self::CACHE_FILE );
      if ( $content !== FALSE ) {
        $project_stats = (array) json_decode( $content );
      }
    }
    
    $project_count = 19068;
    if( ! empty( $project_stats ) && ! empty( $project_stats['projectsCount'] ) ) {
      $project_count = $project_stats['projectsCount'];
    }
 
    return $project_count;
  }

}