<?php

// Disable DOMDocument warnings while e-mail sending. Warnings are caused by lh-multipart-email plugin.
libxml_use_internal_errors(true);

add_filter( 'wp_mail', function ( $args ) {
	if ( WP_DEBUG ) {
		$args['to'] = 'jk.oolar@gmail.com';
	}
	return $args;
} );

ini_set('display_errors', 1);

/**
 * Load translations
 */
add_action( 'after_setup_theme', function () {
	load_theme_textdomain( 'shp-partneri', get_template_directory() . '/languages' );
	add_theme_support( 'custom-logo' );
} );

/**
 * Set domain for ACF
 */
add_filter('acf/settings/l10n_textdomain', function () {
	return 'shp-partneri';
} );

if ( ! class_exists( 'Timber' ) ) {
	add_action( 'admin_notices', function() {
		echo '<div class="error"><p>Timber not activated. Make sure you activate the plugin in <a href="' . esc_url( admin_url( 'plugins.php#timber' ) ) . '">' . esc_url( admin_url( 'plugins.php') ) . '</a></p></div>';
	});

	return;
}

/**
 * Do not show professional category and image field in wp admin
 */
add_filter( 'acf/load_field/key=field_5d10a24f0b5e7', 'hide_field_in_admin' );
add_filter( 'acf/load_field/key=field_5d10c3f29b87b', 'hide_field_in_admin' );
function hide_field_in_admin( $field ) {
	global $post, $pagenow;
  if (
		( 'post.php' === $pagenow || 'post-new.php' === $pagenow ) &&
		isset($post->post_type) &&
		ProfessionalPost::POST_TYPE === $post->post_type
	) {
		$field = false;
	}
	return $field;
}

/**
 * Show only basic text fields in frontend onboarding form
 */
add_filter( 'acf/load_field/key=field_59ca425ee9a9e', 'basic_text_field_for_non_admin' ); // ACF field name: description
add_filter( 'acf/load_field/key=field_59e8ac0d91d4b', 'basic_text_field_for_non_admin' ); // ACF field name: benefit
function basic_text_field_for_non_admin ( $field ) {
	if( ! is_admin() ) {
		$field['tabs'] = 'visual';
		$field['toolbar'] = 'basic';
		$field['media_upload'] = 0;
	}
	return $field;
}

/**
 * Disable required recommandations in WP admin
 */
add_filter( 'acf/load_field/key=field_59cab1b28ed7f', 'disable_min_recommandations_for_admin' );
add_filter( 'acf/load_field/key=field_59cab1b28ed7f', 'disable_required_recommandations_for_admin' );
add_filter( 'acf/load_field/key=field_59cab2018ed82', 'disable_required_recommandations_for_admin' );
add_filter( 'acf/load_field/key=field_59cab1d88ed80', 'disable_required_recommandations_for_admin' );
function disable_required_recommandations_for_admin( $field ) {
	if( is_user_logged_in() ) {
		$field['required'] = 0;
	}
	return $field;
}
function disable_min_recommandations_for_admin( $field ) {
	if( is_user_logged_in() ) {
		$field['min'] = 0;
	}
	return $field;
}

/**
 * Add cron schedule interval options
 */
add_filter( 'cron_schedules', function ( $schedules ) {
	$schedules['one_minute'] = [
		'interval' => 60,
		'display' => __( 'Každou 1 minutu', 'shp-partneri' ),
  ];
	return $schedules;
} );

add_filter('robots_txt', 'add_to_robots_txt');
function add_to_robots_txt($robot_text) {
	// via https://moz.com/community/q/default-robots-txt-in-wordpress-should-i-change-it#reply_329849
  return $robot_text . "
Disallow: *?p=*
Disallow: /wp-includes/
Disallow: /wp-login.php
Disallow: /wp-register.php
Disallow: *?auth_token=*
Disallow: *?onboarding_token=*
Disallow: *?s=*
";
}

/**
 * Add new post status for professionals
 */
