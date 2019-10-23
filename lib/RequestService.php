<?php

class RequestService
{

  static function init () {
    add_action( 'init', [ get_called_class(), 'registerPostStatus' ] );
    add_action( 'init', [ get_called_class(), 'handleExpirationURL' ] );
    add_action( 'transition_post_status', [ get_called_class(), 'updatePreviousStatus' ], 10, 3 );
    add_action( 'pending_to_publish', [ get_called_class(), 'schedulePost' ] );
    add_action( 'acf/save_post', [ get_called_class(), 'notify' ], 20 );
    add_action( 'wp_ajax_request_message', [ get_called_class(), 'handleMessage' ] );
    add_action( 'wp_ajax_nopriv_request_message', [ get_called_class(), 'handleMessage' ] );
  }

  static function registerPostStatus() {
    $args = [
      'label' => __( 'Expirováno', 'shp-partneri' ),
      'public' => true,
      'show_in_admin_all_list' => false,
      'show_in_admin_status_list' => true,
      'post_type' => [ RequestPost::POST_TYPE ],
      'label_count' => _n_noop( 'Expirováno <span class="count">(%s)</span>', 'Expirováno <span class="count">(%s)</span>' ),
    ];
    register_post_status( 'expired', $args );
  }

  static function handleExpirationURL() {
    if( ! isset( $_GET['request_expiration_token'] ) || '' === $_GET['request_expiration_token'] ) return;
    $request_expiration_token = $_GET['request_expiration_token'];

    $request_post = RequestPost::getByExpirationToken( $request_expiration_token );

    if( ! $request_post ) {
      wp_die(
        __( 'Vypadá to, že jste zadali neplatný odkaz. Zkuste to prosím znovu.', 'shp-partneri' ),
        __( 'Neplatný odkaz', 'shp-partneri' )
      );
      return;
    }

    $post_status = get_post_status( $request_post->getID() );
    if( ! in_array( $post_status, [ 'pending', 'future', 'publish' ] ) ) {
      wp_die(
        __( 'Poptávka je již expirována', 'shp-partneri' ),
        __( 'Poptávka expirována', 'shp-partneri' )
      );
      return;
    }

    $postarr = [
      'ID' => $request_post->getID(),
      'post_status' => 'expired',
    ];
    wp_update_post( $postarr );
    $request_post->setMeta( '_expired_at', current_time( 'mysql' ) );

    wp_die(
			__( 'Poptávka byla expirována', 'shp-partneri' ),
			__( 'Poptávka expirována', 'shp-partneri' )
    );

    do_action( 'shp/request_service/expire', $request_post->getID() );    
  }

  static function updatePreviousStatus( $new_status, $old_status, $post ) {
    if( $new_status == $old_status ) return;
    if( RequestPost::POST_TYPE != get_post_type( $post->ID ) ) return;

    $request_post = new RequestPost( $post->ID );
    $request_post->setMeta( '_previous_status', $old_status );
  }

  static function schedulePost( $post ) {
    if( RequestPost::POST_TYPE != get_post_type( $post->ID ) ) return;

    $date_format = 'Y-m-d H:i:s';
    $time = '+24 hours';
    $current_time = current_time( 'timestamp', false );
    $current_time_gmt = current_time( 'timestamp', true );
    $future_date = strtotime( $time, $current_time );
    $future_date_gmt = strtotime( $time, $current_time_gmt );
    $formated_future_date = date( $date_format, $future_date );
    $formated_future_date_gmt = date( $date_format, $future_date_gmt );

    $postarr = [
      'ID' => $post->ID,
      'post_status' => 'future',
      'post_date' => $formated_future_date,
      'post_date_gmt' => $formated_future_date_gmt,
    ];
    wp_update_post( $postarr );
  }

  static function notify( $post_id ) {
    if ( RequestPost::POST_TYPE != get_post_type( $post_id ) ) return;

    $request_post = new RequestPost( $post_id );

    // Check correct post status transition
    if (
      'publish' != $request_post->getMeta( '_previous_status' ) ||
      'future' != get_post_status( $post_id )
    ) {
      return;
    }

    if (
      ! $request_post->getMeta( '_notification_sent' ) &&
      $request_post->getMeta( 'author_email' )
    ) {
      do_action( 'shp/request_service/approve', $post_id );
      $request_post->setMeta( '_notification_sent', true );
    }
  }

  function handleMessage() {

    // Verify reCAPTCHA
    $recaptcha_response = sanitize_text_field( $_POST[ 'g-recaptcha-response' ] );
    $recaptcha = new \ReCaptcha\ReCaptcha( G_RECAPTCHA_SECRET_KEY );
    $resp = $recaptcha->verify( $recaptcha_response, $_SERVER['REMOTE_ADDR'] );
    if ( ! $resp->isSuccess() ) {
      status_header( 403 );
      die();
    }
    
    // Sanitize message post data
    $name = sanitize_text_field( $_POST[ 'name' ] );
    $email = sanitize_email( $_POST[ 'email' ] );
    $message = sanitize_textarea_field( $_POST[ 'message' ] );
    $post_id = intval( $_POST[ 'post_id' ] );

    // Check correct post status
    if ( 'publish' != get_post_status( $post_id ) ) {
      wp_die();
      return;
    }
    
    // WordPress comments blacklist check
    $user_url = '';
    $user_ip = $_SERVER['REMOTE_ADDR'];
    $user_ip = preg_replace( '/[^0-9a-fA-F:., ]/', '', $user_ip );
    $user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : '';
    $user_agent = substr( $user_agent, 0, 254 );
    $is_blacklisted = wp_blacklist_check( $name, $email, $user_url, $message, $user_ip, $user_agent );

    if ( $is_blacklisted ) {
      wp_die();
      return;
    }

    $message_arr = [
      'name' => $name,
      'email' => $email,
      'message' => $message,
    ];

    do_action( 'shp/request_message/validate', $post_id, $message_arr );

  }

}