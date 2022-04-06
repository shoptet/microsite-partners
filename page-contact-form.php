<?php
/**
 * Template Name: Contact Form
 */

$context = Timber::get_context();
$post = new TimberPost();
$context['post'] = $post;

Timber::render( 'page-contact-form.twig', $context );
