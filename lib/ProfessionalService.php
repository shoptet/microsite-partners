<?php

class ProfessionalService
{

  static function init () {
    add_action( 'shp/' . ProfessionalPost::POST_TYPE . '_service/url_action', [ get_called_class(), 'handleURLAction' ] );
  }

  static function handleURLAction( $post_id ) {
    if( isset( $_GET['unsubscribe'] ) && 'all' == $_GET['unsubscribe'] ) {
      self::unsubscribeAll( $post_id );
    } elseif( isset( $_GET['unsubscribe_category'] ) && '' != $_GET['unsubscribe_category'] ) {
      $term_id = intval( $_GET['unsubscribe_category'] );
      self::unsubscribeCategory( $post_id, $term_id );
    }
  }

  static function unsubscribeAll( $post_id ) {
    $professional_post = new ProfessionalPost( $post_id );

    if( $professional_post->getMeta( 'disable_mailing' ) ) {
      wp_die(
        __( 'Jste již ze všech poptávek odhlášen', 'shp-partneri' ),
        __( 'Jste již odhlášen', 'shp-partneri' )
      );
      return;
    }

    $professional_post->setMeta( 'disable_mailing', true );
    do_action( 'shp/professional_service/unsubscribe', $post_id );
    wp_die(
			__( 'Byl jste úspěšně odhlášen ze všech poptávek', 'shp-partneri' ),
			__( 'Úspěšně odhlášeno', 'shp-partneri' )
    );
  }

  static function unsubscribeCategory( $post_id, $term_id ) {
    $term = get_term( $term_id, ProfessionalPost::TAXONOMY );
    if( ! $term ) {
      wp_die(
        __( 'Vypadá to, že jste zadali neplatný odkaz. Zkuste to prosím znovu.', 'shp-partneri' ),
        __( 'Neplatný odkaz', 'shp-partneri' )
      );
      return;
    }

    $professional_post = new ProfessionalPost( $post_id );
    $unsubscribed_categories = $professional_post->getMeta( 'unsubscribed_categories' );

    if ( ! is_array($unsubscribed_categories) ) {
      $unsubscribed_categories = [];
    }

    if ( in_array( $term->term_id, $unsubscribed_categories ) ) {
      wp_die(
        __( 'Jste již z kategorie odhlášen', 'shp-partneri' ),
        __( 'Jste již odhlášen', 'shp-partneri' )
      );
      return;
    }

    $unsubscribed_categories[] = $term->term_id;
    $professional_post->setMeta( 'unsubscribed_categories', $unsubscribed_categories );
    do_action( 'shp/professional_service/unsubscribe', $post_id, $term_id );

    wp_die(
			__( 'Byl jste úspěšně odhlášen z kategorie', 'shp-partneri' ),
			__( 'Úspěšně odhlášeno', 'shp-partneri' )
    );
  }

}