<?php

class RequestNotifier
{

  const REQUEST_VARIABLES_FIELD_NAMES = [
    'request_new_author',
    'request_new_admin',
    'request_approve_author',
    'request_approve_professional',
    'request_message_author',
    'request_expired_author',
    'request_footer_author',
  ];
  const PROFESSIONAL_VARIABLES_FIELD_NAMES = [
    'request_approve_professional',
    'request_unsubscribed_professional',
  ];
  const MESSAGE_VARIABLES_FIELD_NAMES = [
    'request_message_author',
  ];
  const AUTHOR_FOOTER_FILED_NAME = 'request_footer_author';
  const SHOW_AUTHOR_FOOTER_FIELD_NAMES = [
    'request_new_author',
    'request_approve_author',
  ];

  static function init()
  {
    add_action( 'shp/request_form/save', [ get_called_class(), 'newRequestAdmin' ] );
    add_action( 'shp/request_form/save', [ get_called_class(), 'newRequestAuthor' ] );
    add_action( 'shp/request_service/approve', [ get_called_class(), 'approvedRequestAuthor' ] );
    add_action( 'shp/request_service/approve', [ get_called_class(), 'approvedRequestProfessionals' ] );
    add_action( 'shp/request_message/validate', [ get_called_class(), 'messageRequestAuthor' ], 10, 2 );
    add_action( 'shp/request_service/expire', [ get_called_class(), 'expiredRequestAuthor' ] );
    add_action( 'shp/professional_service/unsubscribe', [ get_called_class(), 'unsubscribeRequestPartner' ] );

    foreach( self::REQUEST_VARIABLES_FIELD_NAMES as $field_name ) {
      add_filter( 'acf/load_field/name=' . $field_name , [ get_called_class(), 'showRequestVariablesInstructions' ] );
    }
    foreach( self::PROFESSIONAL_VARIABLES_FIELD_NAMES as $field_name ) {
      add_filter( 'acf/load_field/name=' . $field_name , [ get_called_class(), 'showProfessionalVariablesInstructions' ] );
    }
    foreach( self::MESSAGE_VARIABLES_FIELD_NAMES as $field_name ) {
      add_filter( 'acf/load_field/name=' . $field_name , [ get_called_class(), 'showMessageVariablesInstructions' ] );
    }
    foreach( self::SHOW_AUTHOR_FOOTER_FIELD_NAMES as $field_name ) {
      add_filter( 'acf/load_field/name=' . $field_name , [ get_called_class(), 'showAuthorFooterInstructions' ] );
    }
  }

  static function showRequestVariablesInstructions( $field )
  {
    $instructions = '<br><strong>Proměnné poptávky</strong>';
    $instructions .= '<br><code>%request_name%</code>, <code>%request_category%</code>, <code>%request_admin_edit_url%</code>, <code>%request_text%</code>, <code>%request_author_name%</code>, <code>%request_author_email%</code>, <code>%request_preview_url%</code>, <code>%request_url%</code>, <code>%request_expiration_url%</code><br>';
    $field['instructions'] .= $instructions;
    return $field;
  }

  static function showProfessionalVariablesInstructions( $field )
  {
    $instructions = '<br><strong>Proměnné partnera</strong>';
    $instructions .= '<br><code>%partner_name%</code>, <code>%partner_unsubscribe_category_url%</code>, <code>%partner_unsubscribe_all_url%</code><br>';
    $field['instructions'] .= $instructions;
    return $field;
  }

  static function showMessageVariablesInstructions( $field )
  {
    $instructions = '<br><strong>Proměnné reakce</strong>';
    $instructions .= '<br><code>%message_name%</code>, <code>%message_email%</code>, <code>%message_text%</code><br>';
    $field['instructions'] .= $instructions;
    return $field;
  }

  static function showAuthorFooterInstructions( $field )
  {
    $instructions = 'Za touto zprávou se zobrazí patička s expiračním odkazem (viz níže)<br>';
    $field['sub_fields'][1]['instructions'] .= $instructions;
    return $field;
  }

