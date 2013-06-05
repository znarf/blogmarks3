/*
bindWithDelay jQuery plugin
Author: Brian Grinstead
MIT license: http://www.opensource.org/licenses/mit-license.php

http://github.com/bgrins/bindWithDelay
http://briangrinstead.com/files/bindWithDelay

Usage:
	See http://api.jquery.com/bind/
	.bindWithDelay( eventType, [ eventData ], handler(eventObject), timeout, throttle )

Examples:
	$("#foo").bindWithDelay("click", function(e) { }, 100);
	$(window).bindWithDelay("resize", { optional: "eventData" }, callback, 1000);
	$(window).bindWithDelay("resize", callback, 1000, true);
*/

(function($) {
$.fn.bindWithDelay = function( type, data, fn, timeout, throttle ) {

	if ( $.isFunction( data ) ) {
		throttle = timeout;
		timeout = fn;
		fn = data;
		data = undefined;
	}

	// Allow delayed function to be removed with fn in unbind function
	fn.guid = fn.guid || ($.guid && $.guid++);

	// Bind each separately so that each element has its own delay
	return this.each(function() {

        var wait = null;

        function cb() {
            var e = $.extend(true, { }, arguments[0]);
            var ctx = this;
            var throttler = function() {
            	wait = null;
            	fn.apply(ctx, [e]);
            };

            if (!throttle) { clearTimeout(wait); wait = null; }
            if (!wait) { wait = setTimeout(throttler, timeout); }
        }

        cb.guid = fn.guid;

        $(this).bind(type, data, cb);
	});


}
})(jQuery);

/**
 * infinitescroll - Lightweight Infinite Scrolling
 * Copyright (c) 2012 DIY Co
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not use this
 * file except in compliance with the License. You may obtain a copy of the License at:
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software distributed under
 * the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF
 * ANY KIND, either express or implied. See the License for the specific language
 * governing permissions and limitations under the License.
 *
 * @author Brian Reavis <brian@diy.org>
 */

;(function($) {

  $.fn.infiniteScroll = function() {
    var $container = $(this);
    var $window    = $(window);
    var $body      = $('body');
    var action     = 'init';
    var waiting    = false;
    var moreExists = true;

    // defaults
    // -----------------------------------------------------------------------------
    var options = {
      threshold : 80,
      onBottom  : function() {},
      onEnd     : null,
      iScroll   : null
    };

    // parse arguments
    // -----------------------------------------------------------------------------
    if (arguments.length) {
      if (typeof arguments[0] === 'string') {
        action = arguments[0];
        if (arguments.length > 1 && typeof arguments[1] === 'object') {
          options = $.extend(options, arguments[1]);
        }
      } else if (typeof arguments[0] === 'object') {
        options = $.extend(options, arguments[0]);
      }
    }

    // initialize
    // -----------------------------------------------------------------------------
    if (action === 'init') {
      var onScroll = function() {
        if (waiting || !moreExists) return;

        var dy = options.iScroll
          ? -options.iScroll.maxScrollY + options.iScroll.y
          :  $body.outerHeight() - $window.height() - $window.scrollTop();

        if (dy < options.threshold) {
          waiting = true;
          options.onBottom(function(more) {
            if (more === false) {
              moreExists = false;
              if (typeof options.onEnd === 'function') {
                options.onEnd();
              }
            }
            waiting = false;
          });
        }
      }

      if (options.iScroll) {
        // ios scrolling
        var onScrollMove = options.iScroll.options.onScrollMove || null;
        options.iScroll.options.onScrollMove = function() {
          if (onScrollMove) onScrollMove();
          onScroll();
        }
        options.iScroll_scrollMove = onScrollMove;
      } else {
        // traditional scrolling
        $window.on('scroll.infinite resize.infinite', onScroll);
      }

      $container.data('infinite-scroll', options);
      $(onScroll);
    }

    // reinitialize (for when content changes)
    // -----------------------------------------------------------------------------
    if (action === 'reset') {
      var options = $container.data('infinite-scroll');
      if (options.iScroll) {
        if (options.iScroll_scrollMove) {
          options.iScroll.options.onScrollMove = options.iScroll_scrollMove;
        }
        options.iScroll.scrollTo(0, 0, 0, false);
      }
      $window.off('scroll.infinite resize.infinite');
      $container.infiniteScroll(options);
    }

    return this;
  };

})(jQuery);
