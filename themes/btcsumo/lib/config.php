<?php

namespace BTCSumo\Config;

use BTCSumo\ConditionalTagCheck;

/**
 * Enable theme features.
 */
add_theme_support( 'soil-clean-up' );     // Enable clean up from Soil.
add_theme_support( 'soil-js-to-footer' ); // Move all JS to footer.
add_theme_support( 'soil-disable-asset-versioning' ); // Disable version queries of assets.
add_theme_support( 'soil-nice-search' ); // Redirects search results from /?s=query to /search/query/, converts %20 to +.

/**
 * Configuration values
 */
// Fallback if WP_ENV isn't defined in your WordPress config
// Used in lib/assets.php to check for 'development' or 'production'
defined( 'WP_ENV' ) || define( 'WP_ENV', 'production' );
  // Path to the build directory for front-end assets
defined( 'DIST_DIR' ) || define( 'DIST_DIR', '/dist/' );

/**
 * Define which pages shouldn't have the sidebar
 */
function display_sidebar() {
  static $display;

  if ( ! isset( $display ) ) {
    $conditionalCheck = new ConditionalTagCheck(
      /**
       * Any of these conditional tags that return true won't show the sidebar.
       * You can also specify your own custom function as long as it returns a boolean.
       *
       * To use a function that accepts arguments, use an array instead of just the function name as a string.
       *
       * Examples:
       *
       * 'is_single'
       * 'is_archive'
       * ['is_page', 'about-me']
       * ['is_tax', ['flavor', 'mild']]
       * ['is_page_template', 'about.php']
       * ['is_post_type_archive', ['foo', 'bar', 'baz']]
       *
       */
      [
        'is_404',
        'is_front_page',
        'is_search',
        [ 'is_page', [ 'about', 'search' ] ]
      ]
    );

    $display = apply_filters( 'sage/display_sidebar', $conditionalCheck->result );
  }

  return $display;
}
