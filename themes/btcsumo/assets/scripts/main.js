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
    load: function($feedBox, what) {
      var $feedList = $('.feed-list', $feedBox);

      var nonce = $('.feed-boxes').attr('data-nonce');
      var id    = parseInt($feedList.attr('data-feed-id'));
      var count = parseInt($feedList.attr('data-feed-count'));
      var start = parseInt($feedList.attr('data-feed-start'));

      // Get the "Older", "Newer" and refresh buttons.
      var $newerButton   = $('.load-newer',   $feedBox);
      var $olderButton   = $('.load-older',   $feedBox);
      var $refreshButton = $('.feed-refresh', $feedBox);
      var $loadSpinner   = $('.load-spinner', $feedBox);

      // Adjust parameters for fetching items, depending on what we're doing.
      switch (what) {
        case 'newer':
          if (start <= 0) {
            // We're already at the beginning. Disable the button as this event shouldn't even occur.
            $newerButton.addClass('disabled');
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

      // Remember the button states in case the request fails.
      var newerButtonClass = $newerButton.attr('class');
      var olderButtonClass = $olderButton.attr('class');

      // Show spinner and disable all buttons while loading new content.
      $loadSpinner.fadeIn();
      $newerButton.addClass('disabled');
      $olderButton.addClass('disabled');
      $refreshButton.addClass('fa-spin disabled');

      // Make the AJAX request.
      $.post(ajaxurl, {
        'action' : 'ajax_fetch_feed_items',
        'nonce'  : nonce,
        'id'     : id,
        'count'  : count,
        'start'  : start
      })
      .done(function(response) {
        // Do we have a successful request?
        if (response.success) {
          var feedItems = response.data.items;
          if (feedItems.length > 0) {
            $feedList.attr('data-feed-start', start);
            $feedList.empty();
            feedItems.forEach(function(item) {
              $feedList.append(item);
            });

            // If there are more items in the feed, enable the "Older" button.
            if (response.data.has_more) {
              $olderButton.removeClass('disabled');
            }

            // If we're not at the beginning of the feed, make sure the "Newer" button is enabled.
            if (start > 0) {
              $newerButton.removeClass('disabled');
            }
          } else {
            console.log('That\'s all folks!');
          }
        } else {
          $newerButton.attr('class', newerButtonClass);
          $olderButton.attr('class', olderButtonClass);
          console.warn('AJAX request failed: ' + response.data);
        }
      })
      .fail(function(response) {
        // AJAX request failed.
        $newerButton.attr('class', newerButtonClass);
        $olderButton.attr('class', olderButtonClass);
        console.warn('AJAX request failed: Invalid AJAX URL.');
      })
      .always(function() {
        $refreshButton.removeClass('fa-spin disabled');
        $loadSpinner.fadeOut();
      });
    } // load()
  };

  /**
   * Bitcoin price ticker related.
   * @type {Object}
   * @todo Add AJAX call to update prices periodically.
   */
  var BTCTicker = {
    load: function() {
      $('#bitcoin-ticker-list li').click(function(e) {
        e.preventDefault();
        var $this = $(this);
        var info = $.parseJSON($this.attr('data-info'));
        $this.siblings().removeClass('active');
        $this.addClass('active');

        $('#bitcoin-ticker-price').html('<span>' + info.cur + '</span>' + info.price);
      });
    } // load()
  };

  // Use this variable to set up the common and page specific functions. If you
  // rename this variable, you will also need to rename the namespace below.
  var BTCSumo = {
    // All pages
    'common': {
      init: function() {
        BTCTicker.load();
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
      },
      finalize: function() {
        // JavaScript to be fired on the home page, after the init JS

        // Assign all feed box buttons to their respective job.
        $('.feed-box').each(function() {
          var $feedBox = $(this);

          $('.load-older', $feedBox).click(function(e) {
            e.preventDefault();
            Feeds.load($feedBox, 'older');
          });
          $('.load-newer', $feedBox).click(function(e) {
            e.preventDefault();
            Feeds.load($feedBox, 'newer');
          });
          $('.feed-refresh', $feedBox).click(function(e) {
            e.preventDefault();
            Feeds.load($feedBox, 'refresh');
          });
        });
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
      var namespace = BTCSumo;
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
