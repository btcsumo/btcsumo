<?php

namespace BTCSumo\Feeds;

/**
 * Fetch the items from a certain feed URL.
 * @param  integer $feed_id   Feed ID to load the items from.
 * @param  integer $start     Get items starting from this number.
 * @param  integer $count     The number of items to fetch.
 * @param  boolean &$has_more Assign if there are more items to be fetched after this call.
 * @return object             The fetched items if available.
 */
function fetch_feed_items( $feed_id, $start = 0, $count = 5, &$has_more = true ) {
  // Get the feed items from the feed meta data.
  if ( $feed_items = get_post_meta( $feed_id, 'feed-feed-items', true ) ) {

    // Figure out how many total items there are.
    // We need this to determine if there are still more items to be loaded after this call.
    $max_items = count( $feed_items );
    $has_more = ( $max_items > $start + $count );

    // Get $count feed items starting at $start.
    return array_slice( $feed_items, $start, $count );
  }

  return [];
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
    wp_send_json_error( __( 'Nonce check failed.', 'btcsumo' ) );
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

  // Get the feed items.
  $feed_items = fetch_feed_items( $feed_id, $feed_start, $feed_count, $has_more );
  if ( ! isset( $feed_items ) ) {
    wp_send_json_error( __( 'Error while fetching Feed Items.', 'btcsumo' ) );
  }

  // There are no feed items, even though the call was successful.
  if ( empty( $feed_items ) ) {
    wp_send_json_success( [
      'hasMore' => false,
      'message' => __( 'No Feed Items.', 'btcsumo' ),
      'items'   => []
    ] );
  }

  // Use the feed-item template to generate the list items and put them all into an array.
  $feed_items_html = [];
  foreach ( $feed_items as $feed_item ) {
    $feed_items_html[] = render_feed_item( $feed_item, false );
  }

  // Pass back the fetched feed items.
  wp_send_json_success( [
    'hasMore' => $has_more,
    'message' => sprintf( _n( '%s Item loaded.', '%s Items loaded.', count( $feed_items ), 'btcsumo' ), count( $feed_items ) ),
    'items'   => $feed_items_html
  ] );
}
// The private one (top) is necessary to make it work when we're logged in.
add_action( 'wp_ajax_ajax_fetch_feed_items', __NAMESPACE__ . '\\ajax_fetch_feed_items' );
add_action( 'wp_ajax_nopriv_ajax_fetch_feed_items', __NAMESPACE__ . '\\ajax_fetch_feed_items' );

/**
 * Render an individual feed item. If the rendered content is requested, use the Output Buffer to return it.
 * @param  object  $feed_item The feed item to be rendered.
 * @param  boolean $echo      Output the rendered content or return it?
 * @return string             If requested with $echo, the rendered content.
 */
function render_feed_item( $feed_item, $echo = true ) {
  if ( ! $echo ) {
    ob_start();
  }

  // Render the feed item.
  include locate_template('templates/feed-item.php', false, false);

  if ( ! $echo ) {
    return ob_get_clean();
  }
}