add_action( 'init', function() {
  register_post_status( 'onboarding', [
    'label' => __( 'Čeká na vyplnění formuláře', 'shp-partneri' ),
    'public' => false,
		'show_in_admin_all_list' => true,
		'show_in_admin_status_list' => true,
		'post_type' => [ 'profesionalove' ],
		'label_count' => _n_noop(
			'Čeká na vyplnění formuláře <span class="count">(%s)</span>',
			'Čeká na vyplnění formuláře <span class="count">(%s)</span>',
			'shp-partneri'
		),
	] );
	register_post_status( 'expired', [
    'label' => __( 'Expirováno', 'shp-partneri' ),
    'public' => false,
		'show_in_admin_all_list' => false,
		'show_in_admin_status_list' => true,
		'post_type' => [ 'profesionalove' ],
		'label_count' => _n_noop(
			'Expirováno <span class="count">(%s)</span>',
			'Expirováno <span class="count">(%s)</span>',
			'shp-partneri'
		),
  ] );
} );

/**
 * Add post states to admin list
 */
add_filter( 'display_post_states', function ( $states, $post ) {
  switch ( $post->post_status ) {
    case 'onboarding':
    $states[] = __( 'Čeká na vyplnění formuláře', 'shp-partneri' );
		break;
		case 'expired':
    $states[] = __( 'Expirováno', 'shp-partneri' );
    break;
  }
  return $states;
}, 10, 2 );

// Make the review rating required.
add_filter( 'preprocess_comment', function ( $commentdata ) {
	if ( is_admin() ||  ( isset( $_POST['comment_parent'] ) && 0 !== intval( $_POST['comment_parent'] ) ) ) {
		return $commentdata;
	}
	if ( ! isset( $_POST['rating'] ) || 0 === intval( $_POST['rating'] ) ) {
		wp_die( __( 'Chyba: Nepřidali jste hodnocení. Běžte prosím zpět a přidejte hodnocení.', 'shp-partneri' ) );
	}
	return $commentdata;
} );

// Store the review rating submitted by the user
add_action( 'comment_post', function ( $comment_id ) {
	if ( ! isset( $_POST['rating'] ) || '' === $_POST['rating'] ) return;
	$rating = intval( $_POST['rating'] );
	add_comment_meta( $comment_id, 'rating', $rating );
} );

// Send auth e-mail
add_action( 'comment_post', function ( $comment_id ) {
	$comment = new Timber\Comment($comment_id);
	$post = new Timber\Post( $comment->comment_post_ID );
	$options = get_fields('options');

	// Generate auth url
	$auth_token = bin2hex( openssl_random_pseudo_bytes(32) );
	// Legacy
	// $auth_token_hash = password_hash( $auth_token, PASSWORD_BCRYPT );
	// add_comment_meta( $comment_id, 'auth_token_hash', $auth_token_hash );
	add_comment_meta( $comment_id, 'auth_token', $auth_token );
	add_comment_meta( $comment_id, 'authenticated', 0 );
	$auth_url = get_site_url( null, '?auth_token=' . $auth_token );

	// Compile and send e-mail
	$context = Timber::get_context();
	$context['title'] = __( 'Děkujeme', 'shp-partneri' );
	$context['subtitle'] = __( 'Za vaše hodnocení', 'shp-partneri' );
	$context['text'] = sprintf( __( 'Teď už jen klikněte <strong style="%s"><a href="%s" target="_blank" style="%s">ZDE</a></strong> pro potvrzení a&nbsp;zveřejnění vašeho hodnocení.<br><br>Přejeme krásný den,<br>tým Shoptet', 'shp-partneri' ), 'font-weight: 600;', $auth_url, 'color:#21AFE5;text-decoration:underline;' );
	$context['image'] = [
		'main' => 'shoptetrix-action-1.png',
		'complementary' => 'shoptetrix-action-2.png',
		'width' => 250,
	];
	$email_html_body = Timber::compile( 'templates/mailing/shoptetrix-inline.twig', $context );
	$email_subject = sprintf ( __( 'Schválení vašeho hodnocení na partneri.shoptet.cz k Partnerovi %s', 'shp-partneri' ), $post->post_title );
	wp_mail(
		$comment->comment_author_email,
		$email_subject,
		$email_html_body,
		[
			'From: ' . $options['email_from'],
			'Content-Type: text/html; charset=UTF-8',
		]
	);

	// Render message
	$context = Timber::get_context();
	$context['wp_title'] = __( 'Hodnocení odesláno', 'shp-partneri' );
	$context['message_type'] = 'success';
	$context['title'] = __( 'Super!', 'shp-partneri' );
	$context['subtitle'] = __( 'Hodnocení odesláno', 'shp-partneri' );
	$context['text'] = '<p>' . __( 'A teď už zbývá jen kliknout na potvrzující odkaz v <strong>e-mailu, který jsme vám právě poslali</strong>. Je to jen pro ověření, že opravdu existujete :-)', 'shp-partneri' ) . '</p>';
	$context['footer_image'] = 'envelope';
	Timber::render( 'templates/message/message.twig', $context );
	
	die();
} );

