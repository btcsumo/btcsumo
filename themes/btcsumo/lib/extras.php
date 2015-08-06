<?php

namespace Roots\Sage\Extras;

use Roots\Sage\Config;

/**
 * Add <body> classes
 */
function body_class($classes) {
  // Add page slug if it doesn't exist
  if (is_single() || is_page() && !is_front_page()) {
    if (!in_array(basename(get_permalink()), $classes)) {
      $classes[] = basename(get_permalink());
    }
  }

  // Add class if sidebar is active
  if (Config\display_sidebar()) {
    $classes[] = 'sidebar-primary';
  }

  return $classes;
}
add_filter('body_class', __NAMESPACE__ . '\\body_class');

/**
 * Clean up the_excerpt()
 */
function excerpt_more() {
  return ' &hellip; <a href="' . get_permalink() . '">' . __('Continued', 'sage') . '</a>';
}
add_filter('excerpt_more', __NAMESPACE__ . '\\excerpt_more');

/**
 * Add ajaxurl variable to use AJAX on front-end.
 */
function ajaxurl() {
  ?>
  <script>
    var ajaxurl = '<?= admin_url( 'admin-ajax.php' ); ?>';
  </script>
  <?php
}
add_action('wp_head', __NAMESPACE__ . '\\ajaxurl');


/**
 * Add extra WP Cron schedules.
 * @param array $schedules WP Cron schedules.
 */
function add_wpcron_schedules( $schedules ) {
  $schedules['5min'] = [
    'interval' => 5 * MINUTE_IN_SECONDS,
    'display'  => __( 'Every 5 Minutes', 'btcsumo' )
  ];
  return $schedules;
}
add_filter( 'cron_schedules', __NAMESPACE__ . '\\add_wpcron_schedules' );
