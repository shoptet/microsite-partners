<?php

class RequestNotifier
{
  static function init()
  {
    add_action( 'shp/request_form/save', [ get_called_class(), 'newRequestAdmin' ] );
    add_action( 'shp/request_form/save', [ get_called_class(), 'newRequestAuthor' ] );
    add_action( 'shp/request_service/approve', [ get_called_class(), 'approvedRequestAuthor' ] );
    add_action( 'shp/request_service/approve', [ get_called_class(), 'approvedRequestProfessionals' ] );
    add_action( 'shp/request_message/validate', [ get_called_class(), 'messageRequestAuthor' ], 10, 2 );
    add_action( 'shp/request_service/expire', [ get_called_class(), 'expiredRequestAuthor' ] );
    add_action( 'shp/professional_service/unsubscribe', [ get_called_class(), 'unsubscribeRequestPartner' ] );
  }

  static function getDefaultEmailHeaders() {
    $options = get_fields( 'options' );
    $headers = [
      'From: ' . $options['email_from'],
      'Content-Type: text/html; charset=UTF-8',
    ];
    return $headers;
  }

  static function newRequestAdmin( $post_id )
  {
    $template = 'templates/mailing/request/new-request-admin.twig';
	  $options = get_fields( 'options' );

    $admin_email = $options['authorized_review_email_recipient'];
    $headers = self::getDefaultEmailHeaders();
    $subject = __( 'Nová poptávka čeká na schválení', 'shp-partneri' );

    $context = Timber::get_context();
    $context['post_admin_link'] = admin_url( sprintf( 'post.php?post=%s&action=edit', $post_id ) );
    $context['post'] = new Timber\Post( $post_id );
    $html_message = Timber::compile( $template, $context );
    
    wp_mail( $admin_email, $subject, $html_message, $headers );
  }

  static function newRequestAuthor( $post_id )
  {
    $template = 'templates/mailing/request/new-request-author.twig';

    $request_post = new RequestPost( $post_id );
    $author_email = $request_post->getMeta( 'author_email' );
    $headers = self::getDefaultEmailHeaders();
    $subject = __( 'Děkujeme za vložení vaší poptávky. Koukněte co se bude dít dále.', 'shp-partneri' );

    $context = Timber::get_context();
    $context['expiration_link'] = $request_post->getAuthTokenURL( [ 'action' => 'expire' ] );
    $html_message = Timber::compile( $template, $context );
    
    wp_mail( $author_email, $subject, $html_message, $headers );
  }

  static function approvedRequestAuthor( $post_id )
  {
    $template = 'templates/mailing/request/approve-request-author.twig';

    $request_post = new RequestPost( $post_id );
    $author_email = $request_post->getMeta( 'author_email' );
    $term =  $request_post->getTerm();

    if( ! $author_email ) {
      throw new Exception( 'No author e-mail at a request post with ID ' . $post_id );
    }

    if( ! $term ) {
      throw new Exception( 'No term related to a request post with ID ' . $post_id );
    }

    $headers = self::getDefaultEmailHeaders();
    $subject = __( 'Vaše poptávka byla schválena. Držíme palce ať si vyberete toho nejlepšího partnera.', 'shp-partneri' );

    $context = Timber::get_context();
    $context['term'] = $term;
    $context['expiration_link'] = $request_post->getAuthTokenURL( [ 'action' => 'expire' ] );
    $html_message = Timber::compile( $template, $context );
    
    wp_mail( $author_email, $subject, $html_message, $headers );
  }

  static function approvedRequestProfessionals( $post_id )
  {
    $template = 'templates/mailing/request/approve-request-professional.twig';

    $request_post = new RequestPost( $post_id );
    
    if ( $request_post->getMeta( '_notification_sent' ) ) {
      return;
    }

    $author_email = $request_post->getMeta( 'author_email' );
    $term =  $request_post->getTerm();

    if( ! $term ) {
      throw new Exception( 'No term related to a request post with ID ' . $post_id );
    }

    $headers = self::getDefaultEmailHeaders();

    $related_term = get_term_by( 'slug', $term->slug, ProfessionalPost::TAXONOMY );

    $subject = sprintf(
      __( 'Přibyla nová poptávka v kategorii %s. Reaguj na ní ještě dříve než se zobrazí na Shoptet Partnerech.', 'shp-partneri' ),
      $related_term->name
    );

    $professionals = ProfessionalPost::getAllToNotify( $related_term );

    foreach( $professionals as $professional_post ) {
      $professional_email = $professional_post->getMeta( 'emailAddress' );

      if( ! $professional_email ) {
        try {
          throw new Exception( 'Professional ( ' . $professional_post->getID() . ' ) has no e-mail.' );
        } catch ( Exception $e ) {
          error_log( $e->getMessage() );
        }
        continue;
      }
      
      $context = Timber::get_context();
      $context['request_post'] = new TimberPost( $request_post->getID() );
      $context['preview_link'] = $request_post->getFuturePreviewLink();
      $context['category_name'] = $related_term->name;
      $context['unsubscribe_category_link'] = $professional_post->getAuthTokenURL( [ 'unsubscribe_category' => $related_term->term_id ] );
      $context['unsubscribe_all_link'] = $professional_post->getAuthTokenURL( [ 'unsubscribe' => 'all' ] );
      $html_message = Timber::compile( $template, $context );

      wp_mail( $professional_email, $subject, $html_message, $headers );
    }

    $request_post->setMeta( '_notification_sent', true );
  }

  static function messageRequestAuthor( $post_id, $message_arr )
  {
    $template = 'templates/mailing/request/message-request-author.twig';

    $request_post = new RequestPost( $post_id );
    $author_email = $request_post->getMeta( 'author_email' );
    $headers = self::getDefaultEmailHeaders();
    $subject = __( 'Máte novou reakci na vaši poptávku. Omkrněte jí.', 'shp-partneri' );

    $context = Timber::get_context();
    $context['message'] = $message_arr;
    $context['expiration_link'] = $request_post->getAuthTokenURL( [ 'action' => 'expire' ] );
    $html_message = Timber::compile( $template, $context );
    
    wp_mail( $author_email, $subject, $html_message, $headers );
  }

  static function expiredRequestAuthor( $post_id )
  {
    $template = 'templates/mailing/request/expired-request-author.twig';

    $request_post = new RequestPost( $post_id );
    $author_email = $request_post->getMeta( 'author_email' );
    $headers = self::getDefaultEmailHeaders();
    $subject = __( 'Hotovo, nebo teprve začátek? Tak či tak děkujeme za vložení poptávky a tešíme se na výsledné dílo.', 'shp-partneri' );

    $context = Timber::get_context();
    $html_message = Timber::compile( $template, $context );
    
    wp_mail( $author_email, $subject, $html_message, $headers );
  }

  static function unsubscribeRequestPartner( $post_id )
  {
    $template = 'templates/mailing/request/unsubscribe-request-professional.twig';

    $professional_post = new ProfessionalPost( $post_id );
    $professional_email = $professional_post->getMeta( 'emailAddress' );

    if( ! $professional_email ) {
      throw new Exception( 'Professional (' . $p->ID . ') has no e-mail.' );
    }

    $headers = self::getDefaultEmailHeaders();
    $subject = __( 'Nesprávná poptávka, nebo jen moc práce? Nevadí nic není definitivní.', 'shp-partneri' );

    $context = Timber::get_context();
    $html_message = Timber::compile( $template, $context );
    
    wp_mail( $professional_email, $subject, $html_message, $headers );
  }

}