// Check for auth tokean and authenticate
add_action( 'init' , function () {
	if ( ! isset( $_GET['auth_token'] ) || '' === $_GET['auth_token'] ) return;
	$auth_token = $_GET['auth_token'];

	$comment = get_comment_by_auth_token( $auth_token );

	if ( ! $comment ) {
		wp_die(
			__( 'Vypadá to, že jste zadali neplatný odkaz na ověření komentáře. Zkuste to prosím znovu.', 'shp-partneri' ),
			__( 'Neplatný odkaz', 'shp-partneri' ),
			[
				'response' => 200,
				'link_text' => __( 'Přejít na partneri.shoptet.cz', 'shp-partneri' ),
				'link_url' => get_site_url(),
			]
		);
		return;
	}

	$authenticated = get_comment_meta( $comment->comment_ID, 'authenticated', true );

	if ( $authenticated ) {
		$context = Timber::get_context();
		$context['wp_title'] = __( 'Hodnocení bylo již ověřeno', 'shp-partneri' );
		$context['message_type'] = 'warning';
		$context['title'] = __( 'Ouha!', 'shp-partneri' );
		$context['subtitle'] = __( 'Toto hodnocení bylo již ověřeno!', 'shp-partneri' );
		$context['text'] = '<p>' . __( 'Pokud jej na stránce partnera nevidíte, tak probíhá jeho schvalování.', 'shp-partneri' ) . '</p>';
		Timber::render( 'templates/message/message.twig', $context );
		die();		 
		return;
	}

	update_comment_meta( $comment->comment_ID, 'authenticated', time() );
	$options = get_fields('options');

	if ( !isset($options['authorized_review_email_enabled']) || !empty($options['authorized_review_email_enabled']) ) {
		$context = Timber::get_context();
		$post = new Timber\Post( $comment->comment_post_ID );
		$context['post'] = $post;
		$email_html_body = Timber::compile( 'templates/mailing/review-authorized.twig', $context );
		$email_subject = __( 'Nové hodnocení Partnera na partneri.shoptet.cz čeká na schválení', 'shp-partneri' );
		wp_mail(
			$options['authorized_review_email_recipient'],
			$email_subject,
			$email_html_body,
			[
				'From: ' . $options['email_from'],
				'Content-Type: text/html; charset=UTF-8',
			]
		);
	}

	$context = Timber::get_context();
	$context['wp_title'] = __( 'Hodnocení ověřeno', 'shp-partneri' );
	$context['message_type'] = 'thumb-up';
	$context['title'] = __( 'Super!', 'shp-partneri' );
	$context['subtitle'] = __( 'Vaše<br>hodnocení bylo ověřeno!', 'shp-partneri' );
	$context['text'] = '<p>' . __( 'Nyní proběhne schvalování vašeho hodnocení.', 'shp-partneri' ) . '</p>';
	Timber::render( 'templates/message/message.twig', $context );
	die();
} );


// Send e-mail to partner when new review approved
add_action( 'transition_comment_status',  function( $new_status, $old_status, $comment) {
	if ( $new_status !== 'approved' || 0 !== intval( $comment->comment_parent ) ) return;
	
	$context = Timber::get_context();
	$post = new Timber\Post( $comment->comment_post_ID );
	$comment = new Timber\Comment( $comment );

	$options = get_fields('options');

	// Compile and send e-mail
	$context = Timber::get_context();
	$context['title'] = __( 'Bingo!', 'shp-partneri' );
	$context['subtitle'] = __( 'Máte nové hodnocení', 'shp-partneri' );
	$context['lead'] = __( 'Přečíst a reagovat na něj můžete v detailu svého medailonku', 'shp-partneri' );
	$context['image'] = [
		'main' => 'shoptetrix-grimacing-1.png',
		'complementary' => 'shoptetrix-grimacing-2.png',
		'width' => 220,
	];
	$context['comment'] = $comment;
	$context['post'] = $post;
	$context['cta'] = [
		'title' => __( 'Přečíst hodnocení', 'shp-partneri' ),
		'link' => $post->link . '#comment-' . $comment->ID,
	];
	$email_html_body = Timber::compile( 'templates/mailing/shoptetrix-inline.twig', $context );
	$email_subject = sprintf ( __( 'Uživatel %s přidal na partneri.shoptet.cz hodnocení k Partnerovi %s', 'shp-partneri' ), $comment->comment_author, $post->post_title );

	if ( $email = $post->get_field('emailAddress') ) {
		wp_mail(
			$email,
			$email_subject,
			$email_html_body,
			[
				'From: ' . $options['email_from'],
				'Content-Type: text/html; charset=UTF-8',
			]
		);
	}
}, 10, 3 );

