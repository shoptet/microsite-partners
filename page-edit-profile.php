<?php
/**
 * Template Name: Edit profile
 */

if (!is_user_logged_in()) {
  auth_redirect();
}

$tabs = [
  'general' => __( 'Základní', 'shp-partneri' ),
  'contact' => __( 'Kontakt', 'shp-partneri' ),
  'about' => __( 'O mně', 'shp-partneri' ),
  'skills' => __( 'Co umím', 'shp-partneri' ),
];

$acf_form_fields = [
  'general' => [
    'field_5d10c3f29b87b', // photo
    'field_5d8217ec31ab1', // currency
    'field_5d3f026f0cef2', // price / hour
    'field_5d82194831ab2', // vat payer
    'field_5d3f02d50cef4', // price text
    'field_59ca39287537d', // cin
    'field_59edcc260922d', // tin
  ],
  'contact' => [
    'field_59ca38b07537a', // web
    'field_59ca38cc7537b', // phone
    'field_59ca4bf803c36', // facebook
    'field_59ca4c2503c38', // linkedin
    'field_69fa4c4237d38', // instagram
    'field_59ca4c1603c37', // twitter
  ],
  'about' => [
    'field_59ca425ee9a9e', // about me
  ],
  'skills' => [
    'field_59e8ac0d91d4b', // skills
  ],
];

if (isset($_GET['tab']) && isset($tabs[$_GET['tab']])) {
  $current_tab = $_GET['tab'];
} else {
  $current_tab = 'general';
}

if (isset($acf_form_fields[$current_tab])) {
  $fields = $acf_form_fields[$current_tab];
} else {
  $fields = $acf_form_fields['general'];
}

$acf_form_args = [
  'id' => 'acf_edit_profile_form',
  'post_id' => get_current_user_post()->ID,
  'post_title' => ($current_tab == 'general' ? true : false),
  'fields' => $fields,
  'uploader' => 'basic',
  'kses' => false,
  'updated_message' => '<div class="alert alert-success">' . __('Úspěšně odesláno! Vaše žádost o úpravu profilu byla odeslána. Čekejte prosím na potvrzení.', 'shp-partneri') . '</div>',
  'html_submit_button'	=> '<div class="text-center pt-4 onboarding-submit"><button type="submit" class="btn btn-primary btn-lg">' . __( 'Požádat o aktualizaci profilu', 'shp-partneri' ) . '</button></div>',
];

$context = Timber::get_context();

$context['title'] = __( 'Žádost o aktualizaci profilu', 'shp-partneri' );
$context['tabs'] = $tabs;
$context['current_tab'] = $current_tab;
$context['acf_form_args'] = $acf_form_args;

Timber::render( 'page-edit-profile.twig', $context );