<?php

class RequestForm
{
  const IS_SHOPTET_FIELD_KEY = 'field_5d9f2fbd8e64a';
  const REQUEST_FORM_TEMPLATE = 'new-request-page.php';

  public static function init()
  {
    add_filter( 'acf/load_field/name=_post_title', [ get_called_class(), 'loadPostTitleField' ] );
    add_filter( 'acf/load_field/name=_post_content', [ get_called_class(), 'loadPostContentField' ] );
    add_filter( 'acf/load_field/key=' . self::IS_SHOPTET_FIELD_KEY , [ get_called_class(), 'loadIsShoptetField' ] );

    add_action( 'acf/save_post', [ get_called_class(), 'verifyForm' ], 5 ); // before acf post data saved
    add_action( 'acf/save_post', [ get_called_class(), 'saveForm' ], 15 ); // after acf post data saved
  }

  public static function loadPostTitleField( $field )
  {
    if( is_page_template( self::REQUEST_FORM_TEMPLATE ) ) {
      $field['label'] = __( 'Nadpis poptávky', 'shp-partneri' );
      $field['placeholder'] = __( 'Srozumitelný a jednoduchý popis problému, který chcete vyřešit', 'shp-partneri' );
    }
    return $field;
  }

  public static function loadPostContentField( $field )
  {
    if( is_page_template( self::REQUEST_FORM_TEMPLATE ) ) {
      $field['label'] = __( 'Text poptávky', 'shp-partneri' );
      $field['placeholder'] = __( 'Sepište co nejvíce informací o problému, s nímž potřebujete pomoct.', 'shp-partneri' );
      $field['required'] = true;
      $field['type'] = 'textarea';
      $field['rows'] = 10;
      $field['maxlength'] = '';
    }
    return $field;
  }

  public static function loadIsShoptetField( $field )
  {
    // Do not show input label in frontend form
    if( ! is_admin() ) {
      $field['label'] = '';
    }
    return $field;
  }

  public static function verifyForm( $post_id ) {
    if (
      'request' == get_post_type( $post_id ) &&
      is_page_template( self::REQUEST_FORM_TEMPLATE ) &&
      ! verify_recaptcha()
    ) {
      wp_delete_post( $post_id, true );
      wp_die(
        'ReCaptcha not verified',
        'ReCaptcha not verified',
        [ 'response' => 403 ]
      );
    }
  }

  public static function saveForm( $post_id )
  {
    if(
      'request' == get_post_type( $post_id ) &&
      is_page_template( self::REQUEST_FORM_TEMPLATE )
    ) {
      do_action( 'shp/request_form/save', $post_id );
    }
  }
}