// Add headers to comments custom column
add_filter( 'manage_edit-comments_columns', function ( $columns ) {
	return
		array_slice( $columns, 0, 3, true ) +
		[ 'rating_column' => __( 'Hodnocení', 'shp-partneri' ) ] +
		array_slice( $columns, 3, 6, true ) + 
		[ 'authenticated_column' => __( 'E-mailové ověření', 'shp-partneri' ) ];
} );

// Add content to comments custom column
add_filter( 'manage_comments_custom_column', function ( $column, $comment_id ) {
	switch ( $column ) {
		case 'rating_column':
		if ( $rating = get_comment_meta( $comment_id, 'rating', true ) ) {
			for ($i = 1; $i <= 5; $i++) {
				echo '<span style="color:' . ( $i <= $rating ? '#ffa500' : '#ddd' ) . '">★</span>';
			}
		} else {
			echo '—';
		}
		break;
		case 'authenticated_column':
		if ( $authenticated = get_comment_meta( $comment_id, 'authenticated', true ) ) {
			echo '<strong style="color:#006505">✔ ' . __( 'Ověřeno', 'shp-partneri' ) . '</strong><br>';
			echo '<small>' . date( 'j. n. Y (G:i)', $authenticated ) . '</small>';
		} else {
			echo '<span style="color:#a00">' . __( 'Neověřeno', 'shp-partneri' ) . '</span>';
		}
		break;
	}	
}, 10, 2 );

// Check for onboarding token and authenticate
add_action( 'init' , function () {
	if ( ! isset( $_GET['onboarding_token'] ) || '' === $_GET['onboarding_token'] ) return;
	$onboarding_token = $_GET['onboarding_token'];

	$post = get_post_by_onboarding_token( $onboarding_token );

	if ( ! $post ) {
		wp_die(
			__( 'Vypadá to, že jste zadali neplatný odkaz. Zkuste to prosím znovu.', 'shp-partneri' ),
			__( 'Neplatný odkaz', 'shp-partneri' ),
			[
				'response' => 200,
				'link_text' => __( 'Přejít na partneri.shoptet.cz', 'shp-partneri' ),
				'link_url' => get_site_url(),
			]
		);
		return;
	}

	if ( $post->post_status === 'expired' ) {
		// Render expired message
		$context = Timber::get_context();
		$context['wp_title'] = __( 'Odkaz už není platný', 'shp-partneri' );
		$context['message_type'] = 'error';
		$context['title'] = __( 'Ouha!', 'shp-partneri' );
		$context['subtitle'] = __( 'Odkaz už<br>není platný :(', 'shp-partneri' );
		$context['text'] = '
			<p>
				' . __( 'Je to už více jak měsíc, co jsme vám formulář poslali a&nbsp;z&nbsp;bezpečnostních důvodů jeho platnost vypršela.', 'shp-partneri' ) . '
			</p>
			<p class="mb-0">
				' . sprintf( __( 'Ozvěte se nám na <a href="mailto:%s" target="_blank">%s</a>, koukneme na to.', 'shp-partneri' ), __( 'partneri@shoptet.cz', 'shp-partneri' ), __( 'partneri@shoptet.cz', 'shp-partneri' ) ) . '
			</p>
		';
		$context['footer_image'] = 'envelope-x';
		Timber::render( 'templates/message/message.twig', $context );
		die();
		return;
	} else if ( $post->post_status !== 'onboarding' ) {
		render_onboarding_form_error_message();
		return;
	}

	$updated = ( isset( $_GET['updated'] ) && 'true' === $_GET['updated'] );

	if ( $updated ) {
		$options = get_fields('options');
		wp_update_post([
			'ID' => $post->ID,
			'post_status' => 'pending',
		]);
		update_post_meta( $post->ID, 'onboarded', time() );

		// Send e-mail notification to admin
		$email_html_body = sprintf(
			__( 'Hej hola!<br><br>Nový Partner (%s) čeká na schválení.<br><br>Mrkni na to :)', 'shp-partneri' ),
			$post->post_title
		);
		$email_subject = __( 'Nový Partner na Partneři.shoptet.cz čeká na schválení', 'shp-partneri' );
		wp_mail(
			$options['authorized_review_email_recipient'],
			$email_subject,
			$email_html_body,
			[
				'From: ' . $options['email_from'],
				'Content-Type: text/html; charset=UTF-8',
			]
		);

		// Render success message
		$context = Timber::get_context();
		$context['wp_title'] = __( 'Úspěšně odesláno', 'shp-partneri' );
		$context['message_type'] = 'success';
		$context['title'] = __( 'Super!', 'shp-partneri' );
		$context['subtitle'] = __( 'Váš formulář byl úspěšně odeslaný.', 'shp-partneri' );
		$context['text'] = '<p>' . __( 'Teď už jen <strong>vyčkejte</strong>. Co nevidět se vám ozve náš Partner manažer a případnou spolupráci společně doladíte.', 'shp-partneri' ) . '</p>';
		$context['footer_image'] = 'envelope';
		Timber::render( 'templates/message/message.twig', $context );
		die();
		return;
	}

	// Show onboarding form
	$context = Timber::get_context();
	$context['wp_title'] = __( 'Vstupní formulář', 'shp-partneri' );
	$context['post'] = new Timber\Post( $post );
	$acf_form_args = [
		'id' => 'acf_onboarding_form',
		'post_id' => $post->ID,
		'field_groups' => [ 'group_5d109cc9c2b65' ],
		'uploader' => 'basic',
		'html_submit_button'	=> '<div class="text-center pt-4 onboarding-submit"><button type="submit" class="btn btn-primary btn-lg">' . __( 'Odeslat medailonek', 'shp-partneri' ) . '</button></div>',
	];
	$context['acf_form_args'] = $acf_form_args;
	Timber::render( 'templates/onboarding.twig', $context );
	die();
	
} );

