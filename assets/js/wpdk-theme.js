/**
 * WPDK Theme Javascript
 *
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2013-12-09
 */

jQuery( function ( $ )
{
  "use strict";

  /**
   * Main Theme utility class
   *
   * @class           WPDKTheme
   * @author          =undo= <info@wpxtre.me>
   * @copyright       Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
   * @date            2013-12-09
   * @version         1.0.0
   *
   */
  window.WPDKTheme = (function ()
  {

    /**
     * This Object
     *
     * @type {{}}
     */
    var $t = {
      version : '1.0.0',
      init    : _init
    };

    /**
     * Skip Link Focus
     *
     * @private
     */
    function _skipLinkFocus()
    {
      var is_webkit = navigator.userAgent.toLowerCase().indexOf( 'webkit' ) > -1,
        is_opera = navigator.userAgent.toLowerCase().indexOf( 'opera' ) > -1,
        is_ie = navigator.userAgent.toLowerCase().indexOf( 'msie' ) > -1;

      if ( ( is_webkit || is_opera || is_ie ) && 'undefined' !== typeof( document.getElementById ) ) {
        var eventMethod = ( window.addEventListener ) ? 'addEventListener' : 'attachEvent';
        window[ eventMethod ]( 'hashchange', function ()
        {
          var element = document.getElementById( location.hash.substring( 1 ) );

          if ( element ) {
            if ( !/^(?:a|select|input|button|textarea)$/i.test( element.tagName ) ) {
              element.tabIndex = -1;
            }

            element.focus();
          }
        }, false );
      }
    }

    /**
     * Handles toggling the navigation menu for small screens.
     *
     * @private
     */
    function _navigation()
    {
      var container, button, menu;

      container = document.getElementById( 'site-navigation' );
      if ( !container ) {
        return;
      }

      button = container.getElementsByTagName( 'h1' )[0];
      if ( 'undefined' === typeof button ) {
        return;
      }

      menu = container.getElementsByTagName( 'ul' )[0];

      // Hide menu toggle button if menu is empty and return early.
      if ( 'undefined' === typeof menu ) {
        button.style.display = 'none';
        return;
      }

      if ( -1 === menu.className.indexOf( 'nav-menu' ) ) {
        menu.className += ' nav-menu';
      }

      button.onclick = function ()
      {
        if ( -1 !== container.className.indexOf( 'toggled' ) ) {
          container.className = container.className.replace( ' toggled', '' );
        }
        else {
          container.className += ' toggled';
        }
      };
    }

    /**
     * Navigation
     *
     * @private
     */
    function _imageNavigation()
    {
      $( document ).keydown( function ( e )
      {
        var url = false;
        if ( e.which === 37 ) {  // Left arrow key code
          url = $( '.nav-previous a' ).attr( 'href' );
        }
        else if ( e.which === 39 ) {  // Right arrow key code
          url = $( '.entry-attachment a' ).attr( 'href' );
        }
        if ( url && ( !$( 'textarea, input' ).is( ':focus' ) ) ) {
          window.location = url;
        }
      } );
    }

    /**
     * Theme Customizer enhancements for a better user experience.
     *
     * Contains handlers to make Theme Customizer preview reload changes asynchronously.
     */
    function _customizer()
    {
      // Site title and description.
      wp.customize( 'blogname', function ( value )
      {
        value.bind( function ( to )
        {
          $( '.site-title a' ).text( to );
        } );
      } );
      wp.customize( 'blogdescription', function ( value )
      {
        value.bind( function ( to )
        {
          $( '.site-description' ).text( to );
        } );
      } );
      // Header text color.
      wp.customize( 'header_textcolor', function ( value )
      {
        value.bind( function ( to )
        {
          if ( 'blank' === to ) {
            $( '.site-title, .site-description' ).css( {
              'clip'     : 'rect(1px, 1px, 1px, 1px)',
              'position' : 'absolute'
            } );
          }
          else {
            $( '.site-title, .site-description' ).css( {
              'clip'     : 'auto',
              'color'    : to,
              'position' : 'relative'
            } );
          }
        } );
      } );
    }

    /**
     *
     * @private
     */
    function _modernizrSVG()
    {
      // SVG fallback
      // toddmotto.com/mastering-svg-use-for-a-retina-web-fallbacks-with-png-script#update
      if ( !Modernizr.svg ) {
        var imgs = document.getElementsByTagName( 'img' );
        for ( var i = 0; i < imgs.length; i++ ) {
          if ( /.*\.svg$/.test( imgs[i].src ) ) {
            imgs[i].src = imgs[i].src.slice( 0, -3 ) + 'png';
          }
        }
      }
    }

    /**
     * Avoid `console` errors in browsers that lack a console.
     *
     * @private
     */
    function _initConsole()
    {
      var method;
      var noop = function () {};
      var methods = [
        'assert', 'clear', 'count', 'debug', 'dir', 'dirxml', 'error',
        'exception', 'group', 'groupCollapsed', 'groupEnd', 'info', 'log',
        'markTimeline', 'profile', 'profileEnd', 'table', 'time', 'timeEnd',
        'timeStamp', 'trace', 'warn'
      ];
      var length = methods.length;
      window.console = (window.console = window.console || {});

      while ( length-- ) {
        method = methods[length];

        // Only stub undefined methods.
        if ( !console[method] ) {
          console[method] = noop;
        }
      }
    }

    /**
     * Return an instance of WPDKTheme object
     *
     * @private
     *
     * @return {}
     */
    function _init()
    {
      /* Your init here. */
      //_customizer();
      _imageNavigation();
      _navigation();
      _skipLinkFocus();
      //_modernizrSVG();
      _initConsole();

      return $t;
    };

    return $t.init();

  })();

} );

