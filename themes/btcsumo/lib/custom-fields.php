<?php

namespace BTCSumo\CustomFields;

/**
 * Add all Custom Fields.
 * @since 1.0.0
 */
function setup() {
  // Add meta boxes to Post Types.
  add_action( 'add_meta_boxes', __NAMESPACE__ . '\\feeds_mb' );

  // Save post meta from the Custom Fields.
  add_action( 'save_post', __NAMESPACE__ . '\\feeds_mb_save' );
}
add_action( 'load-post.php', __NAMESPACE__ . '\\setup' );
add_action( 'load-post-new.php', __NAMESPACE__ . '\\setup' );

/**
 * Add feeds meta box.
 * @since 1.0.0
 */
function feeds_mb() {
  // Add meta box for extra feed details.
  add_meta_box(
    'feed-meta',
    esc_html__( 'Feed Meta', 'btcsumo' ),
    __NAMESPACE__ . '\\feeds_mb_cb',
    'feeds',
    'advanced',
    'default'
  );
}

/**
 * Meta box callback for feeds custom fields.
 * @param WP_Post $post Post object.
 * @since 1.0.0
 */
function feeds_mb_cb( $post ) {

  // Set the nonce field to validate it when saving.
  wp_nonce_field( 'feeds-mb-save', 'feeds-mb-nonce' ); ?>

  <p>
    <label>
      <input type="checkbox" name="feed-active" <?php checked( get_post_meta( $post->ID, 'feed-active', true ) ); ?>><?php _e( 'Active', 'btcsumo' ); ?><br>
    </label>
  </p>

  <p>
    <label>
      <?php _e( 'Site URL', 'btcsumo' ); ?><br>
      <input class="widefat" type="url" name="feed-site-url" value="<?= esc_attr( get_post_meta( $post->ID, 'feed-site-url', true ) ); ?>" size="30" required>
    </label>
  </p>

  <p>
    <label>
      <?php _e( 'Feed URL', 'btcsumo' ); ?><br>
      <input class="widefat" type="url" name="feed-feed-url" value="<?= esc_attr( get_post_meta( $post->ID, 'feed-feed-url', true ) ); ?>" size="30" required>
    </label>
  </p>

<?php
}

/**
 * Save all meta data from feeds custom fields.
 * @param  integer $post_id ID of the post we're saving
 * @return integer          If the save fails just return the post ID.
 * @since 1.0.0
 */
function feeds_mb_save( $post_id ) {

  // Verify the nonce before proceeding.
  if ( ! isset( $_POST['feeds-mb-nonce'] ) || ! wp_verify_nonce( $_POST['feeds-mb-nonce'], 'feeds-mb-save' ) ) {
    return $post_id;
  }

  /// Check if the current user has permission to edit the post.
  if( ! current_user_can( 'edit_post', $post_id ) ) {
    return $post_id;
  }

  // Check if this is and auto save.
  if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
    return $post_id;
  }

  // Get the posted data, sanitize and save it.
  update_post_meta( $post_id, 'feed-active', isset( $_POST['feed-active'] ) );

  $site_url = ( isset( $_POST['feed-site-url'] ) ) ? esc_url( $_POST['feed-site-url'] ) : false;
  update_post_meta( $post_id, 'feed-site-url', $site_url );

  $feed_url = ( isset( $_POST['feed-feed-url'] ) ) ? esc_url( $_POST['feed-feed-url'] ) : false;
  update_post_meta( $post_id, 'feed-feed-url', $feed_url );
}
