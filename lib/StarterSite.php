<?php

class StarterSite extends TimberSite {

  function __construct() {
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
    $context['logo_url'] =  get_custom_logo_url();
    $context['all_categories'] = Timber::get_terms( ProfessionalPost::TAXONOMY, 'hide_empty=true');
    $context['site'] = $this;
    $context['show_breadcrumb'] = true;
    $context['options'] = get_fields('options');
    $context['config'] = [];
    $context['config']['G_RECAPTCHA_SITE_KEY'] = G_RECAPTCHA_SITE_KEY;
    $context['link']['request']['archive'] = get_post_type_archive_link( 'request' );
    $context['link']['webinar']['archive'] = get_post_type_archive_link( 'webinar' );
    $context['link']['course']['archive'] = get_post_type_archive_link( 'course' );
    $context['request_posts_count'] = wp_count_posts( 'request' );
    $context['webinar_posts_count'] = wp_count_posts( 'webinar' );
    $context['course_posts_count'] = wp_count_posts( 'course' );
    $context['read_only_enabled'] = apply_filters( 'read_only_enabled', false );
    $context['footer'] = Shoptet\ShoptetExternal::get_footer();
    $context['shoptet_url'] = Shoptet\ShoptetHelpers::get_shoptet_url();
    $context['admin_post_url'] = admin_url( 'admin-post.php' );
    $context['locale'] = get_locale();
    if (is_user_logged_in()) {
      $context['current_user'] = new Timber\User();
      // TODO
      if (2936) {
        $context['current_user_post'] = new Timber\Post(2936);
      }
    }
    return $context;
  }

  function add_to_twig( $twig ) {
    /* this is where you can add your own functions to twig */
    $twig->addExtension( new Twig_Extension_StringLoader() );
    $twig->addFilter( new Timber\Twig_Filter('static_assets', array($this, 'static_assets')));
    $twig->addFilter( new Timber\Twig_Filter('truncate', 'truncate'));
    $twig->addFilter( new Timber\Twig_Filter('display_url', array($this, 'display_url')));
    $twig->addFilter( new Timber\Twig_Filter('currency_i18n', array($this, 'currency_i18n')));
    $twig->addFilter( new Timber\Twig_Filter('display_price', array($this, 'display_price')));
    $twig->addFilter( new Timber\Twig_Filter('ensure_protocol', array($this, 'ensure_protocol')));
    $twig->addFilter( new Timber\Twig_Filter('date_i18n', array($this, 'date_i18n')));
    $twig->addFilter( new Timber\Twig_Filter('request_state', array($this, 'request_state')));
    $twig->addFilter( new Timber\Twig_Filter('remove_lastname', array($this, 'remove_lastname')));
    $twig->addFilter( new Timber\Twig_Filter('posts_in_term', array($this, 'posts_in_term')));
    $twig->addFilter( new Timber\Twig_Filter('keep_query_string', array($this, 'keep_query_string')));
    $twig->addFilter( new Timber\Twig_Filter('average_rating', array($this, 'average_rating')));
    $twig->addFilter( new Timber\Twig_Filter('verified_level', array($this, 'verified_level')));
    $twig->addFilter( new \Timber\Twig_Filter('apply_shortcodes', 'apply_shortcodes'));
    $twig->addFilter( new \Timber\Twig_Filter('youtube_thumbnail', 'get_youtube_thumbnail_url'));
    $twig->addFilter( new \Timber\Twig_Filter('time_interval', 'get_time_interval'));
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
    // Remove the Gutenberg CSS
    wp_dequeue_style( 'wp-block-library' );

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

    wp_localize_script( 'main', 'local',
      [
        'recaptchaRequiredMessage' => __( 'Zaškrtněte prosím, že nejste robot', 'shp-partneri' ),
        'contactAllFieldsRequiredMessage' => __( 'Vyplňte prosím všechna pole', 'shp-partneri' ),
        'contactCorrectEmailMessage' => __( 'Vyplňte prosím správný e-mail', 'shp-partneri' ),
        'contactFormSent' => __( 'Odesláno!', 'shp-partneri' ),
        'contactFormErrorMessage' => __( 'Omlouvám se, ale při odeslání došlo k chybě. Zkuste to prosím později.', 'shp-partneri' ),
        'select2_placeholder' => __( 'Vyberte', 'shp-partneri' ),
        'select2_searching' => __( 'Načítám...', 'shp-partneri' ),
        'select2_loading_more' => __( 'Načítám další...', 'shp-partneri' ),
      ]
    );

    wp_enqueue_script( 'recaptcha', '//www.google.com/recaptcha/api.js' );

    wp_enqueue_script( 'fontawesome', 'https://use.fontawesome.com/releases/v5.0.6/js/all.js' );

    wp_localize_script('main', 'dl', [
      'page' => [
        'currency' => get_currency(),
        'category' => 'not_available_DL',
        'subCategory' => 'not_available_DL',
        'subSubCategory' => 'not_available_DL',
        'environment' => defined('WP_DEBUG') && WP_DEBUG ? 'dev' : 'live',
        'language' => get_language(),
        'hostname' => $_SERVER['SERVER_NAME'],
        'title' => get_the_title(),
        'path' => 'not_available_DL',
        'url' => 'not_available_DL',
        'params' => '# / ?',
        'type' => get_datalayer_type(),
      ],
      'user' => [
        'accountHash' => 'not_available_DL',
        'email' => 'not_available_DL',
        'accountId' => 'not_available_DL',
        'name' => 'not_available_DL',
        'surname' => 'not_available_DL',
      ],
      'partner' => get_datalayer_partners(),
    ]);
    
  }

