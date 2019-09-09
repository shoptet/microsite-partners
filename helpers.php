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
    // TODO: uncomment on production
    // 'date_query' => [
    //   'before' => date( 'Y-m-d', strtotime('-7 days') ),
    // ],
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
    $context['text'] = __( '
      Je to už nějaký čas, co jste napsal <strong>své hodnocení</strong>.
      Asi se ztratil váš potvrzovací email.
      Bez potvrzení to ale nepůjde.
      Tak to pojďme zkusit znovu, ať můžeme hodnocení zveřejnit.
    ', 'shp-partneri' );
    $context['image'] = [
      'main' => 'shoptetrix-warning-mail.png',
      'width' => 250,
    ];
    $context['cta'] = [
      'title' => 'Potvrdit hodnocení',
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
	$args = [];
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