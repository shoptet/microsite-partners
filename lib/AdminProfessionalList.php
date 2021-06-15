<?php

namespace Shoptet;

class AdminProfessionalList {

  static function init() {
    add_action( 'restrict_manage_posts', [ get_called_class(), 'render_manager_select' ] );
    add_action( 'pre_get_posts', [ get_called_class(), 'filter_by_manager' ] );
    add_action( 'manage_posts_custom_column', [ get_called_class(), 'manage_column_content' ], 10, 2 );

    add_filter( 'manage_edit-' . \ProfessionalPost::POST_TYPE . '_columns', [ get_called_class(), 'manage_columns' ] );
  }

  static function render_manager_select( $post_type ) {
    
    if ( \ProfessionalPost::POST_TYPE != $post_type ) return;

    $maneger_field = get_field_object('field_87asdc350933f');
    if ( !isset($maneger_field['choices']) || !is_array($maneger_field['choices']) ) return;
    $managers = $maneger_field['choices'];

    $selected_manager = null;
    if ( !empty($_REQUEST['manager']) ) {
      $selected_manager = $_REQUEST['manager'];
    }
  
    echo '<select id="manager" name="manager">';
    echo '<option value="0">' . __( '— Manager —', 'shp-partneri' ) . ' </option>';
    foreach( $managers as $m ){
      $select_attr = ( $m == $selected_manager ) ? ' selected="selected"' : '' ;
      echo '<option value="' . $m . '"' . $select_attr . '>' . $m . ' </option>';
    }
    echo '</select>';
  
  }

  static function filter_by_manager( $wp_query ) {
    if ( !is_admin() || !$wp_query->is_main_query() ) return;
    
    $post_type = $wp_query->get( 'post_type' );
    if ( \ProfessionalPost::POST_TYPE != $post_type ) return;
    
    if ( empty($_REQUEST['manager']) ) return;
    $manager = $_REQUEST['manager'];
    
    $meta_query = $wp_query->get( 'meta_query' );
    
    if ( empty($meta_query) ) {
      $meta_query = [];
    }
    
    $meta_query[] = [
      'key' => 'partnerManager',
      'value' => $manager,
    ];
  
    $wp_query->set( 'meta_query', $meta_query );
  }

  static function manage_columns( $columns ) {
    $custom_columns = [
      'manager' => __( 'Manager', 'shp-partneri' ),
    ];
    return (
      array_slice( $columns, 0, 2, true ) +
      $custom_columns +
      array_slice( $columns, 2, 4, true )
    );
  }

  static function manage_column_content( $column, $post_id ) {
    switch( $column ) {
      case 'manager':
        if( $manager = get_post_meta($post_id, 'partnerManager', true) ) {
          echo esc_html($manager);
        } else {
          echo '–';
        }
      break;
    }
  }

}

AdminProfessionalList::init();