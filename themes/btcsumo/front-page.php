<?php
/**
 * This is the front page, showing all the feed boxes.
 */

// Get all the active feeds.
query_posts( [
  'posts_per_page' => -1,
  'post_type'      => 'feeds',
  'meta_key'       => 'feed-active',
  'meta_value'     => true
] );
?>

<div class="row feed-boxes" data-nonce="<?= wp_create_nonce( 'ajax_fetch_feed_items_nonce' ); ?>">
  <?php
  if ( have_posts() ) {
    while ( have_posts() ) {
      the_post();
      get_template_part( 'templates/feed', 'box' );
    }
  } else {
    echo '<div class="alert alert-info">';
    _e( 'No feeds available right now, they are probably being updated. Check back in a bit ;-)', 'btcsumo' );
    echo '</div>';
  }
  ?>
</div>

<?php wp_reset_query(); ?>