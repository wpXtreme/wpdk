/**
 * wpdkTransition
 *
 * About transitions
 * For simple transition effects, include wpdk-transition.js once alongside the other JS files.
 *
 * What's inside
 *
 * wpdk-transition.js is a basic helper for transitionEnd events as well as a CSS transition emulator.
 * It's used by the other plugins to check for CSS transition support and to catch hanging transitions.
 *
 * @class           wpdkTransition
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date            2014-02-08
 * @version         3.1.0
 * @note            Base on bootstrap: transition.js v3.1.0
 *
 *
 */


// One time
if ( typeof( jQuery.fn.wpdkTransition ) === 'undefined' ) {

  /* ========================================================================
   * Bootstrap: transition.js v3.1.0
   * http://getbootstrap.com/javascript/#transitions
   * ========================================================================
   * Copyright 2011-2014 Twitter, Inc.
   * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
   * ======================================================================== */

  +function ( $ )
  {
    'use strict';

    // CSS TRANSITION SUPPORT (Shoutout: http://www.modernizr.com/)
    // ============================================================

    function transitionEnd()
    {
      var el = document.createElement( 'wpdk' )

      var transEndEventNames = {
        'WebkitTransition' : 'webkitTransitionEnd',
        'MozTransition'    : 'transitionend',
        'OTransition'      : 'oTransitionEnd otransitionend',
        'transition'       : 'transitionend'
      }

      for ( var name in transEndEventNames ) {
        if ( el.style[name] !== undefined ) {
          return { end : transEndEventNames[name] }
        }
      }

      return false // explicit for ie8 (  ._.)
    }

    // http://blog.alexmaccaw.com/css-transitions
    $.fn.emulateTransitionEnd = function ( duration )
    {
      var called = false, $el = this
      $( this ).one( $.support.transition.end, function () { called = true } )
      var callback = function () {
        if ( !called ) {
          $( $el ).trigger( $.support.transition.end )
        }
      }
      setTimeout( callback, duration )
      return this
    }

    $( function ()
    {
      $.support.transition = transitionEnd()
    } )

  }( jQuery );

}
