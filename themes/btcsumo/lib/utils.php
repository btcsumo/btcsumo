<?php

namespace BTCSumo\Utils;

/**
 * Tell WordPress to use searchform.php from the templates/ directory
 */
function get_search_form() {
  $form = '';
  locate_template( '/templates/searchform.php', true, false );
  return $form;
}
add_filter( 'get_search_form', __NAMESPACE__ . '\\get_search_form' );

/**
 * Check if a value is empty and return the value if not.
 * @param  object $value Value to be checked.
 * @param  object $empty Value to be returned if empty.
 * @return object        Either the value or the empty-value.
 */
function empty_or_value( $value, $empty = '' ) {
  return ( empty( $value ) ) ? $empty : $value;
}

/**
 * Make an attribute string out of the passed values.
 * @param  string $attribute The attribute to make.
 * @param  string $value     The value to assign.
 * @return string            The complete attribute string.
 */
function attrify( $attribute, $value ) {
  if ( ! $value ) {
    return '';
  }
  return sprintf( ' %s="%s"', $attribute, $value );
}
