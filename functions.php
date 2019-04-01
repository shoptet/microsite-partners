<?php

ini_set('display_errors', 1);

if ( ! class_exists( 'Timber' ) ) {
	add_action( 'admin_notices', function() {
		echo '<div class="error"><p>Timber not activated. Make sure you activate the plugin in <a href="' . esc_url( admin_url( 'plugins.php#timber' ) ) . '">' . esc_url( admin_url( 'plugins.php') ) . '</a></p></div>';
	});

	return;
}

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if (! is_plugin_active("custom-post-type-ui/custom-post-type-ui.php")) {
	include_once 'cpt_posts.php';
	include_once 'cpt_taxonomies.php';
}

include_once 'acf_settings.php';
include_once 'acf_add_options_page.php';

include_once 'taxonomy_slug_rewrite.php';

include_once 'remove_default_user_roles.php';

include_once 'custom_search.php';

add_filter('robots_txt', 'add_to_robots_txt');
function add_to_robots_txt($robot_text) {
	// via https://moz.com/community/q/default-robots-txt-in-wordpress-should-i-change-it#reply_329849
  return $robot_text . "
Disallow: *?p=*
Disallow: /wp-includes/
Disallow: /wp-login.php
Disallow: /wp-register.php
Disallow: *?s=*
";
}

Timber::$dirname = array('templates', 'views');

class StarterSite extends TimberSite {

