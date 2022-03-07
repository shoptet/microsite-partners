<?php

namespace Shoptet;

class AdminMetaSearch {

  const POST_TYPES = ['profesionalove', 'request'];

  static function init () {
    add_filter('posts_join', [get_called_class(), 'meta_search_join']);
    add_filter('posts_where', [get_called_class(), 'meta_search_where']);
    add_filter('posts_groupby', [get_called_class(), 'meta_search_groupby']);
  }

  static function is_admin_list_search() {
    global $pagenow, $wp_query;
    return
      is_admin() &&
      $pagenow == 'edit.php' &&
      ! empty($_GET['post_type']) &&
      in_array($_GET['post_type'], self::POST_TYPES) &&
      ! empty($_GET['s']) &&
      $wp_query->is_search;
  }

  static function meta_search_join($join) {
    global $wpdb;
    if (self::is_admin_list_search()) {
      $join .= " LEFT JOIN $wpdb->postmeta ON ($wpdb->posts.ID = $wpdb->postmeta.post_id) ";
    }
    return $join;
  }

  static function meta_search_where($where) {
    global $wpdb, $wp;
    if (self::is_admin_list_search()) {
      $like = '%' . $wpdb->esc_like($wp->query_vars['s']) . '%';
      $where = str_replace(
        "($wpdb->posts.post_excerpt LIKE",
        "($wpdb->postmeta.meta_value LIKE '$like') OR ($wpdb->posts.post_excerpt LIKE",
        $where
      );
    }
    return $where;
  }

  static function meta_search_groupby($groupby) {
    global $wpdb;
    if (self::is_admin_list_search()) {
      if (empty($groupby)) {
        $groupby = "$wpdb->posts.ID";
      }
    }
    return $groupby;
  }

}

AdminMetaSearch::init(); 