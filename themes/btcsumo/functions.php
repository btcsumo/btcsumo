<?php
/**
 * Sage includes
 *
 * The $sage_includes array determines the code library included in your theme.
 * Add or remove files to the array as needed. Supports child theme overrides.
 *
 * Please note that missing files will produce a fatal error.
 *
 * @link https://github.com/roots/sage/pull/1042
 */
$sage_includes = [
  'lib/utils.php',                 // Utility functions
  'lib/init.php',                  // Initial theme setup and constants
  'lib/wrapper.php',               // Theme wrapper class
  'lib/conditional-tag-check.php', // ConditionalTagCheck class
  'lib/config.php',                // Configuration
  'lib/assets.php',                // Scripts and stylesheets
  'lib/titles.php',                // Page titles
  'lib/extras.php',                // Custom functions
  'lib/wp-bs-navwalker.php',       // WordPress bootstrap nav walker
  'lib/custom-post-types.php',     // Custom Post Types registration
  'lib/custom-fields.php',         // Custom Fields for all post types
  'lib/bitcoin-ticker.php',        // Bitcoin price ticker
  'lib/feeds.php'                  // Get the requested feed items
];

foreach ( $sage_includes as $file ) {
  if ( ! $filepath = locate_template( $file ) ) {
    trigger_error( sprintf( __( 'Error locating %s for inclusion', 'btcsumo' ), $file ), E_USER_ERROR );
  }

  require_once $filepath;
}
unset( $file, $filepath );
