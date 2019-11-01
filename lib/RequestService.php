<?php

class RequestService
{

  const PUBLISH_AFTER = '+5 minutes'; // TODO: change to +24 hours
  const EXPIRATION_TIME = '-5 minutes'; // TODO: change to -90 days
  const EXPIRATION_CHECK_RECURRENCE = 'one_minute'; // TODO: change to twicedaily

  static function init () {
    add_action( 'init', [ get_called_class(), 'registerPostStatus' ] );
    add_action( 'shp/request_service/url_action', [ get_called_class(), 'handleURLAction' ] );
    add_action( 'transition_post_status', [ get_called_class(), 'updatePreviousStatus' ], 10, 3 );
    add_action( 'pending_to_publish', [ get_called_class(), 'schedulePost' ] );
    add_action( 'expired_to_publish', [ get_called_class(), 'setExpiredPostStatus' ] );
    add_action( 'acf/save_post', [ get_called_class(), 'notify' ], 20 );
    add_action( 'wp_ajax_request_message', [ get_called_class(), 'handleMessage' ] );
    add_action( 'wp_ajax_nopriv_request_message', [ get_called_class(), 'handleMessage' ] );
    add_action( 'shp/request_service/expiration_check', [ get_called_class(), 'expirationCheck' ] );

    if ( ! wp_next_scheduled( 'shp/request_service/expiration_check' ) ) {
      wp_schedule_event( time(), self::EXPIRATION_CHECK_RECURRENCE, 'shp/request_service/expiration_check' );
    }
  }

  static function registerPostStatus() {
    $args = [
      'label' => __( 'Expirováno', 'shp-partneri' ),
      'public' => true,
      'show_in_admin_all_list' => true,
      'show_in_admin_status_list' => true,
      'post_type' => [ RequestPost::POST_TYPE ],
      'label_count' => _n_noop( 'Expirováno <span class="count">(%s)</span>', 'Expirováno <span class="count">(%s)</span>' ),
    ];
    register_post_status( 'expired', $args );
  }

  static function handleURLAction( $post_id ) {
    if( isset( $_GET['action'] ) && 'expire' == $_GET['action'] ) {
      self::expire( $post_id );
    }
  }

  static function expire( $post_id ) {
    $request_post = new RequestPost( $post_id );
    $post_status = get_post_status( $request_post->getID() );
    if( ! in_array( $post_status, [ 'pending', 'future', 'publish' ] ) ) {
      wp_die(
        __( 'Poptávka již byla expirována', 'shp-partneri' ),
        __( 'Poptávka již expirována', 'shp-partneri' )
      );
      return;
    }

    $request_post->setStatus( 'expired' );
    $request_post->setMeta( '_expired_at', current_time( 'mysql' ) );

    do_action( 'shp/request_service/expire', $request_post->getID() );    

    wp_die(
			__( 'Poptávka byla úspěšně expirována', 'shp-partneri' ),
			__( 'Poptávka expirována', 'shp-partneri' )
    );
  }

  static function expirationCheck() {
    // Get all requests to expire
    $query = new WP_Query( [
      'post_type' => 'request',
      'posts_per_page' => -1,
      'post_status' => 'publish',
      'date_query' => [
        'before' => self::EXPIRATION_TIME,
      ],
    ] );

    // Set status to expired
    foreach( $query->posts as $post ) {
      $request_post = new RequestPost( $post->ID );
      $request_post->setStatus( 'expired' );
      $request_post->setMeta( '_expired_at', current_time( 'mysql' ) );
    }
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
    $time = self::PUBLISH_AFTER;
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

  static function setExpiredPostStatus( $post ) {
    if( RequestPost::POST_TYPE != get_post_type( $post->ID ) ) return;
    $request_post = new RequestPost( $post->ID );
    $request_post->setStatus('expired');
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

    do_action( 'shp/request_service/approve', $post_id );
  }

  static function handleMessage() {

    if ( ! verify_recaptcha() ) {
      wp_die(
        'ReCaptcha not verified',
        'ReCaptcha not verified',
        [ 'response' => 403 ]
      );
    }

    // Sanitize message post data
    $name = sanitize_text_field( $_POST[ 'name' ] );
    $email = sanitize_email( $_POST[ 'email' ] );
    $message = sanitize_textarea_field( $_POST[ 'message' ] );
    $post_id = intval( $_POST[ 'post_id' ] );

    // Check correct post status
    if ( 'publish' != get_post_status( $post_id ) ) {
      wp_die(
        'Not correct post',
        'Not correct post',
        [ 'response' => 403 ]
      );
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