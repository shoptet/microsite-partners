<?php

use Vimeo\Vimeo;

class WebinarService
{

  const POST_TYPE = 'webinar';

  static function init () {
    add_action( 'acf/save_post', [ get_called_class(), 'registerPostStatus' ] );
    add_action( 'edit_form_after_title', [ get_called_class(), 'displayVideo' ] );
    add_filter( 'the_content', [ get_called_class(), 'addLinksInContent' ] );
  }

  static function registerPostStatus ($post_id) {
    if (get_post_type($post_id) != self::POST_TYPE) {
      return;
    }
    $external_id_source = get_field('external_id_source', $post_id);
    $external_id = get_field('external_id', $post_id);
    if ($external_id) {
      if ($external_id_source == 'vimeo') {
        self::syncVimeoData($post_id, $external_id);
      } else {
        self::syncYoutubeData($post_id, $external_id);
      }
    }
  }

  static function syncYoutubeData ($post_id, $external_id) {
    $video = self::fetchYoutubeVideo($external_id);
    if ($video) {
      $timestamp = strtotime($video['snippet']['publishedAt']);
      $interval = new DateInterval($video['contentDetails']['duration']);
      $duration = ($interval->h * 3600 + $interval->i * 60 + $interval->s);
      wp_update_post([
        'ID' => $post_id,
        'post_title' => $video['snippet']['title'],
        'post_content' => $video['snippet']['description'],
        'post_date' => wp_date('Y-m-d H:i:s', $timestamp),
        'meta_input' => [
          'duration' => $duration,
        ],
      ]);
    }
  }

  static function fetchYoutubeVideo ($external_id) {
    $api_key = GOOGLE_API_KEY;
    $endpoint_url = "https://www.googleapis.com/youtube/v3/videos?id=$external_id&key=$api_key&part=snippet,contentDetails";
    $response = wp_remote_get($endpoint_url);
    if (is_wp_error($response)) {
      error_log($response->get_error_message());
      return false;
    }
    if (200 != $response['response']['code']) {
      error_log(json_encode($response));
      return false;
    }
    $body = json_decode($response['body'], true);
    if (!isset($body['items']) || !count($body['items'])) {
      return false;
    }
    return $body['items'][0];
  }

  static function syncVimeoData ($post_id, $external_id) {
    $video = self::fetchVimeoVideo($external_id);
    if ($video) {
      $timestamp = strtotime($video['release_time']);
      wp_update_post([
        'ID' => $post_id,
        'post_title' => $video['name'],
        'post_content' => $video['description'],
        'post_date' => wp_date('Y-m-d H:i:s', $timestamp),
        'meta_input' => [
          'duration' => $video['duration'],
          'image_url' => $video['pictures']['base_link'],
        ],
      ]);
    }
  }

  static function fetchVimeoVideo ($external_id) {
    $client = new Vimeo(VIMEO_CLIENT_ID, VIMEO_CLIENT_SECRET, VIMEO_ACCESS_TOKEN);
    $response = $client->request("/videos/$external_id", [], 'GET');
    if (200 != $response['status'] || !isset($response['body'])) {
      error_log(json_encode($response));
      return false;
    }
    return $response['body'];
  }

  static function displayVideo ($post) {
    if ($post->post_type != self::POST_TYPE) {
      return;
    }
    $external_id_source = get_field('external_id_source', $post->ID);
    $external_id = get_field('external_id', $post->ID);
    if ($external_id) {
      $allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share';
      if ($external_id_source == 'vimeo') {
        echo "<iframe style='margin-top: 20px' width='560' height='315' src='https://player.vimeo.com/video/$external_id' frameborder='0' allow='$allow' allowfullscreen></iframe>";
      } else {
        echo "<iframe style='margin-top: 20px' width='560' height='315' src='https://www.youtube.com/embed/$external_id?rel=0' frameborder='0' allow='$allow' allowfullscreen></iframe>";
      }
    }
  }

  static function addLinksInContent ($content) {
    if (get_post_type() != self::POST_TYPE) {
      return $content;
    }
    return urls_to_links($content);
  }

}