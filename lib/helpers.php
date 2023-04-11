<?php

function get_similiar_posts($post, $number = 3) {
  $similarPosts = array();

  // Collect all post categories
  $allTerms = array_merge(
  	$post->terms('category_professionals'),
  	$post->terms('category_plugins'),
  	$post->terms('category_tools')
  );

  // Collect all post from categories
  foreach ($allTerms as $term) {
  	$similarPosts = array_merge($similarPosts, $term->posts());
  }

  // Exclude own post from similar posts
  $similarPosts = array_filter($similarPosts, function ($similarPost) use ($post) {
  	return ($similarPost->id !== $post->id);
  });

  $similarPosts = array_unique($similarPosts);
  shuffle($similarPosts);
  $similarPosts = array_slice($similarPosts, 0, $number);

  return $similarPosts;
}

function get_post_terms($post) {
  $terms = array();

  if ($post->post_type === 'profesionalove') {
  	$terms = $post->terms('category_professionals');
  } else if ($post->post_type === 'napojeni') {
    $terms = $post->terms('category_plugins');
  } else if ($post->post_type === 'nastroje') {
    $terms = $post->terms('category_tools');
  }

  return $terms;
}

function get_post_description_title($post) {
  $title = null;

  if ($post->post_type === 'profesionalove') {
  	$title = get_field('titleDescriptionProfessional', 'options');
  } else if ($post->post_type === 'napojeni') {
    $title = get_field('titleDescriptionPlugin', 'options');
  } else if ($post->post_type === 'nastroje') {
    $title = get_field('titleDescriptionTool', 'options');
  }

  return $title;
}

function get_post_benefit_title($post) {
  $title = null;

  if ($post->post_type === 'profesionalove') {
  	$title = get_field('titleBenefitProfessional', 'options');
  } else if ($post->post_type === 'napojeni') {
    $title = get_field('titleBenefitPlugin', 'options');
  } else if ($post->post_type === 'nastroje') {
    $title = get_field('titleBenefitTool', 'options');
  }

  return $title;
}

function remind_authentication () {
  $options = get_fields('options');
	$args = [
    'status' => 'hold',
    'meta_query' => [
      'relation' => 'AND',
      [
        'key' => 'authenticated',
        'value' => 0,
      ],
      [
        'key' => 'authentication_reminded',
        'compare' => 'NOT EXISTS',
      ],
    ],
    'date_query' => [
      'before' => date( 'Y-m-d', strtotime('-7 days') ),
    ],
  ];
	$comments_query = new WP_Comment_Query;
  $comments = $comments_query->query( $args );
  
  foreach ( $comments as $comment ) {
    // Compile and send e-mail
    $context = Timber::get_context();
    $auth_token = get_comment_meta( $comment->comment_ID, 'auth_token', true );
    if ( ! $auth_token ) {
      $auth_token_hash = get_comment_meta( $comment->comment_ID, 'auth_token_hash', true );
      $auth_token = only_alphanumeric( $auth_token_hash );
    }
    $auth_url = get_site_url( null, '?auth_token=' . $auth_token );
    $context['title'] = __( 'Pozor!', 'shp-partneri' );
    $context['subtitle'] = __( 'Ještě jste nepotvrdil hodnocení<br>:-(', 'shp-partneri' );
    $context['text'] = __( 'Je to už nějaký čas, co jste napsal <strong>své hodnocení</strong>. Asi se ztratil váš potvrzovací email. Bez potvrzení to ale nepůjde. Tak to pojďme zkusit znovu, ať můžeme hodnocení zveřejnit.', 'shp-partneri' );
    $context['image'] = [
      'main' => 'shoptetrix-warning-mail.png',
      'width' => 250,
    ];
    $context['cta'] = [
      'title' => __( 'Potvrdit hodnocení', 'shp-partneri' ),
      'link' => $auth_url,
    ];
    $email_html_body = Timber::compile( 'templates/mailing/shoptetrix-inline.twig', $context );
    $email_subject = __( 'Připomenutí schválení vašeho hodnocení na partneri.shoptet.cz', 'shp-partneri' );
    wp_mail(
      $comment->comment_author_email,
      $email_subject,
      $email_html_body,
      [
        'From: ' . $options['email_from'],
        'Content-Type: text/html; charset=UTF-8',
      ]
    );
		update_comment_meta( $comment->comment_ID, 'authentication_reminded', time() );    
  }
}

function only_alphanumeric( $text ) {
  return preg_replace( "/[^a-zA-Z0-9]+/", "", $text );
}

function get_comment_by_auth_token( $auth_token ) {
  // Get all comments
	$args = [
    'status' => 'any',
  ];
	$comments_query = new WP_Comment_Query;
  $comments = $comments_query->query( $args );
  
  foreach ( $comments as $comment ) {
		$auth_token_hash = get_comment_meta( $comment->comment_ID, 'auth_token_hash', true );
    $auth_token_comment = get_comment_meta( $comment->comment_ID, 'auth_token', true );
    if (
			$auth_token === $auth_token_comment ||
			$auth_token === only_alphanumeric( $auth_token_hash ) ||
			password_verify( $auth_token , $auth_token_hash )
		) return $comment;
  }

  return null;
}

