<?php

use Vimeo\Vimeo;

class CourseService
{

  const POST_TYPE = 'course';

  static function init () {
    add_action( 'acf/save_post', [ get_called_class(), 'registerPostStatus' ] );
    add_action( 'edit_form_after_title', [ get_called_class(), 'displayVideo' ] );
  }

  static function registerPostStatus ($post_id) {
    if (get_post_type($post_id) != self::POST_TYPE) {
      return;
    }
    $external_id = get_field('external_id', $post_id);
    if ($external_id) {
      self::syncVimeoData($post_id, $external_id);
    }
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
    $external_id = get_post_meta($post->ID, 'external_id', true);
    if ($external_id) {
      $allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share';
      echo "<iframe style='margin-top: 20px' width='560' height='315' src='https://player.vimeo.com/video/$external_id' frameborder='0' allow='$allow' allowfullscreen></iframe>";
    }
  }

}