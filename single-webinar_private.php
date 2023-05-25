<?php

if (!is_user_logged_in()) {
  auth_redirect();
} else {
  wp_redirect(admin_url());
  exit;
}