function get_post_by_onboarding_token( $onboarding_token ) {
	$query = new WP_Query( [
    'post_type' => 'profesionalove',
    'posts_per_page' => 1,
    'post_status' => 'any',
    'meta_query' => [
      [
        'key' => 'onboarding_token',
        'value' => $onboarding_token,
      ],
    ],
  ] );

  if ( empty( $query->posts ) ) return null;

  return $query->posts[0];
}

function render_onboarding_form_error_message() {
  $context = Timber::get_context();
  $context['wp_title'] = __( 'Odeslání se nezdařilo', 'shp-partneri' );
  $context['message_type'] = 'error';
  $context['title'] = __( 'Ouha!', 'shp-partneri' );
  $context['subtitle'] = __( 'Odeslání formuláře se&nbsp;nezdařilo :(', 'shp-partneri' );
  $context['text'] = '
    <p>
      ' . __( 'Možná už jste ho v minulosti odeslali, nebo je na vaši e-mailovou adresu už jedna registrace na webu Partneři Shoptet hotová.', 'shp-partneri' ) . '
    </p>
    <p class="mb-0">
      ' . sprintf ( __( 'Ozvěte se nám na <a href="mailto:%s" target="_blank">%s</a>, koukneme na to.', 'shp-partneri' ), __( 'partneri@shoptet.cz', 'shp-partneri' ), __( 'partneri@shoptet.cz', 'shp-partneri' ) ) . '
    </p>
  ';
  $context['footer_image'] = 'envelope-x';
  Timber::render( 'templates/message/message.twig', $context );
  die();
}

function expired_professionals_check () {

  // Get all professionals to expire
  $query = new WP_Query( [
    'post_type' => 'profesionalove',
    'posts_per_page' => -1,
    'post_status' => 'onboarding',
    'date_query' => [
      'before' => '-30 days',
    ],
  ] );

  // Set status to expired
  foreach ( $query->posts as $post ) {
    wp_update_post([
			'ID' => $post->ID,
			'post_status' => 'expired',
    ]);
		update_post_meta( $post->ID, 'expired', time() );    
  }
}

function remind_onboarding () {

  // Get all professionals to remind
  $query = new WP_Query( [
    'post_type' => 'profesionalove',
    'posts_per_page' => -1,
    'post_status' => 'onboarding',
    'date_query' => [
      'before' => '-7 days',
    ],
    'meta_query' => [
      [
        'key' => 'onboarding_reminded',
        'compare' => 'NOT EXISTS',
      ],
    ],
  ] );

  // Set reminding e-mail
  foreach ( $query->posts as $post ) {
    
    $options = get_fields('options');
    $onboarding_token = get_post_meta( $post->ID, 'onboarding_token', true );
    $email = get_post_meta( $post->ID, 'emailAddress', true );
    $onboarding_url = get_site_url( null, '?onboarding_token=' . $onboarding_token );
    
    $replace_pairs = [
      '%form_url%' => $onboarding_url,
    ];
    
    $text = $options['onboarding']['remind_mail_text'];
    $subject = $options['onboarding']['remind_mail_subject'];
    $email_html_body = strtr( $text, $replace_pairs );
    $email_subject = strtr( $subject, $replace_pairs );

    wp_mail(
      $email,
      $email_subject,
      $email_html_body,
      [
        'From: ' . $options['email_from'],
        'Content-Type: text/html; charset=UTF-8',
      ]
    );

    update_post_meta( $post->ID, 'onboarding_reminded', time() );    
  }
}

function verify_recaptcha () {
  if ( ! $_POST[ 'g-recaptcha-response' ] ) {
    return false;
  }
  $recaptcha_response = sanitize_text_field( $_POST[ 'g-recaptcha-response' ] );
  $recaptcha = new \ReCaptcha\ReCaptcha( G_RECAPTCHA_SECRET_KEY );
  $resp = $recaptcha->verify( $recaptcha_response, $_SERVER['REMOTE_ADDR'] );
  return $resp->isSuccess();
}

function is_blacklisted ( $name, $email, $message ) {
  $user_url = '';
  $user_ip = $_SERVER['REMOTE_ADDR'];
  $user_ip = preg_replace( '/[^0-9a-fA-F:., ]/', '', $user_ip );
  $user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : '';
  $user_agent = substr( $user_agent, 0, 254 );
  return wp_check_comment_disallowed_list( $name, $email, $user_url, $message, $user_ip, $user_agent );
}

/**
 * Get post count by meta key and value
 */
function get_post_count( $meta_key, $meta_value, $post_type, $term ): int
{
  $query = new WP_Query( [
    'post_type' => $post_type,
    'posts_per_page' => -1,
    'post_status' => 'publish',
    'fields' => 'ids',
    'no_found_rows' => true,
    'update_post_meta_cache' => false,
    'update_post_term_cache' => false,
    'meta_query' => [
      [
        'key' => $meta_key,
        'value' => $meta_value,
      ],
    ],
    'tax_query' => [
      [
        'taxonomy' => $term->taxonomy,
        'terms' => $term->term_id,
      ],
    ],
  ] );
  return count( $query->posts );
}

