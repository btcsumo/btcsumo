<?php

/**
 * Class Name: wp_bootstrap_navwalker
 * GitHub URI: https://github.com/twittem/wp-bootstrap-navwalker
 * Description: A custom WordPress nav walker class to implement the Bootstrap 3 navigation style in a custom theme using the WordPress built in menu manager.
 * Version: 2.0.4-tweaked
 * Author: Edward McIntyre - @twittem
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

class WP_Bootstrap_Nav_Walker extends Walker_Nav_Menu {

  /**
   * @see Walker::start_lvl()
   * @since 3.0.0
   *
   * @param string $output Passed by reference. Used to append additional content.
   * @param int    $depth  Depth of page. Used for padding.
   * @param array  $args
   */
  public function start_lvl( &$output, $depth = 0, $args = [] ) {
    $indent = str_repeat( "\t", $depth );
    $output .= "\n$indent<ul role=\"menu\" class=\" dropdown-menu\">\n";
  }

  /**
   * @see Walker::start_el()
   * @since 3.0.0
   *
   * @param string $output Passed by reference. Used to append additional content.
   * @param object $item   Menu item data object.
   * @param int    $depth  Depth of menu item. Used for padding.
   * @param array  $args
   * @param int    $id     Menu item ID.
   */
  public function start_el( &$output, $item, $depth = 0, $args = [], $id = 0 ) {
    $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

    /**
     * Dividers, Headers or Disabled
     * =============================
     * Determine whether the item is a Divider, Header, Disabled or regular
     * menu item. To prevent errors we use the strcasecmp() function to so a
     * comparison that is not case sensitive. The strcasecmp() function returns
     * a 0 if the strings are equal.
     */
    if ( strcasecmp( $item->attr_title, 'divider' ) == 0 && $depth === 1 ) {
      $output .= $indent . '<li role="presentation" class="divider">';
    } else if ( strcasecmp( $item->title, 'divider') == 0 && $depth === 1 ) {
      $output .= $indent . '<li role="presentation" class="divider">';
    } else if ( strcasecmp( $item->attr_title, 'dropdown-header') == 0 && $depth === 1 ) {
      $output .= $indent . '<li role="presentation" class="dropdown-header">' . esc_attr( $item->title );
    } else if ( strcasecmp($item->attr_title, 'disabled' ) == 0 ) {
      $output .= $indent . '<li role="presentation" class="disabled"><a href="#">' . esc_attr( $item->title ) . '</a>';
    } else {

      $classes = empty( $item->classes ) ? [] : (array) $item->classes;
      $classes[] = 'menu-item-' . $item->ID;
      $classes[] = ( $args->has_children ) ? 'dropdown' : '';
      $classes[] = ( in_array( 'current-menu-item', $classes ) ) ? 'active' : '';

      $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
      $class_names = ( $class_names ) ? ' class="' . esc_attr( $class_names ) . '"' : '';

      $id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
      $id = ( $id ) ? ' id="' . esc_attr( $id ) . '"' : '';

      $output .= $indent . '<li' . $id . $class_names . '>';

      $atts = [];
      $atts['title']  = ( ! empty( $item->title ) )  ? $item->title  : '';
      $atts['target'] = ( ! empty( $item->target ) ) ? $item->target : '';
      $atts['rel']    = ( ! empty( $item->xfn ) )    ? $item->xfn    : '';

      // If item has_children add atts to a.
      if ( $args->has_children && 0 === $depth ) {
        $atts['href']          = '#';
        $atts['data-toggle']   = 'dropdown';
        $atts['class']         = 'dropdown-toggle';
        $atts['aria-haspopup'] = 'true';
      } else {
        $atts['href'] = ( ! empty( $item->url ) ) ? $item->url : '';
      }


      $atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args );

      $attributes = '';
      foreach ( $atts as $attr => $value ) {
        if ( ! empty( $value ) ) {
          $value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
          $attributes .= sprintf( ' %s="%s"', $attr, $value );
        }
      }

      $item_output = $args->before;

      /*
       * Font Awesome
       * ============
       * Since the the menu item is NOT a Divider or Header we check to see
       * if there is a value in the attr_title property. If the attr_title
       * property is NOT null we apply it as the class name for the font awesome icon.
       */
      $item_output .= '<a' . $attributes . '>';
      if ( ! empty( $item->attr_title ) ) {
        $item_output .= '<i class="fa ' . esc_attr( $item->attr_title ) . '"></i>&nbsp;';
      }

      $item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
      $item_output .= ( $args->has_children && 0 === $depth ) ? ' <span class="caret"></span></a>' : '</a>';

      $item_output .= $args->after;

      $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
    }
  }

  /**
   * Traverse elements to create list from elements.
   *
   * Display one element if the element doesn't have any children otherwise,
   * display the element and its children. Will only traverse up to the max
   * depth and no ignore elements under that depth.
   *
   * This method shouldn't be called directly, use the walk() method instead.
   *
   * @see Walker::display_element()
   * @since 2.5.0
   *
   * @param  object $element           Data object.
   * @param  array  $children_elements List of elements to continue traversing.
   * @param  int    $max_depth         Max depth to traverse.
   * @param  int    $depth             Depth of current element.
   * @param  array  $args
   * @param  string $output            Passed by reference. Used to append additional content.
   * @return null                      Null on failure with no changes to parameters.
   */
  public function display_element( $element, &$children_elements, $max_depth, $depth, $args, &$output ) {
        if ( ! $element ) {
          return;
        }

        $id_field = $this->db_fields['id'];

        // Display this element.
        if ( is_object( $args[0] ) ) {
          $args[0]->has_children = ! empty( $children_elements[ $element->$id_field ] );
        }

        parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
    }

  /**
   * Menu Fallback
   * =============
   * If this function is assigned to the wp_nav_menu's fallback_cb variable
   * and a manu has not been assigned to the theme location in the WordPress
   * menu manager the function with display nothing to a non-logged in user,
   * and will add a link to the WordPress menu manager if logged in as an admin.
   *
   * @param array $args Passed from the wp_nav_menu function.
   *
   */
  public static function fallback( $args ) {
    if ( current_user_can( 'manage_options' ) ) {

      extract( $args );

      $output = sprintf(
        '<ul%s%s><li><a href="%s">%s</a></li></ul>',
        ( $menu_id )    ? ' id="'    . $menu_id    . '"' : '',
        ( $menu_class ) ? ' class="' . $menu_class . '"' : '',
        esc_url( admin_url( 'nav-menus.php' ) ),
        __( 'Add a menu', 'btcsumo' )
      );

      if ( $container ) {
        $output = sprintf(
          '<%1$s%2$s%3$s>%4$s</%1$s>',
          $container,
          ( $container_id )    ? ' id="'    . $container_id    . '"' : '',
          ( $container_class ) ? ' class="' . $container_class . '"' : '',
          $output
        );
      }

      echo $output;
    }
  }
}
