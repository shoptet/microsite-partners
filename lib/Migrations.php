<?php

class Migrations {

  const OPTION_PREFIX = '_migration_';
  const MIGRATIONS = [
    'add_token_to_professionals',
    'add_token_to_requests',
  ];

  static function init() {
    add_action( 'init', [ get_called_class(), 'makeMigrations' ] );    
  }

  static function makeMigrations() {
    foreach( self::MIGRATIONS as $migration ) {
      $option = self::OPTION_PREFIX . $migration;
      if ( get_option( $option ) !== false ) continue;
      self::{$migration}();
      add_option( $option, true );
    }
  }

  static function add_token_to_professionals() {
    $query = new WP_Query( [
      'post_type' => ProfessionalPost::POST_TYPE,
      'posts_per_page' => -1,
      'post_status' => 'any',
      'fields' => 'ids',
    ] );
    foreach( $query->posts as $post_id ) {
      $p = new ProfessionalPost( $post_id );
      $p->setAuthToken();
    }
  }

  static function add_token_to_requests() {
    $query = new WP_Query( [
      'post_type' => RequestPost::POST_TYPE,
      'posts_per_page' => -1,
      'post_status' => 'any',
      'fields' => 'ids',
    ] );
    foreach( $query->posts as $post_id ) {
      $p = new RequestPost( $post_id );
      $p->setAuthToken();
    }
  }

}