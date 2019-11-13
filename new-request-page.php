<?php
/**
* Template Name: New request page
*/

$context = Timber::get_context();

$post = new TimberPost();
$context['post'] = $post;
$template = 'new-request-page.twig';

$context['breadcrumbs'] = [];
$context['breadcrumbs'][ __( 'Poptávky', 'shp-partneri' ) ] = get_post_type_archive_link( 'request' );
$context['breadcrumbs'][ $post->title ] = $post->link;

$acf_form_args_base = [
  'id' => 'acf-form',
  'post_id' => 'new_post',
  'label_placement' => 'left',
  'instruction_placement' => 'field',
  'new_post' => [
    'post_type' => 'request',
    'post_status' => 'pending',
  ],
  'form' => false,
  'updated_message' => false,
];

$acf_form_args = [
  'top' => [
    'fields' => [
      'field_5d9f2ebf8e646', // name
      'field_5da06f824e26a', // email
      'field_5d9f2efc8e647', // url
      'field_5d9f2f4a8e648', // category
      '_post_title',
      '_post_content',
    ],
  ],
  'bottom' => [
    'fields' => [
      'field_5d9f2fbd8e64a', // is_shoptet
    ],
    'label_placement' => 'top',
    'instruction_placement' => 'label',
  ],
];
array_walk( $acf_form_args, function ( &$value ) use( &$acf_form_args_base ) {
  $value = array_merge( $acf_form_args_base, $value );
} );
$context['acf_form_args'] = $acf_form_args;

// Pass ACF updated query to context
if ( isset( $_GET[ 'updated' ] ) && $_GET[ 'updated' ] == 'true' ) {
  $context['submited'] = $acf_form_args;
}

Timber::render( $template, $context );
