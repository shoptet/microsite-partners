<?php

class StarterSite extends TimberSite {

  function __construct() {
    add_theme_support( 'post-formats' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'menus' );
    add_theme_support( 'html5', array( 'comment-list', 'comment-form', 'search-form', 'gallery', 'caption' ) );
    add_filter( 'timber_context', array( $this, 'add_to_context' ) );
    add_filter( 'get_twig', array( $this, 'add_to_twig' ) );
    add_action( 'init', array( $this, 'register_post_types' ) );
    add_action( 'init', array( $this, 'register_taxonomies' ) );
    add_action( 'wp_enqueue_scripts', array( $this, 'load_styles' ) );
    add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ) );

    $this->clean_header();
    parent::__construct();
  }

  function register_post_types() {
    //this is where you can register custom post types
  }

  function register_taxonomies() {
    //this is where you can register custom taxonomies
  }

  function add_to_context( $context ) {
    $context['header_menu'] = new Timber\Menu( 'header-menu' );
    $context['archive_link'] =  get_post_type_archive_link( 'profesionalove' );
    $context['all_categories'] = Timber::get_terms('category_professionals', 'hide_empty=true');
    $context['site'] = $this;
    $context['show_breadcrumb'] = true;
    $context['options'] = get_fields('options');
    $context['config'] = [];
    $context['config']['G_RECAPTCHA_SITE_KEY'] = G_RECAPTCHA_SITE_KEY;
    $context['link']['archive']['request'] = get_post_type_archive_link( 'request' );
    $context['request_posts_count'] = wp_count_posts( 'request' );
    return $context;
  }

  function add_to_twig( $twig ) {
    /* this is where you can add your own functions to twig */
    $twig->addExtension( new Twig_Extension_StringLoader() );
    $twig->addFilter('static_assets', new Twig_SimpleFilter('static_assets', array($this, 'static_assets')));
    $twig->addFilter('truncate', new Twig_SimpleFilter('truncate', array($this, 'truncate')));
    $twig->addFilter('display_url', new Twig_SimpleFilter('display_url', array($this, 'display_url')));
    $twig->addFilter('currency_i18n', new Twig_SimpleFilter('currency_i18n', array($this, 'currency_i18n')));
    $twig->addFilter('ensure_protocol', new Twig_SimpleFilter('ensure_protocol', array($this, 'ensure_protocol')));
    $twig->addFilter('date_i18n', new Twig_SimpleFilter('date_i18n', array($this, 'date_i18n')));
    $twig->addFilter('request_state', new Twig_SimpleFilter('request_state', array($this, 'request_state')));
    $twig->addFilter('remove_lastname', new Twig_SimpleFilter('remove_lastname', array($this, 'remove_lastname')));
    $twig->addFilter('posts_in_term', new Twig_SimpleFilter('posts_in_term', array($this, 'posts_in_term')));
    return $twig;
  }

  function clean_header() {
    /* Clean WordPress header (for cleaning emoji and oEmbed is used a plugin) */
    remove_action( 'wp_head', 'rsd_link' );
    remove_action( 'wp_head', 'wlwmanifest_link' );
    remove_action( 'wp_head', 'wp_shortlink_wp_head' );
    remove_action( 'wp_head', 'wp_generator' );
  }

  function load_styles() {
    /* Load styles and add a cache-breaking URL parameter */

    $fileName = '/assets/main.css';
    $fileUrl = get_template_directory_uri() . $fileName;
    $filePath = get_template_directory() . $fileName;
    wp_enqueue_style( 'main', $fileUrl, array(), filemtime($filePath), 'all' );

  }

  function load_scripts() {
    /* Load scripts and add a cache-breaking URL parameter */

    $fileName = '/assets/vendor.js';
    $fileUrl = get_template_directory_uri() . $fileName;
    $filePath = get_template_directory() . $fileName;
    wp_enqueue_script( 'vendor', $fileUrl, array(), filemtime($filePath), true );

    $fileName = '/assets/main.js';
    $fileUrl = get_template_directory_uri() . $fileName;
    $filePath = get_template_directory() . $fileName;
    wp_enqueue_script( 'main', $fileUrl, array('vendor'), filemtime($filePath), true );

    if ( is_singular( 'request' ) ) {
      wp_enqueue_script( 'recaptcha', '//www.google.com/recaptcha/api.js' );
    }

    wp_enqueue_script( 'fontawesome', 'https://use.fontawesome.com/releases/v5.0.6/js/all.js' );
    
  }

  function static_assets( $filePath ) {
    return $this->theme->link . '/assets/' . $filePath;
  }

  function truncate( $string, $limit, $separator = '...' ) {
    if (strlen($string) > $limit) {
      $newlimit = $limit - strlen($separator);
      $s = substr($string, 0, $newlimit + 1);
      return substr($s, 0, strrpos($s, ' ')) . $separator;
    }
    return $string;
  }

  function ensure_protocol( $url ) {
    $url_with_protocol = $url;
    if ( ! preg_match( '/^(http:|https:)?\/\//i', $url ) ) {
      $url_with_protocol = '//' . $url_with_protocol;
    }
    return $url_with_protocol;
  }

  function display_url( $url ) {
    // Romove protocol
    if (substr( $url, 0, 7 ) === 'http://') {
      $url = substr( $url, 7 );
    } else if (substr( $url, 0, 8 ) === 'https://') {
      $url = substr( $url, 8 );
    } else if (substr( $url, 0, 2 ) === '//') {
      $url = substr( $url, 2 );
    }
    // Remove www subdomain
    if (substr( $url, 0, 4 ) === 'www.') {
      $url = substr( $url, 4 );
    }
    // Remove last slash
    if (substr( $url, -1 ) === '/') {
      $url = substr( $url, 0, -1 );
    }

    return $url;
  }

  function date_i18n( $date ) {
    $date_format = get_option( 'date_format' );
    $timestamp = strtotime( $date );
    return date_i18n( $date_format, $timestamp );
  }

  function request_state( $post_status ) {
    $request_state = $post_status;
    switch( $post_status ) {
      case 'publish':
      $request_state = __( 'Aktivní', 'shp-partneri' );
      break;
      case 'expired':
      $request_state = __( 'Expirovaná', 'shp-partneri' );
      break;
      case 'pending':
      $request_state = __( 'Čeká na schválení', 'shp-partneri' );
      break;
      case 'future':
      $request_state = __( 'Naplánováno', 'shp-partneri' );
      break;
    }
    return $request_state;
  }

  function currency_i18n( $currency ) {
    switch ( $currency ) {
      case 'CZK':
      $currency_i18n = __( 'Kč', 'shp-partneri' );
      break;
      case 'EUR':
      $currency_i18n = __( '&euro;', 'shp-partneri' );
      break;
      default:
      $currency_i18n = $currency;
    }
    return $currency_i18n;
  }

  function remove_lastname( $fullname ) {
    $words = preg_split( '/\s+/', $fullname );
    $removed_lastname = '';
    for( $i = 0; $i < count( $words ); $i++  ) {
      $word = $words[$i];
      if( $i == 0 ) {
        $removed_lastname .= $word;
        continue;
      }
      $first_letter = mb_substr( $word, 0, 1 );
      $removed_lastname .= ' ' . $first_letter . '.';
    }
    return $removed_lastname;
  }

  function posts_in_term( $term ) {
    $query = new WP_Query( [
      'post_type' => 'request',
      'posts_per_page' => -1,
      'post_status' => [ 'publish', 'expired' ],
      'fields' => 'ids',
      'tax_query' => [
        [
          'taxonomy' => $term->taxonomy,
          'terms' => $term->term_id,
        ]
      ],
    ] );
    return $query->found_posts;
  }
}