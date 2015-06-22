<?php

namespace BTCSumo\Feeds;

/**
 * Fetch the items from a certain feed URL.
 * @param  string  $url   Feed URL to load the items from.
 * @param  integer $count The number of items to fetch.
 * @param  integer $start Get items starting from this number.
 * @return object         The fetched items if available.
 */
function fetch_feed_items( $url, $count = 5, $start = 0, &$has_more = true ) {

  // Set the lifetime transient before fetching the feeds and reset it again afterwards.
  add_filter( 'wp_feed_cache_transient_lifetime' , __NAMESPACE__ . '\\cache_lifetime' );
  $feed = fetch_feed( esc_url( $url ) );
  remove_filter( 'wp_feed_cache_transient_lifetime' , __NAMESPACE__ . '\\cache_lifetime' );

  // The variable containing all the feed items.
  $feed_items = null;

  // Make sure we have a valid feed.
  if ( ! is_wp_error( $feed ) ) {

    // Figure out how many total items there are.
    // We need this to determine if there are still more items to be loaded after this call.
    $max_items = $feed->get_item_quantity();
    $has_more = ( $max_items > $start + $count );

    // Get $count feed items starting at $start.
    $feed_items_raw = $feed->get_items( $start, $count );

    // Prepare the feeds in a nice format.
    $feed_items = [];
    foreach ( $feed_items_raw as $feed_item ) {
      $new_feed_item = [
        'title'      => esc_html( $feed_item->get_title() ),
        'permalink'  => esc_url( $feed_item->get_permalink() ),
        'date'       => $feed_item->get_date( 'j M'),
        'date_title' => $feed_item->get_date( 'j F Y, H:i:s' )
      ];
      $feed_items[] = (object) $new_feed_item;
    }
  }

  return $feed_items;
}

/**
 * Change the default feed cache recreation period.
 * @param  integer $seconds How many seconds to keep the cache for.
 * @return integer          Keep for 1 minutes.
 * @todo   Change time.
 */
function cache_lifetime( $seconds ) {
  return 60 * MINUTE_IN_SECONDS;
}

/**
 * AJAX callback to fetch feed items.
 * @return string JSON formatted string with the fetched data.
 */
function ajax_fetch_feed_items() {
  // Make it easier to access all passed $_POST values.
  extract( $_POST, EXTR_PREFIX_ALL, 'feed' );

  // Make sure this call is allowed.
  if ( ! isset( $feed_nonce ) || ! wp_verify_nonce( $feed_nonce, 'ajax_fetch_feed_items_nonce' ) ) {
    wp_send_json_error( __( 'Invalid call.', 'btcsumo' ) );
  }

  // Make sure we have a feed ID.
  if ( ! isset( $feed_id ) ) {
    wp_send_json_error( __( 'No Feed ID passed.', 'btcsumo' ) );
  }

  // Make sure we have a feed URL.
  $feed_url = get_post_meta( $feed_id, 'feed-feed-url', true );
  if ( ! $feed_url ) {
    wp_send_json_error( __( 'No Feed URL found.', 'btcsumo' ) );
  }

  // Get the feed items and pass them back.
  $feed_items = fetch_feed_items( $feed_url, $feed_count, $feed_start, $has_more );
  if ( ! isset( $feed_items ) ) {
    wp_send_json_error( __( 'Error while fetching Feed Items.', 'btcsumo' ) );
  }

  if ( empty( $feed_items ) ) {
    wp_send_json_success([
      'has_more' => false,
      'message' => __( 'No Feed Items.', 'btcsumo' ),
      'items' => []
    ]);
  }

  // Use the feed-item template to generate the list items and put them all into an array.
  $feed_items_html = [];
  foreach ( $feed_items as $feed_item ) {
    set_query_var( 'feed_item', $feed_item );
    ob_start();
    get_template_part( 'templates/feed', 'item' );
    $feed_items_html[] = ob_get_clean();
  }

  // Pass back the fetched feed items.
  wp_send_json_success([
    'has_more' => $has_more,
    'message'  => sprintf( _n( '%s Item loaded.', '%s Items loaded.', count( $feed_items ), 'btcsumo' ), count( $feed_items ) ),
    'items'    => $feed_items_html
  ]);
}
// The private one (top) is necessary to make it work when we're logged in.
add_action( 'wp_ajax_ajax_fetch_feed_items', __NAMESPACE__ . '\\ajax_fetch_feed_items' );
add_action( 'wp_ajax_nopriv_ajax_fetch_feed_items', __NAMESPACE__ . '\\ajax_fetch_feed_items' );

?>