<?php

class ProfessionalAdmin
{

  static function init () {
    add_action( 'admin_menu', [ get_called_class(), 'admin_menu' ] );
    add_action( 'admin_post_export_professionals', [ get_called_class(), 'handle_export' ] );
  }

  static function admin_menu () {
    add_submenu_page(
      'edit.php?post_type=profesionalove',
      __( 'Export profesionálů', 'shp-partneri' ),
      __( 'Export', 'shp-partneri' ),
      'manage_options',
      'export',
      [ get_called_class(), 'export_page' ]
    );
  }

  static function export_page () {
    ?>
    <form action="<?= admin_url( 'admin-post.php' ); ?>" method="post">
      <input type="hidden" name="action" value="export_professionals">
      <?php submit_button(  __( 'Exportovat profesionály', 'shp-partneri' ) ); ?>
    </form>
    <?php
  }

  static function handle_export () {
    if ( is_current_user_admin() ) {
      self::export();
    }
  }

  static function get_row ($post_id) {
    $name = get_the_title($post_id);
    $email = get_post_meta($post_id, 'emailAddress', true);
    $url = get_post_meta($post_id, 'url', true);
    $badge = get_post_meta($post_id, 'verifiedLevel', true);
    $partner_manager = get_post_meta($post_id, 'partnerManager', true);
    $country = get_post_meta($post_id, 'country', true);
    $categories = wp_get_post_terms($post_id, 'category_professionals', ['fields' => 'names']);
    $categories = implode(',', $categories);
    $price_per_hour = get_post_meta($post_id, 'price_per_hour_display', true);
    $price_currency = get_post_meta($post_id, 'price_currency', true);
    $price_vat_payer = get_post_meta($post_id, 'vat_payer', true);
    $price_text = get_post_meta($post_id, 'price_text', true);
    $post_status = get_post_status($post_id);
    $publish_date = get_the_date('c', $post_id);

    return [
      $name,
      $email,
      $url,
      $badge,
      $partner_manager,
      $country,
      $categories,
      $price_per_hour,
      $price_currency,
      $price_vat_payer,
      $price_text,
      $post_status,
      $publish_date,
    ];
  }

  static function export () {
    $file_path = get_temp_dir() . 'export.csv';
    $fp = fopen( $file_path, 'w' );
    $header = [
      'partner name',
      'e-mail',
      'url',
      'badge',
      'partner manager',
      'country',
      'categories',
      'price_per_hour',
      'price_currency',
      'price_vat_payer',
      'price_text',
      'post_status',
      'publish date',
    ];
    fputcsv( $fp, $header );

    $posts_per_page = 50;
    $paged = 1;

    $args = [
      'post_type' => 'profesionalove',
      'posts_per_page' => $posts_per_page,
      'post_status' => 'any',
      'fields' => 'ids',
      'no_found_rows' => true,
      'update_post_meta_cache' => false,
      'update_post_term_cache' => false,
    ];

    do {
      $args['paged'] = $paged;
      $wp_query = new \WP_Query( $args );
      foreach( $wp_query->posts as $post_id ) {
        $row = self::get_row($post_id);
        fputcsv( $fp, $row );
      }
      stop_the_insanity();
      $paged++;
    } while ( count($wp_query->posts) );

    fclose( $fp );

    // Http headers for downloads
    header( 'Pragma: public' );
    header( 'Expires: 0' );
    header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' ); 
    header( 'Content-Type: application/csv' );
    header( 'Content-Disposition: attachment; filename=export.csv' );
    header( 'Content-Length: ' . filesize( $file_path ) );
    readfile( $file_path );

    unlink( $file_path );
  }

}