<?php

use BTCSumo\Feeds;

// Get meta data needed to show feed list.
$site_url = get_post_meta( get_the_id(), 'feed-site-url', true );
$twitter_url = '';
if ( $twitter_username = get_post_meta( get_the_id(), 'feed-twitter-username', true ) ) {
  $twitter_url = 'https://twitter.com/' . $twitter_username;
}

// Just define the variable first to avoid error notices.
$has_more = true;

// Make sure we get something from the feed.
if ( $feed_items = Feeds\fetch_feed_items( get_the_ID(), 0, 5, $has_more ) ) : ?>
  <div class="feed-box col-xs-12 col-md-6 col-lg-4">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title">
          <?php if ( ! empty( $twitter_url ) ) : ?>
          <a href="<?= $twitter_url; ?>" target="_blank" class="feed-twitter fa fa-twitter" title="<?= __( 'Twitter', 'btcsumo' ); ?>"></a>
          <?php endif; ?>
          <a href="<?= $site_url; ?>" target="_blank"><?php the_title(); ?></a>
          <span class="feed-refresh fa fa-refresh" title="<?= __( 'Refresh', 'btcsumo' ); ?>"></span>
        </h3>
      </div>
      <ul class="feed-list list-group" data-feed-id="<?= the_ID(); ?>" data-feed-start="0" data-feed-count="5">
        <?php
        // Loop through and render each feed item.
        foreach ( $feed_items as $feed_item ) {
          Feeds\render_feed_item( $feed_item );
        }
        ?>
      </ul>
      <div class="load-more panel-footer">
        <i class="load-spinner fa fa-refresh fa-spin"></i>
        <div class="row">
          <a class="btn btn-link load-newer col-xs-6 disabled">
            <?= __( 'Newer', 'btcsumo' ); ?>
          </a>
          <a class="btn btn-link load-older col-xs-6<?= ( ! $has_more ) ? ' disabled' : ''; ?>">
            <?= __( 'Older', 'btcsumo' ); ?>
          </a>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>