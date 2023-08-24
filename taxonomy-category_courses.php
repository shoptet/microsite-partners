<?php

if (!is_user_logged_in()) {
  auth_redirect();
}

require_once( 'archive-course.php' );