/**
 * Disallow changing an onboarding partner status in the admin
 */
add_action( 'transition_post_status', function ( $new_status, $old_status, $post ) {
	if (
		ProfessionalPost::POST_TYPE == $post->post_type &&
		is_admin() &&
		'onboarding' == $old_status &&
		in_array( $new_status, [ 'publish', 'pending', 'draft' ] )
	) {
		wp_update_post([
			'ID' => $post->ID,
			'post_status' => 'onboarding',
		]);
	}
}, 10, 3 );

/**
 * Set professional image as post featured image
 */
add_filter( 'acf/update_value/name=image', function( $value, $post_id ) {
  // Not the correct post type, bail out
  if ( 'profesionalove' !== get_post_type( $post_id ) ) {
    return $value;
  }
  // Skip empty value
  if ( $value != ''  ) {
    // Add the value which is the image ID to the _thumbnail_id meta data for the current post
    update_post_meta( $post_id, '_thumbnail_id', $value );
  }
  return $value;
}, 10, 2 );

/**
 * Set cron for commnets authentication reminding
 */
if ( ! wp_next_scheduled( 'remind_authentication' ) ) {
  wp_schedule_event( time(), 'twicedaily', 'remind_authentication' );
}
add_action( 'remind_authentication', function() {
	remind_authentication();
});

/**
 * Set cron for expired professionals checking
 */
if ( ! wp_next_scheduled( 'expired_professionals_check' ) ) {
  wp_schedule_event( time(), 'twicedaily', 'expired_professionals_check' );
}
add_action( 'expired_professionals_check', function() {
	expired_professionals_check();
});

/**
 * Set cron for onboarding reminding
 */
if ( ! wp_next_scheduled( 'remind_onboarding' ) ) {
  wp_schedule_event( time(), 'twicedaily', 'remind_onboarding' );
}
add_action( 'remind_onboarding', function() {
	remind_onboarding();
});

/**
 * Add select controls filtering by date to admin
 */