	function __construct() {
		add_theme_support( 'post-formats' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'menus' );
		add_theme_support( 'html5', array( 'comment-list', 'comment-form', 'search-form', 'gallery', 'caption' ) );
		add_filter( 'timber_context', array( $this, 'add_to_context' ) );
		add_filter( 'get_twig', array( $this, 'add_to_twig' ) );
		add_action( 'init', array( $this, 'register_post_types' ) );
		add_action( 'init', array( $this, 'register_taxonomies' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'load_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ) );

		$this->clean_header();
		parent::__construct();
	}

	function register_post_types() {
		//this is where you can register custom post types
	}

	function register_taxonomies() {
		//this is where you can register custom taxonomies
	}

	function add_to_context( $context ) {
		$context['header_menu'] = new Timber\Menu( 'header-menu' );
		$context['archive_link'] =  get_post_type_archive_link( 'profesionalove' );
		$context['all_categories'] = Timber::get_terms('category_professionals', 'hide_empty=true');
		$context['site'] = $this;
		$context['show_breadcrumb'] = true;
		$context['options'] = get_fields('options');
		return $context;
	}

	function add_to_twig( $twig ) {
		/* this is where you can add your own functions to twig */
		$twig->addExtension( new Twig_Extension_StringLoader() );
		$twig->addFilter('static_assets', new Twig_SimpleFilter('static_assets', array($this, 'static_assets')));
		$twig->addFilter('truncate', new Twig_SimpleFilter('truncate', array($this, 'truncate')));
		$twig->addFilter('display_url', new Twig_SimpleFilter('display_url', array($this, 'display_url')));
		return $twig;
	}

	function clean_header() {
		/* Clean WordPress header (for cleaning emoji and oEmbed is used a plugin) */
		remove_action( 'wp_head', 'rsd_link' );
		remove_action( 'wp_head', 'wlwmanifest_link' );
		remove_action( 'wp_head', 'wp_shortlink_wp_head' );
		remove_action( 'wp_head', 'wp_generator' );
	}

	function load_styles() {
		/* Load styles and add a cache-breaking URL parameter */

		$fileName = '/assets/main.css';
		$fileUrl = get_template_directory_uri() . $fileName;
		$filePath = get_template_directory() . $fileName;
		wp_enqueue_style( 'main', $fileUrl, array(), filemtime($filePath), 'all' );

		$fileName = '/assets/shoptet.css';
		$fileUrl = get_template_directory_uri() . $fileName;
		$filePath = get_template_directory() . $fileName;
		wp_enqueue_style( 'shoptet', $fileUrl, array(), filemtime($filePath), 'all' );
	}

	function load_scripts() {
		/* Load scripts and add a cache-breaking URL parameter */

		$fileName = '/assets/vendor.js';
		$fileUrl = get_template_directory_uri() . $fileName;
		$filePath = get_template_directory() . $fileName;
		wp_enqueue_script( 'vendor', $fileUrl, array(), filemtime($filePath), true );

		$fileName = '/assets/main.js';
		$fileUrl = get_template_directory_uri() . $fileName;
		$filePath = get_template_directory() . $fileName;
		wp_enqueue_script( 'main', $fileUrl, array('vendor'), filemtime($filePath), true );

        $fileName = '/assets/jquery-3.3.1.min.js';
		$fileUrl = get_template_directory_uri() . $fileName;
		$filePath = get_template_directory() . $fileName;
		wp_enqueue_script( 'jquery', $fileUrl, array('vendor'), filemtime($filePath), true );

        $fileName = '/assets/navigation.js';
		$fileUrl = get_template_directory_uri() . $fileName;
		$filePath = get_template_directory() . $fileName;
		wp_enqueue_script( 'navigation', $fileUrl, array('vendor'), filemtime($filePath), true );

	}

	function static_assets( $filePath ) {
	  return $this->theme->link . '/assets/' . $filePath;
	}

	function truncate( $string, $limit, $separator = '...' ) {
	  if (strlen($string) > $limit) {
      $newlimit = $limit - strlen($separator);
      $s = substr($string, 0, $newlimit + 1);
      return substr($s, 0, strrpos($s, ' ')) . $separator;
	  }
	  return $string;
	}

	function display_url( $url ) {
		// Romove protocol
	  if (substr( $url, 0, 7 ) === 'http://') {
			$url = substr( $url, 7 );
	  } else if (substr( $url, 0, 8 ) === 'https://') {
			$url = substr( $url, 8 );
		} else if (substr( $url, 0, 2 ) === '//') {
		 	$url = substr( $url, 2 );
	 	}
		// Remove www subdomain
		if (substr( $url, 0, 4 ) === 'www.') {
			$url = substr( $url, 4 );
		}
		// Remove last slash
		if (substr( $url, -1 ) === '/') {
			$url = substr( $url, 0, -1 );
		}

	  return $url;
	}
}

new StarterSite();


function wp_getStats() {
   $cacheFile = 'wp-content/uploads/counters.cached';
   $modifyLimit = 3600; // hour in seconds


   if (!file_exists($cacheFile) || (time() - filemtime($cacheFile) > $modifyLimit)) {
       $tmp = @file_get_contents('https://www.shoptet.cz/projectAction/ShoptetStatisticCounts/Index');
       if ($tmp !== FALSE) {
           file_put_contents(
               $cacheFile,
               $tmp
           );
       }
   }

   $content = file_get_contents($cacheFile);
   if ($content !== FALSE) {
       return (array) json_decode($content);
   }

   return array(
       'projectsCount' => 8060,
       'transactionsCount' => 81476,
       'sales' => 32020068
   );
}

function wp_showProjectsCount() {
    $projectStats = wp_getStats();
    if(!empty($projectStats) && !empty($projectStats['projectsCount'])) {
        return $projectStats['projectsCount'];
    } else {
        return '13000';
    }
}

add_shortcode('projectCount', 'wp_showProjectsCount');

function get_shoptet_footer() {
    // params
    $id = 'partnerishoptetcz';
    $temp = 'wp-content/themes/shoptet/tmp/shoptet-footer.html';

    $url = 'https://www.shoptet.cz/action/ShoptetFooter/render/';
    $cache = 24 * 60 * 60;
    $probability = 50;
    $ignoreTemp = isset($_GET['force_footer']);

    // code
    $footer = '';
    if (!$ignoreTemp && is_readable($temp)) {
        $footer = file_get_contents($temp);
        $regenerate = rand(1, $probability) === $probability;
        if (!$regenerate) {
            return $footer;
        }
        $mtine = filemtime($temp);
        if ($mtine + $cache > time()) {
            return $footer;
        }
    }

    $address = $url . '?id=' . urlencode($id);
    $new = file_get_contents($address);
    if ($new !== FALSE) {
        $newTemp = $temp . '.new';
        $length = strlen($new);
        $result = file_put_contents($newTemp, $new);
        if ($result === $length) {
            rename($newTemp, $temp);
        }
        $footer = $new;
    }

    return $footer;
}

add_filter('acf/format_value/type=text', 'do_shortcode');

function wpcf7_dynamic_recipient_filter($recipient, $args=array()) {
    if (isset($args['partner-email-address'])) {
        $recipient = $args['partner-email-address'];
    } else {
        $recipient = 'hanzlikova@shoptet.cz';
    }
    return $recipient;
}
add_filter('wpcf7-dynamic-recipient-filter', 'wpcf7_dynamic_recipient_filter', 10, 2);

add_filter( 'wpcf7_load_js', '__return_false' );