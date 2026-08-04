<?php

class ContactForm
{

  protected $name;
  protected $email;
  protected $message;
  protected $token;

  const ACTION = 'contact_form';

  function __construct()
  {
    add_action( 'init', [ $this, 'handle' ] );
  }

  public function handle()
  {
    $action = isset( $_POST['action'] ) && is_string( $_POST['action'] ) ? wp_unslash( $_POST['action'] ) : '';

    if ( self::ACTION != $action ) {
      return;
    }

    $raw_name = isset( $_POST['name'] ) && is_string( $_POST['name'] ) ? wp_unslash( $_POST['name'] ) : '';
    $raw_email = isset( $_POST['email'] ) && is_string( $_POST['email'] ) ? wp_unslash( $_POST['email'] ) : '';
    $raw_message = isset( $_POST['message'] ) && is_string( $_POST['message'] ) ? wp_unslash( $_POST['message'] ) : '';

    $this->name = sanitize_text_field( $raw_name );
    $this->email = sanitize_email( $raw_email );
    $this->message = sanitize_textarea_field( $raw_message );

    if ( ! $this->is_valid() ) {
      wp_die(
        'Contact form is not valid',
        'Contact form is not valid',
        [ 'response' => 400 ]
      );
    }

    if ( ! verify_recaptcha() ) {
      wp_die(
        'ReCaptcha not verified',
        'ReCaptcha not verified',
        [ 'response' => 403 ]
      );
    }

    if ( is_blacklisted( $this->name, $this->email, $this->message ) ) {
      wp_die();
    }

    if ( $this->is_spam( $raw_name, $raw_email, $raw_message ) ) {
      wp_die(
        'Contact form is not valid',
        'Contact form is not valid',
        [ 'response' => 400 ]
      );
    }

    if ( ! shp_check_onboarding_rate_limit( $this->email ) ) {
      wp_die(
        'Too many contact form submissions',
        'Too many contact form submissions',
        [ 'response' => 429 ]
      );
    }

    if ( ! $this->is_email_unique() ) {
      render_onboarding_form_error_message();
      return;
    }

    $this->token = bin2hex(openssl_random_pseudo_bytes(32));
    $this->create_partner_post();
    $this->send_success_email();
    $this->render_success_message();
  }

  protected function is_valid()
  {
    return
      ! empty($this->name) &&
      filter_var($this->email, FILTER_VALIDATE_EMAIL) &&
      ! empty($this->message)
    ;
  }

  protected function is_spam( $raw_name, $raw_email, $raw_message )
  {
    return
      shp_contains_cyrillic_word( $raw_message ) ||
      shp_contains_hebrew_word( $raw_message ) ||
      shp_contains_html_anchor_link( $raw_message ) ||
      shp_fields_contain_ru_domain( [ $raw_name, $raw_email, $raw_message ] ) ||
      ! shp_comment_has_minimum_words( $this->message )
    ;
  }

  protected function render_success_message()
  {
    $context = Timber::get_context();
    $context['wp_title'] = __( 'Zpráva odeslána', 'shp-partneri' );
    $context['title'] = __( 'Děkujeme!', 'shp-partneri' );
    $context['subtitle'] = __( 'Vaše zpráva byla odeslána', 'shp-partneri' );
    $context['text'] = '<p>' . __( 'My teď budeme netrpělivě čekat na vyplnění formuláře, který jsme vám právě poslali e-mailem. Tak na něj prosím nezapomeňte :)', 'shp-partneri' ) . '</p>';
    Timber::render( 'templates/message/message.twig', $context );
    die();
  }

  protected function send_success_email()
  {
    $onboarding_url = get_site_url( null, '?onboarding_token=' . $this->token );

    // Compile and send e-mail
    $context = Timber::get_context();
    $options = get_fields('options');
    $context['title'] = __( 'Děkujeme za Váš zájem stát se Shoptet partnerem.', 'shp-partneri' );
    $replace_pairs = [
      '%form_url%' => $onboarding_url,
    ];
    $text = $options['onboarding']['contact_mail_text'];
    $context['content'] = strtr( $text, $replace_pairs );
    $context['cta'] = [
      'title' => __( 'Vyplnit dotazník', 'shp-partneri' ),
      'link' => $onboarding_url,
    ];
    $email_html_body = Timber::compile( 'templates/mailing/message.twig', $context );
    $email_subject = $options['onboarding']['contact_mail_subject'];
    wp_mail(
      $this->email,
      $email_subject,
      $email_html_body,
      [
        'From: ' . $options['email_from'],
        'Content-Type: text/html; charset=UTF-8',
      ]
    );
  }

  protected function is_email_unique()
  {
    $query = new WP_Query( [
      'post_type' => ProfessionalPost::POST_TYPE,
      'posts_per_page' => 1,
      'post_status' => 'any',
      'meta_query' => [
        [
          'key' => 'emailAddress',
          'value' => $this->email,
        ],
      ],
    ] );
    return empty($query->posts);
  }

  protected function create_partner_post()
  {
    $postarr = [
      'post_type' => ProfessionalPost::POST_TYPE,
      'post_title' => $this->name,
      'post_status' => 'onboarding',
      'meta_input' => [
        'emailAddress' => $this->email,
        'onboarded' => 0,
        'expired' => 0,
        'onboarding_token' => $this->token,
        'message' => $this->message,
      ],
    ];
    wp_insert_post( $postarr );
  }

}
