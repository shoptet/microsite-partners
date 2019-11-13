<?php

class PostService
{

  const POST_ClASSES_WITH_AUTH_TOKEN = [
    'ProfessionalPost',
    'RequestPost',
  ]; 

  static function init () {
    add_action( 'init', [ get_called_class(), 'handleAuthTokenURL' ] );
    add_action( 'save_post', [ get_called_class(), 'setAuthToken' ], 10, 3 );
    add_filter( 'robots_txt', [ get_called_class(), 'filterRobotsTxt' ] );
  }

  static function handleAuthTokenURL() {
    if(
      ! isset( $_GET['token'] ) || '' == $_GET['token'] ||
      ! isset( $_GET['post_type'] ) || '' == $_GET['post_type']
    ) return;

    $auth_token = $_GET['token'];
    $post_type = $_GET['post_type'];

    if ( ! self::isPostTypeWithToken( $post_type ) ) return;

    $post = Post::getByAuthToken( $auth_token, $post_type );

    if( ! $post ) {
      wp_die(
        __( 'Vypadá to, že jste zadali neplatný odkaz. Zkuste to prosím znovu.', 'shp-partneri' ),
        __( 'Neplatný odkaz', 'shp-partneri' )
      );
      return;
    }
    do_action( 'shp/' . $post_type . '_service/url_action', $post->getID() );
  }

  static function isPostTypeWithToken( $post_type ) {
    $is_correct_auth_post_type = false;
    foreach( self::POST_ClASSES_WITH_AUTH_TOKEN as $post_class ) {
      if ( $post_class::POST_TYPE == $post_type ) {
        $is_correct_auth_post_type = true;
        break;
      }
    }
    return $is_correct_auth_post_type;
  }

  static function setAuthToken( $post_id, $post, $update ) {
    if ( self::isPostTypeWithToken( $post->post_type ) && ! $update ) {
      $p = new Post( $post_id );
      $p->setAuthToken();
    }
  }

  static function filterRobotsTxt( $robot_text ) {
    return $robot_text . "
Disallow: *?token=*
Disallow: *?orderby=*
Disallow: *?filterby=*
";
  }

}