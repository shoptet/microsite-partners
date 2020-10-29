<?php

class ProfessionalService
{

  static function init () {
    add_action( 'shp/' . ProfessionalPost::POST_TYPE . '_service/url_action', [ get_called_class(), 'handleURLAction' ] );
    add_action( 'wp_ajax_partner_message', [ get_called_class(), 'handleMessage' ] );
    add_action( 'wp_ajax_nopriv_partner_message', [ get_called_class(), 'handleMessage' ] );
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

  static function handleMessage() {

    if ( !verify_recaptcha() ) {
      wp_send_json_error( 'reCaptcha not verified' , 403 );
      return;
    }

    // Sanitize message post data
    $name = sanitize_text_field( $_POST[ 'name' ] );
    $email = sanitize_email( $_POST[ 'email' ] );
    $message = sanitize_textarea_field( $_POST[ 'message' ] );
    $post_id = intval( $_POST[ 'post_id' ] );

    // Check correct post type
    if ( ProfessionalPost::POST_TYPE != get_post_type( $post_id ) ) {
      wp_send_json_error( 'Wrong post type' , 403 );
      return;
    }

    // Check correct post status
    if ( 'publish' != get_post_status( $post_id ) ) {
      wp_send_json_error( 'Wrong post status' , 403 );
      return;
    }

    // Validate data
    if ( empty($name) || empty($message) || !is_email($email) ) {
      wp_send_json_error( 'Data is not valid' , 403 );
      return;
    }
    
    // WordPress comments blacklist check
    $user_url = '';
    $user_ip = $_SERVER['REMOTE_ADDR'];
    $user_ip = preg_replace( '/[^0-9a-fA-F:., ]/', '', $user_ip );
    $user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : '';
    $user_agent = substr( $user_agent, 0, 254 );

    if ( wp_check_comment_disallowed_list( $name, $email, $user_url, $message, $user_ip, $user_agent ) ) {
      wp_send_json_error( null, 403 );
      return;
    }

    $professional_post = new ProfessionalPost($post_id);

    $professional_mail = $professional_post->getMeta('emailAddress');
    if ( empty($professional_mail) || !is_email($professional_mail) ) {
      $professional_mail = 'jakubkolar23@gmail.com';
    }

    $options = get_fields( 'options' );
    $headers = [
      'From: ' . $options['email_from'],
      'Content-Type: text/html; charset=UTF-8',
      'Reply-To: ' . $email,
    ];
    $replace_pairs = [
      '%message_name%' => $name,
      '%message_email%' => $email,
      '%message_text%' => nl2br($message),
      '%partner_url%' => $professional_post->getLink(),
    ];

    $subject = $options['professional_message']['subject'];
    $message = $options['professional_message']['message'];

    $subject = strtr( $subject, $replace_pairs );
    $message = strtr( $message, $replace_pairs );

    $result = wp_mail( $professional_mail, $subject, $message, $headers );

    if ( $result == false ) {
      wp_send_json_error( __( 'Zprávu se nepodařilo odeslat', 'shp-partneri' ), 403 );
      return;
    }

    wp_send_json_success( __( 'Zpráva odeslána', 'shp-partneri' ) );
  }

}