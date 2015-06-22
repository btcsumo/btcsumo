/* ========================================================================
 * DOM-based Routing
 * Based on http://goo.gl/EUTi53 by Paul Irish
 *
 * Only fires on body classes that match. If a body class contains a dash,
 * replace the dash with an underscore when adding it to the object below.
 *
 * .noConflict()
 * The routing is enclosed within an anonymous function so that you can
 * always reference jQuery with $, even when in .noConflict() mode.
 * ======================================================================== */

(function($) {

  // All Feed related methods.
  var Feeds = {
    load: function( $feed_box, what ) {
      var $feed_list = $( '.feed-list', $feed_box );

      var nonce = $( '.feed-boxes' ).attr( 'data-nonce' );
      var id    = parseInt( $feed_list.attr( 'data-feed-id') );
      var count = parseInt( $feed_list.attr( 'data-feed-count') );
      var start = parseInt( $feed_list.attr( 'data-feed-start') );

      // Adjust parameters for fetching items, depending on what we're doing.
      switch( what ) {
        case 'newer':
          if ( start <= 0 ) {
            // We're already at the beginning, nothing to do.
            return;
          }
          start -= count;
          break;
        case 'older':
          start += count;
          break;
        case 'refresh':
          start = 0;
          break;
      }

      // Get the "Older" and "Newer" buttons.
      var $prev_button = $( '.load-newer', $feed_box );
      var $next_button = $( '.load-older', $feed_box );

      // Make the AJAX request.
      $.post( ajaxurl, {
        'action' : 'ajax_fetch_feed_items',
        'nonce'  : nonce,
        'id'     : id,
        'count'  : count,
        'start'  : start
      },
      function( response ) {
        // Do we have a successful request?
        if ( response.success ) {
          var feed_items = response.data.items;
          if ( feed_items.length > 0 ) {
            $feed_list.attr( 'data-feed-start', start );
            $feed_list.empty();
            feed_items.forEach(function( item ) {
              $feed_list.append( item );
            });

            // If there are no more items in the feed, disable the "Older" button.
            if ( ! response.data.has_more ) {
              $next_button.addClass( 'disabled' );
            } else {
              $next_button.removeClass( 'disabled' );
            }

            // If we're not at the beginning of the feed, make sure the "Newer" button is enabled.
            if ( start > 0 ) {
              $prev_button.removeClass( 'disabled' );
            } else {
              $prev_button.addClass( 'disabled' );
            }

          } else {
            console.log( 'That\'s all folks!' );
          }
        } else {
          console.warn( 'AJAX request failed: ' + response.data );
        }
      });
    } // load()
  };

  // Use this variable to set up the common and page specific functions. If you
  // rename this variable, you will also need to rename the namespace below.
  var Sage = {
    // All pages
    'common': {
      init: function() {
        // JavaScript to be fired on all pages
      },
      finalize: function() {
        // JavaScript to be fired on all pages, after page specific JS is fired
      }
    },
    // Home page
    'home': {
      init: function() {
        // JavaScript to be fired on the home page

        $( '.feed-box' ).each(function() {
          var $feed_box = $( this );

          $( '.load-older', $feed_box ).click(function( e ) {
            e.preventDefault();
            Feeds.load( $feed_box, 'older' );
          });
          $( '.load-newer', $feed_box ).click(function( e ) {
            e.preventDefault();
            Feeds.load( $feed_box, 'newer' );
          });
          $( '.feed-refresh', $feed_box ).click(function( e ) {
            e.preventDefault();
            Feeds.load( $feed_box, 'refresh' );
          });
        });
      },
      finalize: function() {
        // JavaScript to be fired on the home page, after the init JS
      }
    },
    // About us page, note the change from about-us to about_us.
    'about_us': {
      init: function() {
        // JavaScript to be fired on the about us page
      }
    }
  };

  // The routing fires all common scripts, followed by the page specific scripts.
  // Add additional events for more control over timing e.g. a finalize event
  var UTIL = {
    fire: function(func, funcname, args) {
      var fire;
      var namespace = Sage;
      funcname = (funcname === undefined) ? 'init' : funcname;
      fire = func !== '';
      fire = fire && namespace[func];
      fire = fire && typeof namespace[func][funcname] === 'function';

      if (fire) {
        namespace[func][funcname](args);
      }
    },
    loadEvents: function() {
      // Fire common init JS
      UTIL.fire('common');

      // Fire page-specific init JS, and then finalize JS
      $.each(document.body.className.replace(/-/g, '_').split(/\s+/), function(i, classnm) {
        UTIL.fire(classnm);
        UTIL.fire(classnm, 'finalize');
      });

      // Fire common finalize JS
      UTIL.fire('common', 'finalize');
    }
  };

  // Load Events
  $(document).ready(UTIL.loadEvents);

})(jQuery); // Fully reference jQuery after this point.
