<?php
/**
 * Template Name: Edit profile
 */

if (!is_user_logged_in()) {
  auth_redirect();
}

$tabs = [
  'general' => __( 'Základní', 'shp-partneri' ),
  'about' => __( 'O mně', 'shp-partneri' ),
  'skills' => __( 'Co umím', 'shp-partneri' ),
  'references' => __( 'Reference', 'shp-partneri' ),
  'recommendations' => __( 'Doporučení', 'shp-partneri' ),
];

$current_tab = isset($_GET['tab']) ? $_GET['tab'] : 'general';

$acf_form_args = [
  'id' => 'acf_edit_profile_form',
  'post_id' => get_current_user_post()->ID,
  'post_title' => true,
  'fields' => [ 'field_5d10c3f29b87b', 'field_59ca39287537d', 'field_59edcc260922d', 'field_59ca425ee9a9e', 'field_59ca4ec3995cd' ],
  'uploader' => 'basic',
  'html_submit_button'	=> '<div class="text-center pt-4 onboarding-submit"><button type="submit" class="btn btn-primary btn-lg">' . __( 'Požádat o aktualizaci profilu', 'shp-partneri' ) . '</button></div>',
];

$context = Timber::get_context();

$context['title'] = __( 'Žádost o aktualizaci profilu', 'shp-partneri' );
$context['tabs'] = $tabs;
$context['current_tab'] = $current_tab;
$context['acf_form_args'] = $acf_form_args;

Timber::render( 'page-edit-profile.twig', $context );