  static function getDefaultEmailHeaders()
  {
    $options = get_fields( 'options' );
    $headers = [
      'From: ' . $options['email_from'],
      'Content-Type: text/html; charset=UTF-8',
    ];
    return $headers;
  }

  static function compileMail( $field_name, $args = [] )
  {
    $options = get_fields( 'options' );
    $field = $options[ $field_name ];
    $subject = $field[ 'subject' ];
    $message = $field[ 'message' ];

    if( in_array( $field_name, self::SHOW_AUTHOR_FOOTER_FIELD_NAMES ) ) {
      $message .= $options[ self::AUTHOR_FOOTER_FILED_NAME ];
    }

    $replace_pairs = self::getReplacePairs( $args );

    $compiled_subject = strtr( $subject, $replace_pairs );
    $compiled_message = strtr( $message, $replace_pairs );

    return [
      'subject' => $compiled_subject,
      'message' => $compiled_message,
    ];
  }

  static function getReplacePairs( $args )
  {
    $term = null;
    $replace_pairs = [];

    if( isset( $args['request_id'] ) ) {
      $request_post = new RequestPost( $args['request_id'] );
      $term = $request_post->getTerm();
      $request_replace_pairs = [
        '%request_name%' => $request_post->getTitle(),
        '%request_text%' => $request_post->getContent(),
        '%request_author_name%' => $request_post->getMeta( 'author_name' ),
        '%request_author_email%' => $request_post->getMeta( 'author_email' ),
        '%request_url%' => $request_post->getLink(),
        '%request_preview_url%' => $request_post->getFuturePreviewLink(),
        '%request_admin_edit_url%' => $request_post->getAdminEditLink(),
        '%request_expiration_url%' => $request_post->getAuthTokenURL( [ 'action' => 'expire' ] ),
      ];
      if( $term ) {
        $request_replace_pairs['%request_category%'] = $term->name;
      }
      $replace_pairs = array_merge( $replace_pairs, $request_replace_pairs );
    }

    if( isset( $args['professional_id'] ) ) {
      $professional_post = new ProfessionalPost( $args['professional_id'] );
      $professional_replace_pairs = [
        '%partner_name%' => $professional_post->getTitle(),
        '%partner_unsubscribe_all_url%' => $professional_post->getAuthTokenURL( [ 'unsubscribe' => 'all' ] ),
      ];
      if( $term ) {
        $related_term = get_term_by( 'slug', $term->slug, ProfessionalPost::TAXONOMY );
        $professional_replace_pairs['%partner_unsubscribe_category_url%'] = $professional_post->getAuthTokenURL( [ 'unsubscribe_category' => $related_term->term_id ] );
      }
      $replace_pairs = array_merge( $replace_pairs, $professional_replace_pairs );
    }

    if( isset( $args['message_arr'] ) ) {
      $message_arr = $args['message_arr'];
      $message_replace_pairs = [
        '%message_name%' => $message_arr['name'],
        '%message_email%' => $message_arr['email'],
        '%message_text%' => nl2br( $message_arr['message'] ),
      ];
      $replace_pairs = array_merge( $replace_pairs, $message_replace_pairs );
    }

    return $replace_pairs;
  }

  static function newRequestAdmin( $post_id )
  {
    $field_name = 'request_new_admin';
	  $options = get_fields( 'options' );
    $admin_email = $options['authorized_review_email_recipient'];
    $headers = self::getDefaultEmailHeaders();

    $compile_args = [
      'request_id' => $post_id,
    ];
    $compiled_mail = self::compileMail( $field_name, $compile_args );
    wp_mail( $admin_email, $compiled_mail['subject'], $compiled_mail['message'], $headers );
  }

