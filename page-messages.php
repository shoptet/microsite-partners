<?php
/**
 * Template Name: Messages
 */

if (!is_user_logged_in()) {
  auth_redirect();
}

$context = Timber::get_context();
$post = get_current_user_post();
$responses = array_reverse($post->get_field('responses'));

$context['title'] = __( 'Přijaté zprávy', 'shp-partneri' );
$context['responses'] = $responses;

Timber::render( 'page-messages.twig', $context );