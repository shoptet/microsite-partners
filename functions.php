<?php
require __DIR__ . '/vendor/autoload.php';
$includes = [
	'lib/cpt_posts.php',
	'lib/cpt_taxonomies.php',
	'lib/FacetedSearch.php',
	'lib/StarterSite.php',
	'lib/Post.php',
	'lib/PostService.php',
	'lib/ProfessionalAdmin.php',
	'lib/ProfessionalService.php',
	'lib/ProfessionalPost.php',
	'lib/ContactForm.php',
	'lib/AdminProfessionalList.php',
	'lib/TermSyncer.php',
	'lib/RequestPost.php',
	'lib/RequestArchive.php',
	'lib/RequestService.php',
	'lib/RequestNotifier.php',
	'lib/RequestForm.php',
	'lib/setup.php',
	'lib/helpers.php',
	'lib/acf_add_options_page.php',
	'lib/acf_settings.php',
	'lib/acf_comment_mailing.php',
	'lib/acf_request_mailing.php',
	'lib/acf_request_archive.php',
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