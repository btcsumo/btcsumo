<?php
/**
 * Template Name: Feeds
 *
 * This is the template for the front page, showing all the feed boxes.
 */

// Get all the active feeds.
query_posts( [
  'posts_per_page' => -1,
  'post_type'  => 'feeds',
  'meta_key'   => 'feed-active',
  'meta_value' => true
] );
?>

<div class="container">
  <div class="row feed-boxes" data-nonce="<?= wp_create_nonce( 'ajax_fetch_feed_items_nonce' ); ?>">

  <?php while ( have_posts() ) : the_post(); ?>
    <?php get_template_part( 'templates/feed', 'box' ); ?>
  <?php endwhile; ?>

  </div>
</div>

<?php wp_reset_query(); ?>