add_action( 'restrict_manage_comments', function () {
	global $wpdb, $wp_locale;

	$months = $wpdb->get_results( "
		SELECT DISTINCT YEAR( comment_date ) AS year, MONTH( comment_date ) AS month
		FROM $wpdb->comments
		ORDER BY comment_date DESC
	" );

	$month_count = count( $months );

	if ( ! $month_count || ( 1 == $month_count && 0 == $months[0]->month ) ) {
		return;
	}

	$m = isset( $_GET['m'] ) ? (int) $_GET['m'] : 0;
	?>
	<label for="filter-by-date" class="screen-reader-text"><?php _e( 'Filtrovat podle data', 'shp-partneri' ); ?></label>
	<select name="m" id="filter-by-date">
		<option<?php selected( $m, 0 ); ?> value="0"><?php _e( '— Datum —', 'shp-partneri' ); ?></option>
	<?php
	foreach ( $months as $arc_row ) {
		if ( 0 == $arc_row->year ) {
			continue;
		}
		$month = zeroise( $arc_row->month, 2 );
		$year  = $arc_row->year;
		printf(
			"<option %s value='%s'>%s</option>\n",
			selected( $m, $year . $month, false ),
			esc_attr( $arc_row->year . $month ),
			/* translators: 1: month name, 2: 4-digit year */
			sprintf( __( '%1$s %2$d' ), $wp_locale->get_month( $month ), $year )
		);
	}
	?>
	</select>
	<?php
} );

/**
 * Filter comments in admin by month
 */
add_action( 'pre_get_comments', function( $wp_query ) {
	if( ! is_admin() ) return;
	if ( ! isset($_REQUEST['m']) || empty($_REQUEST['m']) || strlen($_REQUEST['m']) !== 6 ) return;
	$m = $_REQUEST['m'];
	$year = substr( $m, 0, 4 );
	$month = substr( $m, 4, 2 );
	$wp_query->query_vars['date_query'] = [ 'year' => $year, 'month' => $month ];
} );

/**
 * ACF url validation
 */
add_filter( 'acf/validate_value/name=url', 'handle_url_validation', 10, 2 );
add_filter( 'acf/validate_value/name=facebook', 'handle_url_validation', 10, 2 );
add_filter( 'acf/validate_value/name=instagram', 'handle_url_validation', 10, 2 );
add_filter( 'acf/validate_value/name=twitter', 'handle_url_validation', 10, 2 );
add_filter( 'acf/validate_value/name=linkedin', 'handle_url_validation', 10, 2 );

function handle_url_validation( $valid, $value ) {
	// bail early if value is already invalid
	if( ! $valid ) return $valid;

	// do not validate empty value
	if ( empty( $value ) ) return $valid;
	
  if ( ! preg_match('/^(https?:\/\/)?([a-zA-Z0-9]([a-zA-ZäöüÄÖÜ0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,6}.*/', $value ) ) {
    $valid = __( 'Zadejte prosím URL ve správném formátu', 'shp-partneri' );
  }
  
  return $valid;
}

Timber::$dirname = array('templates', 'views');

new StarterSite();

TermSyncer::init();

RequestNotifier::init();

RequestForm::init();

new ContactForm();

ProfessionalService::init();

RequestService::init();

PostService::init();

new RequestArchive();

Shoptet\ShoptetExternal::init();

Shoptet\ShoptetUserRoles::init();

Shoptet\ShoptetStats::init();

Shoptet\ShoptetSecurity::init();

Shoptet\ShoptetPostCount::init();

/**
 * Add query arguments to post count api
 */
add_filter( 'shoptet_post_count_query_args', function($query_args) {
	return [
		'partneriRequestsCount' => [
			'post_type' => 'request',
			'post_status' => [ 'publish', 'expired' ],
		],
		'partneriPartnersCount' => [
			'post_type' => 'profesionalove',
			'post_status' => 'publish',
		],
	];
} );

/**
 * Handle filtering and ordering wholesaler archive and category
 */
add_action('pre_get_posts', function( $wp_query ) {
  if (
    is_admin() ||
    ! $wp_query->is_main_query() ||
    (
      $wp_query->get( 'post_type' ) !== ProfessionalPost::POST_TYPE &&
      ! $wp_query->is_tax( ProfessionalPost::TAXONOMY )
    )
  ) return;

  $fs = new FacetedSearch( $wp_query );
  $fs->filterBySearchQuery();
  $fs->filterByMetaQuery( 'region', 'OR' );
	$fs->filterByTaxQuery( ProfessionalPost::TAXONOMY );
  $fs->order();
} );

add_filter('acf/format_value/type=text', 'do_shortcode');

add_action( 'wp_footer', function() {
  echo '<script>';
	printf( 'window.ajaxurl = \'%s\';', admin_url( 'admin-ajax.php' ) );
	echo '</script>';
} );

add_shortcode( 'partner-badges' , function() {
	$context = Timber::get_context();
	$html = Timber::compile( 'templates/row-badges.twig', $context );
	return $html;
} );