  static function newRequestAuthor( $post_id )
  {
    $field_name = 'request_new_author';
    $request_post = new RequestPost( $post_id );
    $author_email = $request_post->getMeta( 'author_email' );
    $headers = self::getDefaultEmailHeaders();

    $compile_args = [
      'request_id' => $post_id,
    ];
    $compiled_mail = self::compileMail( $field_name, $compile_args );
    wp_mail( $author_email, $compiled_mail['subject'], $compiled_mail['message'], $headers );
  }

  static function approvedRequestAuthor( $post_id )
  {
    $field_name = 'request_approve_author';
    $request_post = new RequestPost( $post_id );
    $author_email = $request_post->getMeta( 'author_email' );
    $headers = self::getDefaultEmailHeaders();
    
    $compile_args = [
      'request_id' => $post_id,
    ];
    $compiled_mail = self::compileMail( $field_name, $compile_args );
    wp_mail( $author_email, $compiled_mail['subject'], $compiled_mail['message'], $headers );
  }

  static function approvedRequestProfessionals( $post_id )
  {
    $field_name = 'request_approve_professional';
    $request_post = new RequestPost( $post_id );
    $headers = self::getDefaultEmailHeaders();
    $term =  $request_post->getTerm();

    if( ! $term ) {
      throw new Exception( 'No term related to a request post with ID ' . $post_id );
    }

    $related_term = get_term_by( 'slug', $term->slug, ProfessionalPost::TAXONOMY );
    $professionals = ProfessionalPost::getAllToNotify( $related_term );

    foreach( $professionals as $professional_post ) {
      $professional_email = $professional_post->getMeta( 'emailAddress' );

      if( ! $professional_email ) {
        try {
          throw new Exception( 'Professional ( ' . $professional_post->getID() . ' ) has no e-mail.' );
        } catch ( Exception $e ) {
          error_log( $e->getMessage() );
        }
        continue;
      }
      
      $compile_args = [
        'request_id' => $post_id,
        'professional_id' => $professional_post->getID(),
      ];
      $compiled_mail = self::compileMail( $field_name, $compile_args );
      wp_mail( $professional_email, $compiled_mail['subject'], $compiled_mail['message'], $headers );
    }
  }

  static function messageRequestAuthor( $post_id, $message_arr )
  {
    $field_name = 'request_message_author';
    $request_post = new RequestPost( $post_id );
    $author_email = $request_post->getMeta( 'author_email' );
    $headers = self::getDefaultEmailHeaders();
    $headers[] = 'Reply-To: ' . $message_arr['email'];
    $headers[] = 'Cc: ' . $message_arr['email'];

    $compile_args = [
      'request_id' => $post_id,
      'message_arr' => $message_arr,
    ];
    $compiled_mail = self::compileMail( $field_name, $compile_args );
    wp_mail( $author_email, $compiled_mail['subject'], $compiled_mail['message'], $headers );
  }

  static function expiredRequestAuthor( $post_id )
  {
    $field_name = 'request_expired_author';
    $request_post = new RequestPost( $post_id );
    $author_email = $request_post->getMeta( 'author_email' );
    $headers = self::getDefaultEmailHeaders();

    $compile_args = [
      'request_id' => $post_id,
    ];
    $compiled_mail = self::compileMail( $field_name, $compile_args );
    wp_mail( $author_email, $compiled_mail['subject'], $compiled_mail['message'], $headers );
  }

  static function unsubscribeRequestPartner( $post_id )
  {
    $field_name = 'request_unsubscribed_professional';
    $professional_post = new ProfessionalPost( $post_id );
    $professional_email = $professional_post->getMeta( 'emailAddress' );
    $headers = self::getDefaultEmailHeaders();

    if( ! $professional_email ) {
      throw new Exception( 'Professional (' . $p->ID . ') has no e-mail.' );
    }

    $compile_args = [
      'professional_id' => $post_id,
    ];
    $compiled_mail = self::compileMail( $field_name, $compile_args );
    wp_mail( $professional_email, $compiled_mail['subject'], $compiled_mail['message'], $headers );
  }

}