<?php

use BTCSumo\Feeds;

// Get meta data needed to show feed list.
$site_url = get_post_meta( get_the_id(), 'feed-site-url', true );
$feed_url = get_post_meta( get_the_id(), 'feed-feed-url', true );

// Make sure we get something from the feed.
if ( $feed_items = Feeds\fetch_feed_items( $feed_url ) ) : ?>
  <div class="feed-box col-xs-12 col-md-6 col-lg-4">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title">
          <a href="<?= $site_url; ?>" target="_blank"><?php the_title(); ?></a>
          <span class="feed-refresh glyphicon glyphicon-refresh" title="<?= __( 'Refresh', 'btcsumo' ); ?>"></span>
        </h3>
      </div>
      <ul class="feed-list list-group" data-feed-id="<?= the_ID(); ?>" data-feed-count="5" data-feed-start="0">
        <?php
        // Loop through each feed item and display each item as a hyperlink.
        foreach ( $feed_items as $feed_item ) {
          set_query_var( 'feed_item', $feed_item );
          get_template_part( 'templates/feed', 'item' );
        }
        ?>
      </ul>
      <div class="load-more panel-footer">
        <div class="row">
          <a class="btn btn-link load-newer col-xs-6 disabled">
            <?= __( 'Newer', 'btcsumo' ); ?>
          </a>
          <a class="btn btn-link load-older col-xs-6">
            <?= __( 'Older', 'btcsumo' ); ?>
          </a>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>