  function static_assets( $filePath ) {
    return $this->theme->link . '/assets/' . $filePath;
  }

  function ensure_protocol( $url ) {
    $url_with_protocol = $url;
    if ( ! preg_match( '/^(http:|https:)?\/\//i', $url ) ) {
      $url_with_protocol = 'http://' . $url_with_protocol;
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
      $request_state = __( 'Poptávka již byla vyřízena', 'shp-partneri' );
      break;
      case 'pending':
      $request_state = __( 'Čeká na schválení', 'shp-partneri' );
      break;
      case 'future':
      $request_state = __( 'Aktivní jen pro Shoptet partnery', 'shp-partneri' );
      break;
    }
    return $request_state;
  }

  function currency_i18n( $currency ) {
    switch ( $currency ) {
      case 'CZK':
      $currency_i18n = 'Kč';
      break;
      case 'EUR':
      $currency_i18n = '&euro;';
      break;
      default:
      switch ( get_locale() ) {
        case 'hu_HU':
        $currency_i18n = 'Ft';
        break;
        default:
        $currency_i18n = $currency;
      }
    }
    return $currency_i18n;
  }

  function display_price( $post ) {
    $display_price = '';
    
    if ( $price_per_hour = get_field( 'price_per_hour_display', $post->ID ) ) {
      $currency = get_field( 'price_currency', $post->ID );
      $currency_i18n = $this->currency_i18n( $currency );
      $display_price .= sprintf( __( '%s&nbsp;%s/hod', 'shp-partneri' ), $price_per_hour, $currency_i18n );
      $display_price .= ' ';
      if ( get_field( 'vat_payer', $post->ID ) ) {
        $display_price .= __( 'bez DPH', 'shp-partneri' );
      } else {
        $display_price .= __( '(Neplátce DPH)', 'shp-partneri' );
      }
    } else if ( $price = get_field( 'price', $post->ID ) ) {
      $display_price .= $price;
    }
  
    return $display_price;
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

  function posts_in_term( $term, $post_status = [ 'publish', 'expired' ] ) {
    $query = new WP_Query( [
      'post_type' => 'request',
      'posts_per_page' => -1,
      'post_status' => $post_status,
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

  function keep_query_string( $link ) {
    if ( empty( $_SERVER['QUERY_STRING'] ) ) return $link;
    return $link . '?' . $_SERVER['QUERY_STRING'];
  }

  function average_rating( $post ) {
    $professional_post = new ProfessionalPost( $post->ID );
    return $professional_post->getAverageRating();
  }

  function verified_level( $post ) {
    $verified_level = get_field( 'verifiedLevel', $post->ID );
    switch($verified_level) {
      case 'zlatý':
        $verified_level = __( 'Zlatý partner', 'shp-partneri' );
        break;
      case 'stříbrný:':
        $verified_level = __( 'Stříbrný partner', 'shp-partneri' );
        break;
      default:
        $verified_level = __( 'Bronzový partner', 'shp-partneri' );
    }
    return $verified_level;
  }
}