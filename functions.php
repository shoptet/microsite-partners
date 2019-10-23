<?php
require __DIR__ . '/vendor/autoload.php';
$includes = [
	'lib/StarterSite.php',
	'lib/Post.php',
	'lib/ProfessionalPost.php',
	'lib/TermSyncer.php',
	'lib/RequestService.php',
	'lib/RequestPost.php',
	'lib/RequestNotifier.php',
	'lib/RequestForm.php',
	'lib/setup.php',
	'lib/helpers.php',
	'lib/cpt_posts.php',
	'lib/cpt_taxonomies.php',
	'lib/acf_settings.php',
	'lib/acf_add_options_page.php',
	'lib/taxonomy_slug_rewrite.php',
	'lib/remove_default_user_roles.php',
	'lib/custom_search.php',
];
foreach ($includes as $file) {
  if (!$filepath = locate_template($file)) {
    trigger_error(sprintf('Error locating %s for inclusion', $file));
  }
  require_once $filepath;
}
unset($file, $filepath);