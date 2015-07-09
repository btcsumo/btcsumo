<?php

namespace BTCSumo\CustomPostTypes;

/**
 * Add all Custom Post Types.
 * @since 1.0.0
 */
function setup() {
  add_action( 'init', __NAMESPACE__ . '\\feeds_cpt' );
}
add_action( 'after_setup_theme', __NAMESPACE__ . '\\setup' );

/**
 * Register feeds Custom Post Type.
 * @since 1.0.0
 */
function feeds_cpt() {
  $labels = [
    'name'               => _x( 'Feeds', 'post type general name', 'btcsumo' ),
    'singular_name'      => _x( 'Feeds', 'post type singular name', 'btcsumo' ),
    'menu_name'          => _x( 'Feeds', 'admin menu', 'btcsumo' ),
    'name_admin_bar'     => _x( 'Feed', 'add new on admin bar', 'btcsumo' ),
    'add_new'            => _x( 'Add New', 'Feed', 'btcsumo' ),
    'add_new_item'       => __( 'Add New Feed', 'btcsumo' ),
    'new_item'           => __( 'New Feed', 'btcsumo' ),
    'edit_item'          => __( 'Edit Feed', 'btcsumo' ),
    'view_item'          => __( 'View Feed', 'btcsumo' ),
    'all_items'          => __( 'All Feeds', 'btcsumo' ),
    'search_items'       => __( 'Search Feeds', 'btcsumo' ),
    'parent_item_colon'  => __( 'Parent Feed:', 'btcsumo' ),
    'not_found'          => __( 'No Feeds found.', 'btcsumo' ),
    'not_found_in_trash' => __( 'No Feeds found in Trash.', 'btcsumo' )
  ];

  $args = [
    'has_archive'        => true,
    'labels'             => $labels,
    'menu_icon'          => 'dashicons-admin-home',
    'public'             => true,
    'publicly_queryable' => false,
    'supports'           => [ 'title', 'thumbnail' ]
  ];

  register_post_type( 'feeds', $args );
}
