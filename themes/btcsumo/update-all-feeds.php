<?php

namespace BTCSumo\Feeds\Update;

// First of all, load up WP so we can use all it's juiciness.
require $_SERVER['DOCUMENT_ROOT'] . '/wp/wp-load.php';

// Update the feeds a maximum of once a minute.
define( 'BTCSUMO_FEED_UPDATE_LIMIT', MINUTE_IN_SECONDS );

// How far back do we go with feed items?
define( 'BTCSUMO_FEED_KEEP_LIMIT', 30 * DAY_IN_SECONDS );


// Get all the active feeds.
$feed_posts = get_posts( [
  'posts_per_page' => -1,
  'post_type'      => 'feeds',
  'meta_key'       => 'feed-active',
  'meta_value'     => true
] );

// Update the feeds one by one.
foreach ( $feed_posts as $feed_post ) {
  $last_update = get_post_meta( $feed_post->ID, 'feed-last-update', true );
  $feed_error  = get_post_meta( $feed_post->ID, 'feed-error',       true );

  // Limit the fetch. Faulty feeds skip 1 turn.
  if ( $last_update > ( time() - BTCSUMO_FEED_UPDATE_LIMIT ) || $feed_error ) {
    delete_post_meta( $feed_post->ID, 'feed-error', $feed_error );
    continue;
  }

  $feed_url = get_post_meta( $feed_post->ID, 'feed-feed-url', true );

  // Set the cache transient lifetime to 0 before fetching the feeds and reset it again afterwards.
  // We do this to prevent caching, as we're handling that ourselves above, per feed.
  add_filter( 'wp_feed_cache_transient_lifetime', '__return_zero' );
  $feed_loaded = fetch_feed( $feed_url );
  remove_filter( 'wp_feed_cache_transient_lifetime', '__return_zero' );

  // Make sure we have a valid feed.
  if ( ! is_wp_error( $feed_loaded ) ) {

    // Get the current list of feed items.
    $feed_items = get_post_meta( $feed_post->ID, 'feed-feed-items', true );
    if ( empty( $feed_items ) ) {
      $feed_items = [];
    }

    $i = 0;

    // Loop through all the fetched feed items and save the new ones to the current list.
    foreach ( $feed_loaded->get_items() as $feed_item ) {
      $new_feed_item = (object) [
        'title'     => esc_html( $feed_item->get_title() ),
        'permalink' => esc_url( $feed_item->get_permalink() ),
        'timestamp' => $feed_item->get_date( 'U' )
      ];

      // Is this one new?
      if ( empty( $feed_items ) || count( $feed_items ) === $i || $feed_items[0]->permalink !== $new_feed_item->permalink ) {
        $feed_items[] = $new_feed_item;
        $i++;
      } else {
        // No new entries.
        break;
      }
    }

    // Only keep a limited amount of entries, namely the past 30 days.
    $feed_items = array_filter( $feed_items, function( $val ) {
      return ( $val->timestamp > ( time() - BTCSUMO_FEED_KEEP_LIMIT ) );
    } );

    // Sort by date.
    usort( $feed_items, function( $a, $b ) {
      return ( ( $a->timestamp >= $b->timestamp ) ? -1 : 1 );
    } );

    // Update the feed items.
    update_post_meta( $feed_post->ID, 'feed-feed-items', $feed_items );

    // Remove any error.
    delete_post_meta( $feed_post->ID, 'feed-error', $feed_error );

  } else {
    // Feed is faulty.
    update_post_meta( $feed_post->ID, 'feed-error', true );
  }

  // Update the time of the current feed update.
  update_post_meta( $feed_post->ID, 'feed-last-update', time(), $last_update );
}

?>

done