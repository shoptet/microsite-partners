<?php

class ExternalCommentsSyncer
{

  public static function init()
  {
    add_action('rest_api_init', [ get_called_class(), 'register_rest_route']);
    add_action('external_comments_syncer/sync_all', [ get_called_class(), 'sync_all']);

    if (!wp_next_scheduled('external_comments_syncer/sync_all')) {
      wp_schedule_event(time(), 'daily', 'external_comments_syncer/sync_all');
    }
  }

  static function register_rest_route() {
    register_rest_route( 'partner-comments/v1', '/partner/(?P<email>.+)/(?P<token>.+)', [
      'methods' => 'GET',
      'callback' => [ get_called_class(), 'rest_endpoint_callback' ],
      'permission_callback' => '__return_true',
    ] );
  }

  static function is_enable() {
    return (
      defined('EXTERNAL_COMMENTS_SYNCER_ENABLE') &&
      defined('EXTERNAL_COMMENTS_SYNCER_URL_BASE') &&
      defined('EXTERNAL_COMMENTS_SYNCER_TOKEN') &&
      EXTERNAL_COMMENTS_SYNCER_ENABLE
    );
  }

  static function rest_endpoint_callback($data) {
    if (!self::is_enable()) {
      wp_send_json_error(null, 404);
    }
    if ($data['token'] != EXTERNAL_COMMENTS_SYNCER_TOKEN) {
      wp_send_json_error(null, 401);
    }
    $query = new WP_Query([
      'post_type' => 'profesionalove',
      'meta_key' => 'emailAddress',
      'meta_value' => $data['email'],
      'posts_per_page' => 1,
      'fields' => 'ids',
    ]);
    if (!$query->have_posts()) return [];
    $post_id = $query->posts[0];
    $comments = get_comments([
      'post_id' => $post_id,
      'status' => 'approve',
      'hierarchical' => 'threaded',
    ]);
    $comments = array_map(function($comment) {
      return self::format_comment($comment);
    }, $comments);
    return $comments;
  }

  static function format_comment($comment) {
    $comment = $comment->to_array();
    $comment['rating'] = get_comment_meta($comment['comment_ID'], 'rating', true);
    $comment['external_comment_id'] = get_comment_meta($comment['comment_ID'], 'external_comment_id', true);
    if (isset($comment['children'])) {
      $comment['children'] = array_map(function($comment) {
        return self::format_comment($comment);
      }, $comment['children']);
    }
    return $comment;
  }

  static function fetch_comments($email) {
    $endpoint_url = rtrim(EXTERNAL_COMMENTS_SYNCER_URL_BASE, '/') . '/' . $email . '/' . EXTERNAL_COMMENTS_SYNCER_TOKEN;
    $response = wp_remote_get($endpoint_url);
    if (is_wp_error($response)) {
      error_log($response->get_error_message());
      return false;
    }
    if (200 != $response['response']['code']) {
      error_log(json_encode($response));
      return false;
    }
    return json_decode($response['body'], true);
  }

  static function sync_all() {
    if (!self::is_enable()) {
      return;
    }
    $query = new WP_Query([
      'post_type' => ProfessionalPost::POST_TYPE,
      'posts_per_page' => -1,
      'post_status' => 'publish',
      'fields' => 'ids',
    ]);
    foreach ($query->posts as $post_id) {
      $email = get_post_meta($post_id, 'emailForSync', true) ?: get_post_meta($post_id, 'emailAddress', true);
      if (empty($email)) {
        continue;
      }
      $external_comments = self::fetch_comments($email);
      if (!is_array($external_comments)) {
        continue;
      }
      foreach ($external_comments as $comment) {
        self::create_comment($post_id, $comment);
      }
      // Deleting comments
      // TODO: Test it
      // stop_the_insanity();
      // $comments = get_comments([
      //   'post_id' => $post_id,
      //   'status' => 'approve',
      //   'meta_key' => 'external_comment_id',
      //   'meta_compare' => 'EXISTS',
      // ]);
      // if (!is_array($comments)) {
      //   continue;
      // }
      // foreach ($comments as $comment) {
      //   if (!self::find_comment($comment, $external_comments)) {
      //     wp_delete_comment($comment, false);
      //   }
      // }
    }
  }

  static function create_comment($post_id, $comment, $parent = 0) {
    $existing_comments = get_comments([
      'meta_key' => 'external_comment_id',
      'meta_value' => $comment['comment_ID'],
      'status' => 'approve',
    ]);
    if ($existing_comments) {
      // This comment is already synced
      $comment_id = $existing_comments[0]->comment_ID;
    } else if ($comment['external_comment_id']) {
      // This site is the original source of this comment
      $comment_id = $comment['external_comment_id'];
    } else {
      // Sync this comment
      $comment['comment_meta'] = [
        'external_comment_id' => $comment['comment_ID'],
        'external_comment_parent' => $comment['comment_parent'],
        'external_post_id' => $comment['comment_post_ID'],
        'rating' => $comment['rating'],
      ];
      $comment['user_id'] = 0;
      $comment['comment_post_ID'] = $post_id;
      $comment['comment_parent'] = $parent;
      $comment_id = wp_insert_comment($comment);
    }
    // Sync child comments
    if (is_array($comment['children'])) {
      foreach ($comment['children'] as $comment_child) {
        self::create_comment($post_id, $comment_child, $comment_id);
      }
    }
  }

  static function find_comment($comment, $external_comments) {
    $external_comment_id = get_comment_meta($comment->comment_ID, 'external_comment_id', true);
    foreach ($external_comments as $external_comment) {
      if ($external_comment_id == $external_comment['comment_ID']) {
        return true;
      }
      if (is_array($external_comment['children'])) {
        if (self::find_comment($comment, $external_comment['children'])) {
          return true;
        }
      }
    }
    return false;
  }

}
