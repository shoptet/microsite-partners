<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * To generate specific templates for your pages you can use:
 * /mytheme/views/page-mypage.twig
 * (which will still route through this PHP file)
 * OR
 * /mytheme/page-mypage.php
 * (in which case you'll want to duplicate this file and save to the above path)
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since    Timber 0.1
 */

 $context = Timber::get_context();

 /* start - newest proffesionals for home.twig */
 $args = array(
    'posts_per_page' => 3,
    'order' => 'DESC',
	'post_type' => 'profesionalove',
 );
 $newestPosts = Timber::get_posts($args);
 $context['newest_posts'] = $newestPosts;
 /* newest proffesionals - end */

 $post = new TimberPost();
 $context['post'] = $post;
 $templates =  array( 'page-' . $post->post_name . '.twig', 'page.twig' );

 if (is_front_page()){

   $context['category_professionals'] = Timber::get_terms('category_professionals');
   $context['category_plugins'] = Timber::get_terms('category_plugins');
   $context['category_tools'] = Timber::get_terms('category_tools');

   // Count published post by post type
   $count_category_professionals = wp_count_posts('profesionalove')->publish;
   $count_category_plugins = wp_count_posts('napojeni')->publish;
   $count_category_tools = wp_count_posts('nastroje')->publish;

   $context['show_category_professionals'] = (bool) $count_category_professionals;
   $context['show_category_plugins'] = (bool) $count_category_plugins;
   $context['show_category_tools'] = (bool) $count_category_tools;

   $count_profiles = $count_category_professionals + $count_category_plugins + $count_category_tools;

   $options = get_fields('options');

   $hero_text = str_replace('%count%', $count_profiles, $options['themeHomepageHeroText']);
   $context['hero_text'] = $hero_text;

   $context['meta_description'] = strip_tags($hero_text);

   $context['show_breadcrumb'] = false;

   $context['recent_request_posts'] = Timber::get_posts( [
     'post_type' => 'request',
     'posts_per_page' => 3,
   ] );
    
 	 array_unshift( $templates, 'home.twig' );
 }

 Timber::render( $templates, $context );
