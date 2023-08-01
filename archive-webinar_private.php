<?php

if (!is_user_logged_in()) {
  auth_redirect();
}

Timber::render( 'archive-webinar_private.twig', $context );