<?php
namespace Helpers;


function get_similiar_posts($post, $number = 3) {
  $similarPosts = array();

  // Collect all post categories
  $allTerms = array_merge(
  	$post->terms('category_professionals'),
  	$post->terms('category_plugins'),
  	$post->terms('category_tools')
  );

  // Collect all post from categories
  foreach ($allTerms as $term) {
  	$similarPosts = array_merge($similarPosts, $term->posts());
  }

  // Exclude own post from similar posts
  $similarPosts = array_filter($similarPosts, function ($similarPost) use ($post) {
  	return ($similarPost->id !== $post->id);
  });

  $similarPosts = array_unique($similarPosts);
  shuffle($similarPosts);
  $similarPosts = array_slice($similarPosts, 0, $number);

  return $similarPosts;
}

function get_post_terms($post) {
  $terms = array();

  if ($post->post_type === 'profesionalove') {
  	$terms = $post->terms('category_professionals');
  } else if ($post->post_type === 'napojeni') {
    $terms = $post->terms('category_plugins');
  } else if ($post->post_type === 'nastroje') {
    $terms = $post->terms('category_tools');
  }

  return $terms;
}

function get_post_description_title($post) {
  $title = null;

  if ($post->post_type === 'profesionalove') {
  	$title = get_field('titleDescriptionProfessional', 'options');
  } else if ($post->post_type === 'napojeni') {
    $title = get_field('titleDescriptionPlugin', 'options');
  } else if ($post->post_type === 'nastroje') {
    $title = get_field('titleDescriptionTool', 'options');
  }

  return $title;
}

function get_post_benefit_title($post) {
  $title = null;

  if ($post->post_type === 'profesionalove') {
  	$title = get_field('titleBenefitProfessional', 'options');
  } else if ($post->post_type === 'napojeni') {
    $title = get_field('titleBenefitPlugin', 'options');
  } else if ($post->post_type === 'nastroje') {
    $title = get_field('titleBenefitTool', 'options');
  }

  return $title;
}
