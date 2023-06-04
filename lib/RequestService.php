<?php

class RequestService
{

  const PUBLISH_AFTER = '+24 hours';
  const EXPIRATION_TIME = '-90 days';
  const REMINDER_TIME = '-14 days';
  const EXPIRATION_CHECK_RECURRENCE = 'twicedaily';
  const REMINDER_CHECK_RECURRENCE = 'daily';

  static function init () {
    add_action( 'init', [ get_called_class(), 'registerPostStatus' ] );
    add_action( 'init', [ get_called_class(), 'addFuturePreviewRewriteRules' ] );
    add_filter( 'the_posts', [ get_called_class(), 'handleFuturePreview' ], 10, 2 );
    add_action( 'shp/request_service/url_action', [ get_called_class(), 'handleURLAction' ] );
    add_action( 'transition_post_status', [ get_called_class(), 'updatePreviousStatus' ], 10, 3 );
    add_action( 'pending_to_publish', [ get_called_class(), 'schedulePost' ] );
    add_action( 'acf/save_post', [ get_called_class(), 'notify' ], 20 );
    add_action( 'wp_ajax_request_message', [ get_called_class(), 'handleMessage' ] );
    add_action( 'wp_ajax_nopriv_request_message', [ get_called_class(), 'handleMessage' ] );
    add_action( 'shp/request_service/expiration_check', [ get_called_class(), 'expirationCheck' ] );
    // TODO uncomment add_action( 'shp/request_service/reminder_check', [ get_called_class(), 'reminderCheck' ] );
    add_action( 'admin_footer-post.php', [ get_called_class(), 'addPostStatusControlsToAdmin' ] );
    add_filter( 'use_block_editor_for_post_type', [ get_called_class(), 'disableGutenberg' ], 10, 2 );
    add_filter( 'robots_txt', [ get_called_class(), 'filterRobotsTxt' ] );

    if ( ! wp_next_scheduled( 'shp/request_service/expiration_check' ) ) {
      wp_schedule_event( time(), self::EXPIRATION_CHECK_RECURRENCE, 'shp/request_service/expiration_check' );
    }
    if ( ! wp_next_scheduled( 'shp/request_service/reminder_check' ) ) {
      wp_schedule_event( time(), self::REMINDER_CHECK_RECURRENCE, 'shp/request_service/reminder_check' );
    }
  }

  static function registerPostStatus() {
    $args = [
      'label' => __( 'Expirováno', 'shp-partneri' ),
      'public' => true,
      'show_in_admin_all_list' => true,
      'show_in_admin_status_list' => true,
      'post_type' => [ RequestPost::POST_TYPE ],
      'label_count' => _n_noop( 'Expirováno <span class="count">(%s)</span>', 'Expirováno <span class="count">(%s)</span>', 'shp-partneri' ),
    ];
    register_post_status( 'expired', $args );
  }

  static function addFuturePreviewRewriteRules() {
    add_rewrite_tag( '%request_future_preview%', '[^/]' );
    add_rewrite_rule(
      '^future-request/(.+)/?$',
      'index.php?post_type=request&name=$matches[1]&request_future_preview=true',
      'top'
    );
    flush_rewrite_rules();
  }

  static function handleFuturePreview( $posts, $query ) {
    if( ! array_key_exists( 'request_future_preview', $query->query_vars )  ) return $posts;
    
    $new_query = new WP_Query( [
      'post_type' => RequestPost::POST_TYPE,
      'name' => $query->query['name'],
      'posts_per_page' => 1,
      'post_status' => 'future,publish,expired',
    ] );
  
    if( empty( $new_query->posts ) ) return $posts;
  
    $request_post = $new_query->posts[0];
  
    if( 'future' != get_post_status( $request_post ) ) {
      $permalink = get_permalink( $request_post );
      wp_redirect( $permalink, 302 );
      exit;
    }
    
    return [ $request_post ];
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

  static function disableGutenberg( $current_status, $post_type ) {
    if ( $post_type === RequestPost::POST_TYPE ) return false;
    return $current_status;
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

  static function reminderCheck() {
    // Get all requests to remind
    $query = new WP_Query( [
      'post_type' => 'request',
      'posts_per_page' => -1,
      'post_status' => 'publish',
      'date_query' => [
        'before' => self::REMINDER_TIME,
      ],
      'meta_query' => [
        'relation' => 'AND',
        [
          'key' => 'external_post_id',
          'compare' => 'NOT EXISTS',
        ],
        [
          'key' => '_reminded',
          'compare' => 'NOT EXISTS',
        ],
      ],
    ] );

    // Remind
    foreach( $query->posts as $post ) {
      $request_post = new RequestPost( $post->ID );
      do_action( 'shp/request_service/remind', $post->ID );
      $request_post->setMeta( '_reminded', true );
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

    if ( $request_post->getMeta( '_notification_sent' ) ) return;
    
    do_action( 'shp/request_service/approve', $post_id );
    $request_post->setMeta( '_notification_sent', true );
  }

  static function addPostStatusControlsToAdmin() {
    global $post;
    if ( RequestPost::POST_TYPE != $post->post_type ) return;
    ?>
    <script>
      jQuery(function () {
        jQuery('select#post_status').append(`
          <option value="expired" <?php echo ('expired' == $post->post_status) ? 'selected' : ''; ?>>
            <?php _e( 'Expirováno', 'shp-partneri' ); ?>
          </option>
          <option value="publish" <?php echo ('publish' == $post->post_status) ? 'selected' : ''; ?>>
            <?php _e( 'Publikováno', 'shp-partneri' ); ?>
          </option>
        `);
        <?php if ( 'expired' == $post->post_status ): ?>
          jQuery('#post-status-display').text('<?php _e( 'Expirováno', 'shp-partneri' ); ?>');
          jQuery('input#publish').removeAttr('name').val('<?php _e( 'Aktualizovat', 'shp-partneri' ); ?>');
        <?php endif; ?>
      });
    </script>
    <?php
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
    $post_status = get_post_status( $post_id );

    // Check correct post status
    if ( ! in_array( $post_status, [ 'future', 'publish' ] ) ) {
      wp_die(
        __( 'Omlouváme se, ale na tuto poptávku již není možné reagovat. V mezičase ji autor označil jako vyřešenou. Zkuste se ale mrknout na další poptávky. Díky!', 'shp-partneri' ),
        'Not correct post',
        [ 'response' => 403 ]
      );
      return;
    }

    if ( is_blacklisted( $name, $email, $message ) ) {
      wp_die();
      return;
    }

    $message_arr = [
      'name' => $name,
      'email' => $email,
      'message' => $message,
    ];

    add_row( 'field_646b6d6556abb', $message_arr, $post_id );

    do_action( 'shp/request_message/validate', $post_id, $message_arr );
  }

  static function filterRobotsTxt( $robot_text ) {
    return $robot_text . "
Disallow: /future-request/*
";
  }

}