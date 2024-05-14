<?php
/**
 * Template Name: Settings
 */

if (!is_user_logged_in()) {
  auth_redirect();
}

$tabs = [
  'mailing' => __( 'Mailing', 'shp-partneri' ),
  'change-password' => __( 'Změna hesla', 'shp-partneri' ),
];

$current_tab = isset($_GET['tab']) ? $_GET['tab'] : 'mailing';

$acf_form_args = [
  'id' => 'acf_settings_form',
  'post_id' => get_current_user_post()->ID,
  'fields' => [
    'field_5db082de1cb01', // mailing status
    'field_5db0834a1cb02', // unsubscribed categories
  ],
  'uploader' => 'basic',
  'kses' => false,
  'updated_message' => '<div class="alert alert-success">' . __('Nastavení uloženo!', 'shp-partneri') . '</div>',
  'html_submit_button'	=> '<div class="text-center pt-4"><button type="submit" class="btn btn-primary btn-lg">' . __( 'Uložit nastavení', 'shp-partneri' ) . '</button></div>',
];

$context = Timber::get_context();

$context['title'] = __( 'Nastavení', 'shp-partneri' );
$context['tabs'] = $tabs;
$context['current_tab'] = $current_tab;
$context['acf_form_args'] = $acf_form_args;

Timber::render( 'page-settings.twig', $context );