<?php

class ExternalRequestsSyncer
{

  public static function init()
  {
    add_action('rest_api_init', [ get_called_class(), 'register_rest_route']);
    add_action('external_requests_syncer/sync_all', [ get_called_class(), 'sync_all']);
    add_action('shp/request_service/expire', [ get_called_class(), 'expire_synced' ]);

    if (!wp_next_scheduled('external_requests_syncer/sync_all')) {
      wp_schedule_event(time(), 'daily', 'external_requests_syncer/sync_all');
    }
  }

  static function register_rest_route() {
    register_rest_route( 'requests/v1', '/list/(?P<token>.+)', [
      'methods' => 'GET',
      'callback' => [ get_called_class(), 'rest_endpoint_callback_list' ],
      'permission_callback' => '__return_true',
    ] );
    register_rest_route( 'requests/v1', '/expire/(?P<post_id>.+)/(?P<token>.+)', [
      'methods' => 'GET',
      'callback' => [ get_called_class(), 'rest_endpoint_callback_expire' ],
      'permission_callback' => '__return_true',
    ] );
  }

  static function expire_synced($post_id) {
    if (!self::is_enable()) {
      return;
    }
    if (self::is_pull_enable()) {
      $post_id = get_post_meta($post_id, 'external_post_id', true);
    }
    if ($post_id) {
      $endpoint_url = rtrim(EXTERNAL_REQUESTS_SYNCER_URL_BASE, '/') . '/expire/' . $post_id . '/' . EXTERNAL_REQUESTS_SYNCER_TOKEN;
      wp_remote_get($endpoint_url);
    }
  }

  static function is_pull_enable() {
    return (
      self::is_enable() &&
      defined('EXTERNAL_REQUESTS_SYNCER_PULL') && EXTERNAL_REQUESTS_SYNCER_PULL
    );
  }

  static function is_enable() {
    return (
      defined('EXTERNAL_REQUESTS_SYNCER_ENABLE') && EXTERNAL_REQUESTS_SYNCER_ENABLE &&
      defined('EXTERNAL_REQUESTS_SYNCER_URL_BASE') &&
      defined('EXTERNAL_REQUESTS_SYNCER_TOKEN')
    );
  }

  static function rest_endpoint_callback_list($data) {
    if (!self::is_enable()) {
      wp_send_json_error(null, 404);
    }
    if ($data['token'] != EXTERNAL_REQUESTS_SYNCER_TOKEN) {
      wp_send_json_error(null, 401);
    }
    $query = new WP_Query([
      'post_type' => RequestPost::POST_TYPE,
      'post_status' => 'publish',
      'posts_per_page' => -1,
      'date_query' => [
        'before' => date('Y-m-d', strtotime('-16 days')),
        'after' => '2023-01-01',
      ],
    ]);
    $posts = array_map(function($post) {
      return self::format_post($post);
    }, $query->posts);
    return $posts;
  }

  static function rest_endpoint_callback_expire($data) {
    if (!self::is_enable()) {
      wp_send_json_error(null, 404);
    }
    if ($data['token'] != EXTERNAL_REQUESTS_SYNCER_TOKEN) {
      wp_send_json_error(null, 401);
    }
    if (self::is_pull_enable()) {
      $posts = get_posts([
        'post_type' => RequestPost::POST_TYPE,
        'meta_key' => 'external_post_id',
        'meta_value' => $data['post_id'],
      ]);
    } else {
      $posts = get_posts([
        'post_type' => RequestPost::POST_TYPE,
        'p' => $data['post_id'],
      ]);
    }
    if ($posts) {
      RequestService::expire($posts[0]->ID);
    }
  }

  static function format_post($post) {
    $post = $post->to_array();
    $id = $post['ID'];
    $post['author_name'] = get_post_meta($id, 'author_name', true);
    $post['author_email'] = get_post_meta($id, 'author_email', true);
    $post['url'] = get_post_meta($id, 'url', true);
    $post['category'] = get_post_meta($id, 'category', true);
    $post['is_shoptet'] = get_post_meta($id, 'is_shoptet', true);
    $post['shoptet_id'] = get_post_meta($id, 'shoptet_id', true);
    $post['external_post_id'] = get_post_meta($id, 'external_post_id', true);
    return $post;
  }

  static function fetch_posts() {
    $endpoint_url = rtrim(EXTERNAL_REQUESTS_SYNCER_URL_BASE, '/') . '/list/' . EXTERNAL_REQUESTS_SYNCER_TOKEN;
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
    if (!self::is_pull_enable()) {
      return;
    }
    $external_posts = self::fetch_posts();
    if (!is_array($external_posts)) {
      return;
    }
    foreach ($external_posts as $post) {
      self::create_post($post);
    }
  }
  
  static function create_post($post) {
    $existing_post = get_posts([
      'meta_key' => 'external_post_id',
      'meta_value' => $post['ID'],
      'post_status' => 'future,publish,expired',
      'post_type' => RequestPost::POST_TYPE,
    ]);
    if ($existing_post) {
      // This post is already synced
    } else if ($post['external_post_id']) {
      // This site is the original source of this post
    } else {
      // Sync this post
      $term_id = self::map_external_term($post['category']);
      if ($term_id === false) {
        throw new Exception( 'No related term to external term id ' . $post['category'] );
      }
      $args = [
        'post_title' => $post['post_title'],
        'post_content' => $post['post_content'],
        'post_type' => RequestPost::POST_TYPE,
        'post_status' => 'publish',
        'meta_input' => [
          'author_name' => $post['author_name'],
          'author_email' => $post['author_email'],
          'url' => $post['url'],
          'category' => $term_id,
          'is_shoptet' => $post['is_shoptet'],
          'shoptet_id' => $post['shoptet_id'],
          'external_post_id' => $post['ID'],
        ],
      ];
      $new_post_id = wp_insert_post($args);
      wp_set_post_terms($new_post_id, [$term_id], RequestPost::TAXONOMY);
      $new_post = get_post($new_post_id);
      update_post_meta($new_post_id, '_previous_status', 'publish');
      RequestService::schedulePost($new_post);
      RequestService::notify($new_post_id);
    }
  }

  static function map_external_term($external_term_id) {
    $terms = get_terms([
      'hide_empty' => false,
      'taxonomy' => RequestPost::TAXONOMY,
      'meta_key' => 'external_category_id',
      'meta_value' => $external_term_id,
      'fields' => 'ids',
    ]);
    if ($terms) {
      return intval($terms[0]);
    }
    return false;
  }

}