/*
 HTML5 Shiv v3.6.2 | @afarkas @jdalton @jon_neal @rem | MIT/GPL2 Licensed
 https://github.com/aFarkas/html5shiv
 */
(function ( j, f )
{
  function s( a, b )
  {
    var c = a.createElement( "p" ), m = a.getElementsByTagName( "head" )[0] || a.documentElement;
    c.innerHTML = "x<style>" + b + "</style>";
    return m.insertBefore( c.lastChild, m.firstChild )
  }

  function o()
  {
    var a = d.elements;
    return"string" == typeof a ? a.split( " " ) : a
  }

  function n( a )
  {
    var b = t[a[u]];
    b || (b = {}, p++, a[u] = p, t[p] = b);
    return b
  }

  function v( a, b, c )
  {
    b || (b = f);
    if ( e ) {
      return b.createElement( a );
    }
    c || (c = n( b ));
    b = c.cache[a] ? c.cache[a].cloneNode() : y.test( a ) ? (c.cache[a] = c.createElem( a )).cloneNode() : c.createElem( a );
    return b.canHaveChildren && !z.test( a ) ? c.frag.appendChild( b ) : b
  }

  function A( a, b )
  {
    if ( !b.cache ) {
      b.cache = {}, b.createElem = a.createElement, b.createFrag = a.createDocumentFragment, b.frag = b.createFrag();
    }
    a.createElement = function ( c ) {return!d.shivMethods ? b.createElem( c ) : v( c, a, b )};
    a.createDocumentFragment = Function( "h,f", "return function(){var n=f.cloneNode(),c=n.createElement;h.shivMethods&&(" + o().join().replace( /\w+/g, function ( a )
    {
      b.createElem( a );
      b.frag.createElement( a );
      return'c("' + a + '")'
    } ) + ");return n}" )( d, b.frag )
  }

  function w( a )
  {
    a || (a = f);
    var b = n( a );
    if ( d.shivCSS && !q && !b.hasCSS ) {
      b.hasCSS = !!s( a, "article,aside,figcaption,figure,footer,header,hgroup,main,nav,section{display:block}mark{background:#FF0;color:#000}" );
    }
    e || A( a, b );
    return a
  }

  function B( a )
  {
    for ( var b, c = a.attributes, m = c.length, f = a.ownerDocument.createElement( l + ":" + a.nodeName ); m--; ) {
      b = c[m], b.specified && f.setAttribute( b.nodeName, b.nodeValue );
    }
    f.style.cssText = a.style.cssText;
    return f
  }

  function x( a )
  {
    function b()
    {
      clearTimeout( d._removeSheetTimer );
      c && c.removeNode( !0 );
      c = null
    }

    var c, f, d = n( a ), e = a.namespaces, j = a.parentWindow;
    if ( !C || a.printShived ) {
      return a;
    }
    "undefined" == typeof e[l] && e.add( l );
    j.attachEvent( "onbeforeprint", function ()
    {
      b();
      var g, i, d;
      d = a.styleSheets;
      for ( var e = [], h = d.length, k = Array( h ); h--; ) {
        k[h] = d[h];
      }
      for ( ; d = k.pop(); ) {
        if ( !d.disabled && D.test( d.media ) ) {
          try {
            g = d.imports, i = g.length
          } catch (j) {
            i = 0
          }
          for ( h = 0; h < i; h++ ) {
            k.push( g[h] );
          }
          try {
            e.push( d.cssText )
          } catch (n) {
          }
        }
      }
      g = e.reverse().join( "" ).split( "{" );
      i = g.length;
      h = RegExp( "(^|[\\s,>+~])(" + o().join( "|" ) + ")(?=[[\\s,>+~#.:]|$)", "gi" );
      for ( k = "$1" + l + "\\:$2"; i--; ) {
        e = g[i] = g[i].split( "}" ), e[e.length - 1] = e[e.length - 1].replace( h, k ), g[i] = e.join( "}" );
      }
      e = g.join( "{" );
      i = a.getElementsByTagName( "*" );
      h = i.length;
      k = RegExp( "^(?:" + o().join( "|" ) + ")$", "i" );
      for ( d = []; h--; ) {
        g = i[h], k.test( g.nodeName ) && d.push( g.applyElement( B( g ) ) );
      }
      f = d;
      c = s( a, e )
    } );
    j.attachEvent( "onafterprint", function ()
    {
      for ( var a = f, c = a.length; c--; ) {
        a[c].removeNode();
      }
      clearTimeout( d._removeSheetTimer );
      d._removeSheetTimer = setTimeout( b, 500 )
    } );
    a.printShived = !0;
    return a
  }

  var r = j.html5 || {}, z = /^<|^(?:button|map|select|textarea|object|iframe|option|optgroup)$/i,
    y = /^(?:a|b|code|div|fieldset|h1|h2|h3|h4|h5|h6|i|label|li|ol|p|q|span|strong|style|table|tbody|td|th|tr|ul)$/i, q, u = "_html5shiv", p = 0, t = {}, e;
  (function ()
  {
    try {
      var a = f.createElement( "a" );
      a.innerHTML = "<xyz></xyz>";
      q = "hidden"in a;
      var b;
      if ( !(b = 1 == a.childNodes.length) ) {
        f.createElement( "a" );
        var c = f.createDocumentFragment();
        b = "undefined" == typeof c.cloneNode || "undefined" == typeof c.createDocumentFragment || "undefined" == typeof c.createElement
      }
      e = b
    } catch (d) {
      e = q = !0
    }
  })();
  var d = {elements : r.elements || "abbr article aside audio bdi canvas data datalist details figcaption figure footer header hgroup main mark meter nav output progress section summary time video",
    version         : "3.6.2", shivCSS : !1 !== r.shivCSS, supportsUnknownElements : e, shivMethods : !1 !== r.shivMethods, type : "default", shivDocument : w, createElement : v, createDocumentFragment : function ( a, b )
    {
      a || (a = f);
      if ( e ) {
        return a.createDocumentFragment();
      }
      for ( var b = b || n( a ), c = b.frag.cloneNode(), d = 0, j = o(), l = j.length; d < l; d++ ) {
        c.createElement( j[d] );
      }
      return c
    }};
  j.html5 = d;
  w( f );
  var D = /^$|\b(?:all|print)\b/, l = "html5shiv", C = !e && function ()
  {
    var a = f.documentElement;
    return!("undefined" == typeof f.namespaces || "undefined" == typeof f.parentWindow ||
      "undefined" == typeof a.applyElement || "undefined" == typeof a.removeNode || "undefined" == typeof j.attachEvent)
  }();
  d.type += " print";
  d.shivPrint = x;
  x( f )
})( this, document );