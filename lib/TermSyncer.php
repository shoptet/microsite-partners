<?php

class TermSyncer
{

  const TAX_BASE = 'category_professionals';
  const TAX_TO_SYNC = 'category_requests';
  const IMAGE_FIELD_KEY = 'field_59cab50932231';

  public static function init()
  {
    add_action( 'edit_term', [ get_called_class(), 'sync' ] );
    add_action( 'create_term', [ get_called_class(), 'sync' ] );
    add_action( 'delete_term', [ get_called_class(), 'sync' ] );
    add_action( 'admin_notices', [ get_called_class(), 'showNotice' ] );
  }

  public static function sync() {
  
    // Create missing terms
    self::syncCreate();

    // Delete redundant terms
    self::syncDelete();
  }

  protected static function syncCreate() {
    $base_terms = self::getTerms( self::TAX_BASE );
    foreach( $base_terms as $term ) {
      $synced_term = get_term_by( 'slug', $term->slug, self::TAX_TO_SYNC );
      $image_id = get_term_meta( $term->term_id, 'image', true );
      if ( ! $synced_term ) {
        $synced_term = wp_insert_term( $term->name, self::TAX_TO_SYNC, [ 'slug' => $term->slug ] );
        if( is_wp_error( $synced_term ) ) {
          throw new Exception( $synced_term->get_error_message() );
        }
        $synced_term_id = $synced_term['term_id'];
      } else {
        $synced_term_id = $synced_term->term_id;
      }
  
      // Update or create acf image value
      update_term_meta( $synced_term_id, 'image', $image_id );
      update_term_meta( $synced_term_id, '_image', self::IMAGE_FIELD_KEY );
    }
  }

  protected static function syncDelete() {
    $to_sync_terms = self::getTerms( self::TAX_TO_SYNC );
    foreach( $to_sync_terms as $term ) {
      $match = get_term_by( 'slug', $term->slug, self::TAX_BASE );
      wp_die($to_sync_terms);
      if ( ! $match ) {
        wp_delete_term( $term->term_id, self::TAX_TO_SYNC );
      }
    }
  }

  protected static function getTerms( $taxonomy ) {
    $args = [ 'hide_empty' => false ];
    $terms = get_terms( $taxonomy, $args );
    return $terms;
  }

  public static function showNotice() {
    global $pagenow;
    $is_request_category_page = (
      (
        'edit-tags.php' === $pagenow ||
        'term.php' === $pagenow
      ) &&
      isset( $_GET['taxonomy'] ) &&
      $_GET['taxonomy'] == 'category_requests'
    );
    if ( $is_request_category_page ) : ?>
      <div class="notice notice-warning">
        <p>
          <?php _e( '
            Všechny kategorie poptávek jsou automaticky synchronizovány dle kategorií profesionálů.
            Není tedy možné přidat nebo odebrat kategorii poptávek.
          ', 'shp-partneri' ); ?>
        </p>
      </div>
    <?php endif;
  }

}