/**
 * Get regions by country
 */
function get_regions_by_country( $post_type, $term ): array
{
  $countries = [
    'cz' => [
      'name' => __( 'Česko', 'shp-partneri' ),
      'field' => 'field_5d3ebb18c804a',
    ],
    'sk' => [
      'name' => __( 'Slovensko', 'shp-partneri' ),
      'field' => 'field_5d3ebb6bc804b',
    ],
    'hu' => [
      'name' => __( 'Maďarsko', 'shp-partneri' ),
      'field' => 'field_609d261ccf063',
    ],
  ];
  $regions_by_country = [];
  foreach ( $countries as $country_code => $country ) {
    $regions_in_country = get_field_object( $country[ 'field' ] )[ 'choices' ];
    $regions = [];
    foreach ( $regions_in_country as $region_id => $region_name ) {
      $posts_in_region = get_post_count( 'region', $region_id, ProfessionalPost::POST_TYPE, $term );
      if ( $posts_in_region > 0 ) {
        $regions[] = [
          'id' => $region_id,
          'name' => $region_name,
          'count' => $posts_in_region,
        ];
      }
    }
    if ( empty( $regions ) ) continue;
    
    $regions_by_country[ $country_code ] = [
      'name' => $country[ 'name' ],
      'regions' => $regions,
    ];
  }
  return $regions_by_country;
}

function get_region_name_by_id ($region_id): string
{
  $country_fields = [
    'field_5d3ebb18c804a',
    'field_5d3ebb6bc804b',
    'field_609d261ccf063',
  ];
  $regions = [];
  foreach ( $country_fields as $field ) {
    $regions_in_country = get_field_object( $field )[ 'choices' ];
    $regions = array_merge($regions, $regions_in_country);
  }
  $region_name = '';
  foreach ( $regions as $id => $name ) {
    if ($id == $region_id) {
      $region_name = $name;
      break;
    }
  }
  return $region_name;
}

function get_custom_logo_url (): string
{
  if ( ! has_custom_logo() ) return '';
  $custom_logo_id = get_theme_mod( 'custom_logo' );
  $logo_url = wp_get_attachment_image_src( $custom_logo_id , 'full' );
  return $logo_url[0];
}

function is_current_user_admin() {
  $user = wp_get_current_user();
  $admin_roles = [ 'administrator', 'shoptet_administrator' ];

  if ( ! $user || ! $user->roles ) {
    return false;
  }

  return array_intersect( $admin_roles, $user->roles );
}

function stop_the_insanity () {
	global $wpdb, $wp_object_cache;

	$wpdb->queries = [];

	if ( is_object( $wp_object_cache ) ) {
		$wp_object_cache->group_ops      = [];
		$wp_object_cache->stats          = [];
		$wp_object_cache->memcache_debug = [];
		$wp_object_cache->cache          = [];

		if ( method_exists( $wp_object_cache, '__remoteset' ) ) {
			$wp_object_cache->__remoteset();
		}
	}
}

function get_currencies (): array
{
  $locale = get_locale(); // cs_CZ, sk_SK, hu_HU
  $currencies = [
    'EUR' => '€',
  ];
  switch( $locale ) {
    case 'cs_CZ':
      $currencies = [
        'CZK' => 'Kč',
        'EUR' => '€',
      ];
      break;
    case 'hu_HU':
      $currencies = [
        'HUF' => 'Ft',
        'EUR' => '€',
      ];
      break;
  }
  return $currencies;
}

function get_country_code (): string
{
  $locale = explode('_', get_locale());
  $country_code = ucfirst(strtolower(array_pop($locale)));
  return $country_code;
}

function truncate( $string, $limit, $separator = '...' ) {
  if (strlen($string) > $limit) {
    $newlimit = $limit - strlen($separator);
    $s = substr($string, 0, $newlimit + 1);
    return substr($s, 0, strrpos($s, ' ')) . $separator;
  }
  return $string;
}

function urls_to_links($text) {
  return preg_replace('/https?:\/\/[\w\-\.!~#?&=+\*\'"(),\/]+/', '<a href="$0" target="_blank" rel="nofollow">$0</a>', $text);
}

function get_youtube_thumbnail_url($video_id, $quality = 'mqdefault') {
  return "https://img.youtube.com/vi/$video_id/$quality.jpg";
}

function get_time_interval($duration) {
  $duration = intval($duration);
  $hour_in_sec = 1 * 60 * 60;
  $format = 'i:s';
  if ($duration >= $hour_in_sec) {
    $format = 'H:i:s';
  }
  $time = date($format, $duration);
  // Remove leading O
  if (substr($time, 0, 1) == '0') {
    $time = substr($time, 1);
  }
  return $time;
}