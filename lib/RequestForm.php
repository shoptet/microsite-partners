<?php

class RequestForm
{
  const AUTHOR_NAME_FIELD_KEY = 'field_5d9f2ebf8e646';
  const URL_FIELD_KEY = 'field_5d9f2efc8e647';
  const CATEGORY_FIELD_KEY = 'field_5d10a24f0b5e7';
  const IS_SHOPTET_FIELD_KEY = 'field_5d9f2fbd8e64a';
  const REQUEST_FORM_TEMPLATE = 'new-request-page.php';

  public static function init()
  {
    add_filter( 'acf/load_field/name=_post_title', [ get_called_class(), 'loadPostTitleField' ] );
    add_filter( 'acf/load_field/name=_post_content', [ get_called_class(), 'loadPostContentField' ] );
    add_filter( 'acf/load_field/key=' . self::IS_SHOPTET_FIELD_KEY , [ get_called_class(), 'loadIsShoptetField' ] );

    // Placeholder and instructions cannot be translated in acf_add_local_field_group
    add_filter( 'acf/load_field/key=' . self::AUTHOR_NAME_FIELD_KEY, [ get_called_class(), 'loadAuthorNameField' ] );
    add_filter( 'acf/load_field/key=' . self::URL_FIELD_KEY, [ get_called_class(), 'loadUrlField' ] );
    add_filter( 'acf/load_field/key=' . self::CATEGORY_FIELD_KEY, [ get_called_class(), 'loadCategoryField' ] );

    add_action( 'acf/save_post', [ get_called_class(), 'verifyForm' ], 5 ); // before acf post data saved
    add_action( 'acf/save_post', [ get_called_class(), 'saveForm' ], 20 ); // after acf post data saved
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

  public static function loadAuthorNameField( $field )
  {
    if( is_page_template( self::REQUEST_FORM_TEMPLATE ) ) {
      $field['placeholder'] = __( 'Jméno a příjmení', 'shp-partneri' );
    }
    return $field;
  }

  public static function loadUrlField( $field )
  {
    if( is_page_template( self::REQUEST_FORM_TEMPLATE ) ) {
      $field['placeholder'] = __( 'Webová adresa vašeho e-shopu nebo prezentace', 'shp-partneri' );
    }
    return $field;
  }

  public static function loadCategoryField( $field )
  {
    if( is_page_template( self::REQUEST_FORM_TEMPLATE ) ) {
      $options = get_fields('options');
      $field['instructions'] = sprintf( __( 'Zvolte <a href="%s" target="_blank">obor</a>, do kterého byste chtěli zařadit', 'shp-partneri' ), $options['themeProfessionalPageUrl'] );
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