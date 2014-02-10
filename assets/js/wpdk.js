/**
 * WPDK Javascript
 *
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2014-02-08
 * @version            1.2.0
 */

// Stability
if (typeof jQuery === 'undefined') { throw new Error('jQuery is not loaded or missing!') }

// Extends Javascript with a several useful function PHP style
+function ( $ )
{
  "use strict";

  /**
   * Like PHP empty()
   *
   * @since 1.0.0.b3
   *
   * @param {*} mixed_var
   *
   * @return {Boolean}
   */
  if ( typeof( window.empty ) === 'undefined' ) {
    window.empty = function ( mixed_var )
    {
      // Checks if the argument variable is empty
      // undefined, null, false, number 0, empty string,
      // string "0", objects without properties and empty arrays
      // are considered empty
      //
      // http://kevin.vanzonneveld.net
      // +   original by: Philippe Baumann
      // +      input by: Onno Marsman
      // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
      // +      input by: LH
      // +   improved by: Onno Marsman
      // +   improved by: Francesco
      // +   improved by: Marc Jansen
      // +      input by: Stoyan Kyosev (http://www.svest.org/)
      // +   improved by: Rafal Kukawski
      // *     example 1: empty(null);
      // *     returns 1: true
      // *     example 2: empty(undefined);
      // *     returns 2: true
      // *     example 3: empty([]);
      // *     returns 3: true
      // *     example 4: empty({});
      // *     returns 4: true
      // *     example 5: empty({'aFunc' : function () { alert('humpty'); } });
      // *     returns 5: false
      var undef, key, i, len;
      var emptyValues = [undef, null, false, 0, "", "0"];

      for ( i = 0, len = emptyValues.length; i < len; i++ ) {
        if ( mixed_var === emptyValues[i] ) {
          return true;
        }
      }

      if( $.isArray( mixed_var ) ) {
        return !( mixed_var.length > 0 );
      }

      if ( typeof( mixed_var ) === "object" ) {
        for ( key in mixed_var ) {
          // TODO: should we check for own properties only?
          //if (mixed_var.hasOwnProperty(key)) {
          return false;
          //}
        }
        return true;
      }

      return false;
    };
  }

  /**
   * Like PHP isset()
   *
   * @since 1.0.0.b3
   *
   * @return {Boolean}
   */
  if ( typeof( window.isset ) === 'undefined' ) {
    window.isset = function ()
    {
      // http://kevin.vanzonneveld.net
      // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
      // +   improved by: FremyCompany
      // +   improved by: Onno Marsman
      // +   improved by: Rafał Kukawski
      // *     example 1: isset( undefined, true);
      // *     returns 1: false
      // *     example 2: isset( 'Kevin van Zonneveld' );
      // *     returns 2: true
      var l = arguments.length,
        i = 0,
        undef;

      if ( l === 0 ) {
        throw new Error( 'Empty isset' );
      }

      while ( i !== l ) {
        if ( arguments[i] === undef || arguments[i] === null ) {
          return false;
        }
        i++;
      }
      return true;
    };
  }

  /**
   * Do a sprintf()
   *
   * @since 1.0.0.b3
   *
   * @return {*|void}
   */
  if ( typeof( window.sprintf ) === 'undefined' ) {
    window.sprintf = function ()
    {
      // http://kevin.vanzonneveld.net
      // +   original by: Ash Searle (http://hexmen.com/blog/)
      // + namespaced by: Michael White (http://getsprink.com)
      // +    tweaked by: Jack
      // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
      // +      input by: Paulo Freitas
      // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
      // +      input by: Brett Zamir (http://brett-zamir.me)
      // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
      // +   improved by: Dj
      // +   improved by: Allidylls
      // *     example 1: sprintf("%01.2f", 123.1);
      // *     returns 1: 123.10
      // *     example 2: sprintf("[%10s]", 'monkey');
      // *     returns 2: '[    monkey]'
      // *     example 3: sprintf("[%'#10s]", 'monkey');
      // *     returns 3: '[####monkey]'
      // *     example 4: sprintf("%d", 123456789012345);
      // *     returns 4: '123456789012345'
      var regex = /%%|%(\d+\$)?([-+\'#0 ]*)(\*\d+\$|\*|\d+)?(\.(\*\d+\$|\*|\d+))?([scboxXuideEfFgG])/g;
      var a = arguments,
        i = 0,
        format = a[i++];

      // pad()
      var pad = function ( str, len, chr, leftJustify )
      {
        if ( !chr ) {
          chr = ' ';
        }
        var padding = (str.length >= len) ? '' : new Array( 1 + len - str.length >>> 0 ).join( chr );
        return leftJustify ? str + padding : padding + str;
      };

      // justify()
      var justify = function ( value, prefix, leftJustify, minWidth, zeroPad, customPadChar )
      {
        var diff = minWidth - value.length;
        if ( diff > 0 ) {
          if ( leftJustify || !zeroPad ) {
            value = pad( value, minWidth, customPadChar, leftJustify );
          }
          else {
            value = value.slice( 0, prefix.length ) + pad( '', diff, '0', true ) + value.slice( prefix.length );
          }
        }
        return value;
      };

      // formatBaseX()
      var formatBaseX = function ( value, base, prefix, leftJustify, minWidth, precision, zeroPad )
      {
        // Note: casts negative numbers to positive ones
        var number = value >>> 0;
        prefix = prefix && number && {
          '2'  : '0b',
          '8'  : '0',
          '16' : '0x'
        }[base] || '';
        value = prefix + pad( number.toString( base ), precision || 0, '0', false );
        return justify( value, prefix, leftJustify, minWidth, zeroPad );
      };

      // formatString()
      var formatString = function ( value, leftJustify, minWidth, precision, zeroPad, customPadChar )
      {
        if ( precision !== null ) {
          value = value.slice( 0, precision );
        }
        return justify( value, '', leftJustify, minWidth, zeroPad, customPadChar );
      };

      // doFormat()
      var doFormat = function ( substring, valueIndex, flags, minWidth, _, precision, type )
      {
        var number;
        var prefix;
        var method;
        var textTransform;
        var value;

        if ( substring == '%%' ) {
          return '%';
        }

        // parse flags
        var leftJustify = false,
          positivePrefix = '',
          zeroPad = false,
          prefixBaseX = false,
          customPadChar = ' ';
        var flagsl = flags.length;
        for ( var j = 0; flags && j < flagsl; j++ ) {
          switch ( flags.charAt( j ) ) {
            case ' ':
              positivePrefix = ' ';
              break;
            case '+':
              positivePrefix = '+';
              break;
            case '-':
              leftJustify = true;
              break;
            case "'":
              customPadChar = flags.charAt( j + 1 );
              break;
            case '0':
              zeroPad = true;
              break;
            case '#':
              prefixBaseX = true;
              break;
          }
        }

        // parameters may be null, undefined, empty-string or real valued
        // we want to ignore null, undefined and empty-string values
        if ( !minWidth ) {
          minWidth = 0;
        }
        else if ( minWidth == '*' ) {
          minWidth = +a[i++];
        }
        else if ( minWidth.charAt( 0 ) == '*' ) {
          minWidth = +a[minWidth.slice( 1, -1 )];
        }
        else {
          minWidth = +minWidth;
        }

        // Note: undocumented perl feature:
        if ( minWidth < 0 ) {
          minWidth = -minWidth;
          leftJustify = true;
        }

        if ( !isFinite( minWidth ) ) {
          throw new Error( 'sprintf: (minimum-)width must be finite' );
        }

        if ( !precision ) {
          precision = 'fFeE'.indexOf( type ) > -1 ? 6 : (type == 'd') ? 0 : undefined;
        }
        else if ( precision == '*' ) {
          precision = +a[i++];
        }
        else if ( precision.charAt( 0 ) == '*' ) {
          precision = +a[precision.slice( 1, -1 )];
        }
        else {
          precision = +precision;
        }

        // grab value using valueIndex if required?
        value = valueIndex ? a[valueIndex.slice( 0, -1 )] : a[i++];

        switch ( type ) {
          case 's':
            return formatString( String( value ), leftJustify, minWidth, precision, zeroPad, customPadChar );
          case 'c':
            return formatString( String.fromCharCode( +value ), leftJustify, minWidth, precision, zeroPad );
          case 'b':
            return formatBaseX( value, 2, prefixBaseX, leftJustify, minWidth, precision, zeroPad );
          case 'o':
            return formatBaseX( value, 8, prefixBaseX, leftJustify, minWidth, precision, zeroPad );
          case 'x':
            return formatBaseX( value, 16, prefixBaseX, leftJustify, minWidth, precision, zeroPad );
          case 'X':
            return formatBaseX( value, 16, prefixBaseX, leftJustify, minWidth, precision, zeroPad ).toUpperCase();
          case 'u':
            return formatBaseX( value, 10, prefixBaseX, leftJustify, minWidth, precision, zeroPad );
          case 'i':
          case 'd':
            number = +value || 0;
            number = Math.round( number - number % 1 ); // Plain Math.round doesn't just truncate
            prefix = number < 0 ? '-' : positivePrefix;
            value = prefix + pad( String( Math.abs( number ) ), precision, '0', false );
            return justify( value, prefix, leftJustify, minWidth, zeroPad );
          case 'e':
          case 'E':
          case 'f': // Should handle locales (as per setlocale)
          case 'F':
          case 'g':
          case 'G':
            number = +value;
            prefix = number < 0 ? '-' : positivePrefix;
            method = ['toExponential', 'toFixed', 'toPrecision']['efg'.indexOf( type.toLowerCase() )];
            textTransform = ['toString', 'toUpperCase']['eEfFgG'.indexOf( type ) % 2];
            value = prefix + Math.abs( number )[method]( precision );
            return justify( value, prefix, leftJustify, minWidth, zeroPad )[textTransform]();
          default:
            return substring;
        }
      };

      return format.replace( regex, doFormat );
    };
  }

  /**
   * Porting of PHP join function
   *
   * @param {string} glue
   * @param {array} pieces
   * @return {*}
   */
  if ( typeof( window.join ) === 'undefined' ) {
    window.join = function ( glue, pieces )
    {
      // http://kevin.vanzonneveld.net
      // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
      // -    depends on: implode
      // *     example 1: join(' ', ['Kevin', 'van', 'Zonneveld']);
      // *     returns 1: 'Kevin van Zonneveld'
      return implode( glue, pieces );
    };
  }

  /**
   * Porting of PHP implode
   *
   * @param {string} glue
   * @param {array} pieces
   * @return {*}
   */
  if ( typeof( window.implode ) === 'undefined' ) {
    window.implode = function ( glue, pieces )
    {
      // http://kevin.vanzonneveld.net
      // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
      // +   improved by: Waldo Malqui Silva
      // +   improved by: Itsacon (http://www.itsacon.net/)
      // +   bugfixed by: Brett Zamir (http://brett-zamir.me)
      // *     example 1: implode(' ', ['Kevin', 'van', 'Zonneveld']);
      // *     returns 1: 'Kevin van Zonneveld'
      // *     example 2: implode(' ', {first:'Kevin', last: 'van Zonneveld'});
      // *     returns 2: 'Kevin van Zonneveld'
      var i = '',
        retVal = '',
        tGlue = '';
      if ( arguments.length === 1 ) {
        pieces = glue;
        glue = '';
      }
      if ( typeof(pieces) === 'object' ) {
        if ( Object.prototype.toString.call( pieces ) === '[object Array]' ) {
          return pieces.join( glue );
        }
        for ( i in pieces ) {
          retVal += tGlue + pieces[i];
          tGlue = glue;
        }
        return retVal;
      }
      return pieces;
    };
  }

  /**
   * Return TRUE if the string NOT contains '', 'false', '0', 'no', 'n', 'off', null.
   *
   * @since 1.0.0.b4
   *
   * @note This is a porting of homonymous php function to check if a param is TRUE.
   *
   * @param {*} mixed
   *
   * @return {Boolean}
   */
  if ( typeof( window.wpdk_is_bool ) === 'undefined' ) {
    window.wpdk_is_bool = function ( mixed )
    {
      var undef, i, len;
      var emptyValues = [undef, null, false, 0, "", "0", 'n', 'no', 'off', 'false'];
      for ( i = 0, len = emptyValues.length; i < len; i++ ) {
        if ( mixed === emptyValues[i] || mixed.toLowerCase() == emptyValues[i] ) {
          return false;
        }
      }
      return true;
    };
  }

  /**
   * jQuery Cookie Plugin v1.3.1
   * https://github.com/carhartl/jquery-cookie
   *
   * Copyright 2013 Klaus Hartl
   * Released under the MIT license
   */
  (function ( factory ) { if ( typeof define === 'function' && define.amd ) {
      // AMD. Register as anonymous module.
      define( ['jquery'], factory );
    }
    else {
      // Browser globals.
      factory( jQuery );
    }
  }( function ( $ ) {

    var pluses = /\+/g;

    function raw( s )
    {
      return s;
    }

    function decoded( s )
    {
      return decodeURIComponent( s.replace( pluses, ' ' ) );
    }

    function converted( s )
    {
      if ( s.indexOf( '"' ) === 0 ) {
        // This is a quoted cookie as according to RFC2068, unescape
        s = s.slice( 1, -1 ).replace( /\\"/g, '"' ).replace( /\\\\/g, '\\' );
      }
      try {
        return config.json ? JSON.parse( s ) : s;
      } catch (er) {
      }
    }

    var config = $.cookie = function ( key, value, options )
    {

      // write
      if ( value !== undefined ) {
        options = $.extend( {}, config.defaults, options );

        if ( typeof options.expires === 'number' ) {
          var days = options.expires, t = options.expires = new Date();
          t.setDate( t.getDate() + days );
        }

        value = config.json ? JSON.stringify( value ) : String( value );

        return (document.cookie = [
          config.raw ? key : encodeURIComponent( key ),
          '=',
          config.raw ? value : encodeURIComponent( value ),
          options.expires ? '; expires=' + options.expires.toUTCString() : '', // use expires attribute, max-age is not supported by IE
          options.path ? '; path=' + options.path : '',
          options.domain ? '; domain=' + options.domain : '',
          options.secure ? '; secure' : ''
        ].join( '' ));
      }

      // read
      var decode = config.raw ? raw : decoded;
      var cookies = document.cookie.split( '; ' );
      var result = key ? undefined : {};
      for ( var i = 0, l = cookies.length; i < l; i++ ) {
        var parts = cookies[i].split( '=' );
        var name = decode( parts.shift() );
        var cookie = decode( parts.join( '=' ) );

        if ( key && key === name ) {
          result = converted( cookie );
          break;
        }

        if ( !key ) {
          result[name] = converted( cookie );
        }
      }

      return result;
    };

    config.defaults = {};

    $.removeCookie = function ( key, options )
    {
      if ( $.cookie( key ) !== undefined ) {
        // Must not alter options, thus extending a fresh object...
        $.cookie( key, '', $.extend( {}, options, { expires : -1 } ) );
        return true;
      }
      return false;
    };

  } ));


  /**
   * WPDK Swipe Control extend.
   * The swipe() method get/set the current state for a swipe control
   *
   * @internal {string} Optional. Set the current state
   *
   * @returns {string} Return the current state
   */
  if( typeof( $.fn.swipe ) === 'undefined' ) {
    $.fn.swipe = function () {

      if ( $( this ).hasClass( 'wpdk-form-swipe' ) ) {
        /* Get main control. */
        var control = $( this );

        /* Get sub-elements. */
        var knob = control.children( 'span' ).eq( 0 );
        var input = control.children( 'input[type=hidden]' ).eq( 0 );

        /* Set. */
        if ( arguments.length > 0 ) {

          var v = arguments[0], result;

          /* On. */
          if ( 'on' === v ) {
            result = control.trigger( 'change', [ control, 'on'] );
            if ( false !== result ) {
              input.val( 'on' );
              knob.animate( { marginLeft : '23px' }, 100, function ()
              {
                control.addClass( 'wpdk-form-swipe-on' );
              } );
              control.trigger( 'changed', [ control, 'on'] );
            }
          }

          /* Off. */
          else {
            result = control.trigger( 'change', [ control, 'off'] );
            if ( false !== result ) {
              input.val( 'off' );
              knob.animate( { marginLeft : '0' }, 100, function ()
              {
                control.removeClass( 'wpdk-form-swipe-on' );
              } );
              control.trigger( 'changed', [ control, 'off'] );
            }
          }
        }

        /* Return always the state */
        return input.val();
      }
    };
  }

  /**
   * Manage the Glyph Icon
   */
  if ( 'undefined' === typeof( window.WPDKGlyphIcons ) ) {
    window.WPDKGlyphIcons = (function ()
    {
      var $t = {
        version         : '1.0.2',
        display         : _display,
        html            : _html,

        /* Glyph constants */
        ANGLE_DOWN      : 'wpdk-icon-angle-down',
        UPDOWN_CIRCLE   : 'wpdk-icon-updown-circle',
        ANGLE_LEFT      : 'wpdk-icon-angle-left',
        ANGLE_RIGHT     : 'wpdk-icon-angle-right',
        ANGLE_UP        : 'wpdk-icon-angle-up',
        ARROWS_CW       : 'wpdk-icon-arrows-cw',
        ATTENTION       : 'wpdk-icon-attention',
        BUG             : 'wpdk-icon-bug',
        CANCEL_CIRCLED2 : 'wpdk-icon-cancel-circled2',
        CCW             : 'wpdk-icon-ccw',
        CHAT            : 'wpdk-icon-chat',
        CLOCK           : 'wpdk-icon-clock-1',
        COMMENT_EMPTY   : 'wpdk-icon-comment-empty',
        CW              : 'wpdk-icon-cw',
        DOWN_BIG        : 'wpdk-icon-down-big',
        DOWN_OPEN       : 'wpdk-icon-down-open',
        EMO_COFFEE      : 'wpdk-icon-emo-coffee',
        EXPORT          : 'wpdk-icon-export',
        GITHUB          : 'wpdk-icon-github',
        HEART           : 'wpdk-icon-heart',
        HEART_EMPTY     : 'wpdk-icon-heart-empty',
        LEFT_OPEN       : 'wpdk-icon-left-open',
        LOCK            : 'wpdk-icon-lock',
        LOCK_OPEN       : 'wpdk-icon-lock-open',
        LOCK_OPEN_ALT   : 'wpdk-icon-lock-open-alt',
        MAIL            : 'wpdk-icon-mail',
        MINUS_SQUARED   : 'wpdk-icon-minus-squared',
        OK              : 'wpdk-icon-ok',
        OK_CIRCLED      : 'wpdk-icon-ok-circled',
        PENCIL          : 'wpdk-icon-pencil',
        PLUS_SQUARED    : 'wpdk-icon-plus-squared',
        RIGHT_OPEN      : 'wpdk-icon-right-open',
        SEARCH          : 'wpdk-icon-search',
        SPIN1           : 'wpdk-icon-spin1 animate-spin',
        SPIN2           : 'wpdk-icon-spin2 animate-spin',
        SPIN3           : 'wpdk-icon-spin3 animate-spin',
        SPIN4           : 'wpdk-icon-spin4 animate-spin',
        SPIN5           : 'wpdk-icon-spin5 animate-spin',
        SPIN6           : 'wpdk-icon-spin6 animate-spin',
        STAR            : 'wpdk-icon-star',
        STAR_EMPTY      : 'wpdk-icon-star-empty',
        STAR_HALF       : 'wpdk-icon-star-half',
        STAR_HALF_ALT   : 'wpdk-icon-star-half-alt',
        TRASH           : 'wpdk-icon-trash',
        UP_OPEN         : 'wpdk-icon-up-open',

        /* Since 1.4.5 */
        EMO_HAPPY       : 'wpdk-icon-emo-happy',
        EMO_UNHAPPY     : 'wpdk-icon-emo-unhappy',
        CANCEL_CIRCLED  : 'wpdk-icon-cancel-circled',
        THUMBS_UP_ALT   : 'wpdk-icon-thumbs-up-alt',
        THUMBS_DOWN_ALT : 'wpdk-icon-thumbs-down-alt',
        THUMBS_UP       : 'wpdk-icon-thumbs-up',
        THUMBS_DOWN     : 'wpdk-icon-thumbs-down',
        COG             : 'wpdk-icon-cog',
        UP_BIG          : 'wpdk-icon-up-big',
        LEFT_BIG        : 'wpdk-icon-left-big',
        RIGHT_BIG       : 'wpdk-icon-right-big',
        OFF             : 'wpdk-icon-off',
        FACEBOOK        : 'wpdk-icon-facebook',
        APPLE           : 'wpdk-icon-apple',
        TWITTER         : 'wpdk-icon-twitter',

        /* since 1.4.7 */
        GOOGLE_PLUS     : 'wpdk-icon-gplus'

      };

      /**
       * Return the HTML markup for glyph icon
       *
       * @param {string} glypho
       * @param {string} size Optional. Default = ''
       * @param {string} color Optional. Default = ''
       * @param {string} tag Optional. Default = 'i'
       *
       * @returns {string}
       * @private
       */
      function _display( glypho, size, color, tag )
      {
        document.write( _html( glypho, size, color, tag ) );
      }

      /**
       * Return the HTML markup for glyph icon
       *
       * @param {string} glypho
       * @param {string} size Optional. Default = ''
       * @param {string} color Optional. Default = ''
       * @param {string} tag Optional. Default = 'i'
       *
       * @returns {string}
       * @private
       */
      function _html( glypho, size, color, tag )
      {
        var d = {
          size  : size || '',
          color : color || '',
          tag   : tag || 'i'
        };

        var stack = [], style = '';

        if ( !empty( d.size ) ) {
          stack.push( sprintf( 'font-size:%s', d.size ) );
        }

        if ( !empty( d.color ) ) {
          stack.push( sprintf( 'color:%s', d.color ) );
        }

        if ( !empty( stack ) ) {
          style = sprintf( 'style="%s"', implode( ';', stack ) );
        }

        return sprintf( '<%s %s class="%s"></%s>', d.tag, style, glypho, d.tag );
      }

      return $t;

    })();
  }

}( jQuery );


// On document ready
jQuery( function ( $ )
{
  "use strict";

  /**
   * This class manage all forms controles and fields, attach new event and perform special actions.
   *
   * @class           WPDKControls
   * @author          =undo= <info@wpxtre.me>
   * @copyright       Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
   * @date            2013-12-04
   * @version         1.0.3
   *
   */
  if ( typeof( window.WPDKControls ) === 'undefined' ) {
    window.WPDKControls = (function () {

      /**
       * Empty object
       */
      var $t = {
        version           : '1.0.3',
        init              : _init,
        configurationForm : _configurationForm,
        preferencesForm   : _preferencesForm
      };

      /**
       * Init
       *
       * @returns {{version: string, init: _init, configurationForm: null, preferencesForm: null}}
       * @private
       */
      function _init ()
      {
        _initClearInput();
        _initSwipeControl();
        _initScrollableControl();
        _initLockedControl();
        _initAccordion();
        _initBehavior();
        _initGuide();

        return $t;
      }

      /**
       * Return the standard form for a configuration view. See wpdk-ui.php for more detail.
       *
       * @since 0.9.5
       * @deprecated since 1.2.0 use preferencesForm() instead
       *
       * @param {string} id Configuration view ID
       * @return {*|jQuery|HTMLElement}
       */
      function _configurationForm ( id )
      {
        return $( 'form#wpdk_configuration_view_form-' + id );
      }

      /**
       * Return the standard form for a preferences view. See wpdk-ui.php for more detail.
       *
       * @since 1.2.0
       *
       * @param {string} id Preferences form ID
       *
       * @return {*|jQuery|HTMLElement}
       */
      function _preferencesForm( id )
      {
        return $( 'form#wpdk_preferences_view_form-' + id );
      };

      /**
       * Init the guide engine to open a modal twitter window with an iframe to developer center
       *
       * @since 1.0.0.b4
       * @todo This engine will be move into wpXtreme
       *
       * @private
       */
      function _initGuide()
      {
        $( 'a.wpdk-guide' ).click( function ()
        {
          var title, content, url;

          // Set guide title
          title = $( this ).data( 'title' );

          // If guide content is directly into data-content attribute
          if ( typeof $( this ).data( 'content' ) != 'undefined' && $( this ).data( 'content' ).length > 0 ) {
            content = $( this ).data( 'content' );
          }
          // If guide is in Developer Center
          else {
            url = sprintf( 'https://developer.wpxtre.me/api/v1/articles/%s', $( this ).attr( 'href' ) );
            content = sprintf( '<iframe class="wpdk-iframe-guide" frameborder="0" height="520" width="530" src="%s"></iframe>', url );
          }

          var modal = new WPDKTwitterBootstrapModal( 'wpdk-guide', title, content );
          modal.height = 512;
          modal.display();

          return false;
        } );
      }

      /**
       * When an input field has the clear right button, on click the previous left input is cleaned.
       *
       * @private
       */
      function _initClearInput()
      {
        $( document ).on( 'click', 'span.wpdk-form-clear-left', false, function ()
        {
          $( this ).find( 'input' ).val( '' ).trigger( 'change' ).trigger( 'keyup' );
        } );
      }

      /**
       * Initialize the WPDK custom Swipe Control.
       * When a Swipe Control is clicked (or swipe) an event `swipe` is trigged:
       *
       *     $('.wpdk-form-swipe').on('swipe', function(a, swipeButton, status ) {});
       *
       * @private
       */
      function _initSwipeControl()
      {
        $( document ).on( 'click', 'span.wpdk-form-swipe span', false, function ()
        {
          var control = $( this ).parent();
          var status = wpdk_is_bool( control.swipe() );
          var enabled = status ? 'off' : 'on';
          var result = control.trigger( 'swipe', [ control, enabled] );
          if ( typeof result == 'undefined' || result ) {
            control.swipe( enabled );
          }
        } );
      }

      /**
       * Initialize the scrollale control
       *
       * @since 1.4.7
       * @since 1.4.7
       * @private
       */
      function _initScrollableControl()
      {
        var scrollable = $( '.wpdk-form-scrollable' );
        if ( scrollable.length ) {
          $( document ).on( 'click', '.wpdk-form-scrollable img', false, function ()
          {
            $( this ).toggleClass( 'wpdk-selected' );
          } );
        }
      }

      /**
       * A Locked Control is a special input text when a lock to avoid change unwanted.
       * When you try to edit this field a Javascript confirm is displayed. You have to confirm the edit to next.
       *
       * @private
       */
      function _initLockedControl()
      {
        $( document ).on( 'click', '.wpdk-form-locked', false, function ()
        {
          if ( confirm( wpdk_i18n.messageUnLockField ) ) {
            $( this )
              .attr( 'class', 'wpdk-form-unlocked' )
              .prev( 'input' )
              .removeAttr( 'readonly' );
          }
        } );
      }

      /**
       * Initialize the wpdk accordion
       *
       * @private
       */
      function _initAccordion()
      {
        var accordion = $( 'i.wpdk-openclose-accordion' );
        if ( accordion.length ) {
          /* Memorizzo altezza */
          accordion.parent().next( 'div.wpdk-accordion' ).each( function ( i, e )
          {
            $( this ).addClass( 'wpdk-accordion-open' ).data( 'height', $( this ).height() );
            if ( i > 0 ) {
              $( this ).removeClass( 'wpdk-accordion-open' );
            }
            else {
              $( e ).css( 'height', $( e ).data( 'height' ) + 'px' );
            }
          } );

          accordion.click( function ()
          {
            /* Chiudo tutti gli altri - solo i fieldsset del form parent */
            var form = $( this ).parents( 'form' );
            form.find( 'fieldset' ).removeClass( 'wpdk-accordion-open' );
            form.find( 'fieldset div.wpdk-accordion' ).css( 'height', '0' );

            /* Me stesso lo apro */
            $( this ).parents( 'fieldset' ).addClass( 'wpdk-accordion-open' );

            /* Animation su container */
            var $container = $( this ).parent().next( 'div.wpdk-accordion' );
            $container.css( 'height', $container.data( 'height' ) + 'px' );

          } );
        }
      }

      /**
       * Initialize special behaviors as auto disabled after click, and more.
       *
       * @private
       */
      function _initBehavior()
      {

        /* Disabled after click for input. */
        $( 'input.wpdk-disable-after-click' ).click( function ()
        {
          $( this ).addClass( 'disabled' );
        } );
      }

      return $t;

    })();
  }

  /**
   * This class manage all jQuery enhancer
   *
   * @class           WPDKjQuery
   * @author          =undo= <info@wpxtre.me>
   * @copyright       Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
   * @date            2013-08-23
   * @version         1.1.1
   */
  if( typeof( window.WPDKjQuery ) === 'undefined' ) {
    window.WPDKjQuery = (function () {

    /**
     *
     * @type {{}}
     */
    var $t = {};

    /**
     * Version
     *
     * @type {string}
     */
    $t.version = "1.1.0";

    /**
     * Return the jQury version.
     *
     * @return {string}
     */
    $t.jQueryVersion = function ()
    {
      return $().jquery;
    };

    /**
     * Return the jQury UI version. Return false if jQuery UI is not loaded
     *
     * @returns {string|boolean}
     */
    $t.jQueryUIVersion = function ()
    {
      if ( $.ui && $.ui.version ) {
        return $.ui.version;
      }
      return false;
    };

    /**
     * Init this class
     */
    $t.init = function ()
    {
      _initDatePicker();
      _initTabs();
      __initAutocomplete();
      _initAutocomplete();
      _initCopyPaste();

      /* Wrap the date picker with my pwn class. */
      $( '#ui-datepicker-div' ).wrap( '<div class="wpdk-jquery-ui"/>' );

      return $t;
    };

    /**
     * Initialize the Date Picker
     *
     * @private
     */
    function _initDatePicker()
    {

      /* Enable Date Picker on wpdk input class. */
      $( 'input.wpdk-form-date' ).datepicker();

      /* Locale */
      if ( $().datetimepicker ) {
        $( 'input.wpdk-form-datetime:visible' ).datetimepicker( {
          timeOnlyTitle : wpdk_i18n.timeOnlyTitle,
          timeText      : wpdk_i18n.timeText,
          hourText      : wpdk_i18n.hourText,
          minuteText    : wpdk_i18n.minuteText,
          secondText    : wpdk_i18n.secondText,
          currentText   : wpdk_i18n.currentText,
          dayNamesMin   : (wpdk_i18n.dayNamesMin).split( ',' ),
          monthNames    : (wpdk_i18n.monthNames).split( ',' ),
          closeText     : wpdk_i18n.closeText,
          timeFormat    : wpdk_i18n.timeFormat,
          dateFormat    : wpdk_i18n.dateFormat
        } );
      }
      else {
        if ( typeof window.console !== 'undefined' ) {
          alert( 'Date Time Picker not loaded' );
        }
      }

      /* Date Picker defaults */
      $.datepicker.setDefaults( {
        changeMonth     : true,
        changeYear      : true,
        dayNamesMin     : (wpdk_i18n.dayNamesMin).split( ',' ),
        monthNames      : (wpdk_i18n.monthNames).split( ',' ),
        monthNamesShort : (wpdk_i18n.monthNamesShort).split( ',' ),
        dateFormat      : wpdk_i18n.dateFormat
      } );
    }

    /**
     * Initialize the jQuery Tabs with special cookie for remember the open tab.
     *
     * @private
     */
    function _initTabs()
    {
      var tabs = $( ".wpdk-tabs" );
      tabs.tabs();

      if ( document.location.href.indexOf( '#' ) > 0 ) {
      }
      else {
        tabs.each( function ()
        {
          var id = $( this ).attr( "id" );
          if ( 'undefined' !== typeof(id) ) {
            $( this ).tabs( {
              activate : function ( e, ui )
              {
                $.cookie( id, ui.newTab.index(), { path : '/' } );
              },
              active   : $.cookie( id )
            } );
          }
        } );
      }
    }

    /**
     * Attach an autocomplete Ajax event when an input has the `data-autocomplete_action` attribute.
     * Usually you will use an input text. When you digit smething an Ajax call is made with action get from
     * `autocomplete_action` attribute.
     *
     * @deprecated Since 1.0.0.b4
     *
     * @private
     */
    function __initAutocomplete()
    {
      $( 'input[data-autocomplete_action]' ).each( function ( index, element )
      {
        $( element ).autocomplete(
          {
            source    : function ( request, response )
            {
              $.post( wpdk_i18n.ajaxURL,
                {
                  action          : $( element ).data( 'autocomplete_action' ),
                  autocomplete_id : $( element ).data( 'autocomplete_id' ),
                  data            : $( element ).data( 'user_data' ),
                  term            : request.term
                },
                function ( data )
                {
                  response( $.parseJSON( data ) );
                } );
            },
            select    : function ( event, ui )
            {
              if ( typeof ui.item.href !== 'undefined' ) {
                document.location = ui.item.href;
              }
              else {
                var $name = $( element ).data( 'autocomplete_target' );
                $( 'input[name=' + $name + ']' ).val( ui.item.id );
              }
            },
            minLength : $( element ).data( 'autocomplete_min_length' ) | 0
          }
        );
      } );
    }

    /**
     * Select all input with data-autocomplete attribute and init the right autocomplete subset
     *
     * @private
     */
    function _initAutocomplete()
    {
      $( 'input[data-autocomplete]' ).each( function ( index, element )
      {
        switch ( $( element ).data( 'autocomplete' ) ) {
          case 'posts':
            _initAutocompletePosts( element );
            break;

          case 'embed':
          case 'inline':
            _initAutocompleteEmbed( element );
            break;

          case 'custom':
            _initAutocompleteCustom( element );
            break;
        }
      } );
    }

    /**
     * Attach an autocomplete Ajax event when an input has the `data-autocomplete_posts` attribute.
     * Usually you will use an input text. When you digit something an Ajax call 'wpdk_action_autocomplete_posts' is made.
     *
     * @param element DOM element
     *
     * @private
     */
    function _initAutocompletePosts( element )
    {

      /* Init. */
      $( element ).autocomplete(
        {
          source    : function ( request, response )
          {
            $.post( wpdk_i18n.ajaxURL,
              {
                action      : 'wpdk_action_autocomplete_posts',
                post_type   : function ()
                {
                  var post_type = '';
                  if ( $( $( element ).data( 'post_type' ) ).length ) {
                    post_type = $( $( element ).data( 'post_type' ) ).val();
                  }
                  /* The data attribute post type contains the post type id. */
                  else {
                    post_type = $( element ).data( 'post_type' );
                  }
                  return post_type;
                },
                post_status : $( element ).data( 'post_status' ),
                limit       : $( element ).data( 'limit' ),
                order       : $( element ).data( 'order' ),
                orderby     : $( element ).data( 'orderby' ),
                term        : request.term
              },
              function ( data )
              {
                response( $.parseJSON( data ) );
              } );
          },
          select    : function ( event, ui )
          {
            if ( typeof ui.item.href !== 'undefined' ) {
              document.location = ui.item.href;
            }
            else {
              var $name = $( element ).data( 'target' );
              if ( !empty( $name ) ) {
                $( 'input[name=' + $name + ']' ).val( ui.item.id );
              }
            }
          },
          minLength : $( element ).data( 'min_length' ) | 0
        }
      );
    }

    /**
     * Init an autocomplete with a jSON array embed (inner) into the element
     *
     * @param element DOM element
     * @private
     */
    function _initAutocompleteEmbed( element )
    {
      var source = $( element ).data( 'source' );

      if ( !empty( source ) ) {

        source = $.parseJSON( $( element ).data( 'source' ).replace( /'/g, "\"" ) );
        $( element ).autocomplete(
          {
            source    : source,
            minLength : $( element ).data( 'min_length' ) | 0
          }
        );
      }
    }

    /**
     * Init an autocomplete with a jSON array embed (inner) into the element
     *
     * @param element DOM element
     * @private
     */
    function _initAutocompleteCustom( element )
    {
      var source = $.parseJSON( $( element ).data( 'source' ).replace( /'/g, "\"" ) );
      var callable = $( element ).data( 'function' );
      var select = $( element ).data( 'select' );

      $( element ).autocomplete(
        {
          source    : source,
          minLength : $( element ).data( 'min_length' ) | 0
        }
      ).data( "ui-autocomplete" )._renderItem = eval( callable );
    }

    /**
     * The Copy and Paste engine allow to copy a value from a source input to a target input.
     *
     * @private
     */
    function _initCopyPaste()
    {

      /* This is a hack to send in POST/GET the new value in the multiple select tag. */
      $( 'form' ).submit( function ()
      {
        $( '[data-paste]' ).each( function ()
        {
          var paste = $( '#' + $( this ).attr( 'data-paste' ) );
          var element_paste_type = paste.get( 0 ).tagName;
          if ( element_paste_type.toLowerCase() == 'select' && paste.attr( 'multiple' ) !== 'undefined' ) {
            paste.find( 'option' ).attr( 'selected', 'selected' );
          }
        } );
      } );

      /* Copy & Paste */
      $( document ).on( 'click', '.wpdk-form-button-copy-paste', false, function ()
      {
        /* Recupero options */
        var options = $( this ).attr( 'data-options' ) ? $( this ).attr( 'data-options' ).split( ' ' ) : [];

        /* @todo Aggiungere evento/filtro */

        var copy = $( '#' + $( this ).attr( 'data-copy' ) );
        var paste = $( '#' + $( this ).attr( 'data-paste' ) );

        /* Determino da dove copio e dove incollo */
        var element_copy_type = copy.get( 0 ).tagName;

        var value, text;

        switch ( element_copy_type.toLowerCase() ) {
          case 'input':
            value = copy.val();
            text = value;
            if ( $.inArray( 'clear_after_copy', options ) !== false ) {
              copy.val( '' );
            }
            break;
          case 'select':
            value = $( 'option:selected', copy ).val();
            text = $( 'option:selected', copy ).text();
            break;
        }

        if ( value != '' || value != '' ) {

          /* Determino dove devo incollare */
          var element_paste_type = paste.get( 0 ).tagName;

          switch ( element_paste_type.toLowerCase() ) {
            case 'select':
              paste.append( '<option class="wpdk-form-option" value="' + value + '">' + text + '</option>' );
              break;
          }
        }
      } );

      /* Remove. */
      $( document ).on( 'click', '.wpdk-form-button-remove', false, function ()
      {
        var remove_from = $( this ).attr( 'data-remove_from' );
        $( 'option:selected', '#' + remove_from ).remove();
      } );
    }

    return $t;

  })();
  }

  /**
   * Utility to manage the php WPDKAjaxResponse
   *
   * @class           WPDKAjaxResponse
   * @author          =undo= <info@wpxtre.me>
   * @copyright       Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
   * @date            2013-11-19
   * @version         1.0.2
   * @since           1.4.0
   *
   * @param {string} response JSON response
   * @constructor
   */
  if ( typeof( window.WPDKAjaxResponse ) === 'undefined' ) {
    window.WPDKAjaxResponse = function ( response ) {
      /**
       * Resolve conflict
       *
       * @type {jQuery}
       */
      var $ = window.jQuery;

      this.version = '1.0.2';
      this.error = '';
      this.message = '';
      this.data = '';

      // Init properties

      if ( isset( response.error ) && !empty( response.error ) ) {
        this.error = response.error.replace( /\\n/g, "\n" );
      }

      if ( isset( response.message ) && !empty( response.message ) ) {
        this.message = response.message.replace( /\\n/g, "\n" );
      }

      if ( isset( response.data ) && !empty( response.data ) ) {
        this.data = response.data;
      }

    };
  }

  /**
   * This is a little Javascript framework to improve the UI and checking control specially in the form management.
   *
   * @class           WPDK
   * @author          =undo= <info@wpxtre.me>
   * @copyright       Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
   * @date            2013-12-19
   * @version         0.9.7
   *
   */
  if ( typeof( window.WPDK ) === 'undefined' ) {
    window.WPDK = (function () {

      /**
       * Internal class pointer
       */
      var $t = {
        version        : '0.9.7',
        init           : _init,
        refresh        : _refresh,
        loading        : _loading,
        reloadDocument : _reloadDocument
      };

      /**
       * Initialize all Javascript hook.
       * If you modify the DOM you can call this method to refresh hooks.
       */
      function _init ()
      {
        _hackMenu();
        WPDKjQuery.init();
        WPDKControls.init();

        return $t;
      };

      /**
       * See WPDK.init();
       *
       * @deprecated Use WPDK.init() instead
       */
      function _refresh ()
      {
        $t.init();
      };

      /**
       * Enabled/Disabled loading on the screen top most
       *
       * @param status True to display loading on top most, False to remove
       *
       */
      function _loading( status )
      {
        if ( true === status ) {
          $( '<div />' ).addClass( 'wpdk-loader' ).appendTo( 'body' ).fadeIn( 500 );
        }
        else {
          $( 'div.wpdk-loader' ).fadeOut( function () { $( this ).remove() } );
        }
      };

      /**
       * Reload current document with clear and waiting effects
       *
       * @since 1.0.0.b3
       *
       * @param {bool} Optional. FALSE to avoid mask
       */
      function _reloadDocument ()
      {
        if ( 0 == arguments.length ) {
          $( '<div id="wpdk-mask" />' ).appendTo( 'body' );
        }
        document.location = document.location.href;
      };

      /**
       * Remove the A tag to create a separator item for wpXtreme menu.
       *
       * @private
       */
      function _hackMenu()
      {
        $( 'ul#adminmenu .wp-submenu a[href*=wpdk_menu_divider]' ).each( function ()
        {
          var content = $( this ).html();
          $( this )
            .parent()
            .replaceWith( '<li class="wpdk_menu_divider">' + content + '</li>' );

          $( '#colors-css' ).load( _hackColorMenu );

          _hackColorMenu();

        } );
      }

      /**
       * Invert color menu in accordion with color.css
       *
       * @since 1.4.8
       *
       * @private
       */
      function _hackColorMenu()
      {
        var background_color = $( '#adminmenu' ).css( 'background-color' );
        if ( 'transparent' == background_color ) {
          return;
        }
        var invert_color = _invertColor( background_color );
        $( '.wpdk_menu_divider' ).css( { 'border-color' : invert_color, 'color' : invert_color } );
      }

      /**
       * Invert a rgb color
       *
       * @since 1.4.8
       *
       * @param rgbString
       * @returns {string}
       * @private
       */
      function _invertColor( rgbString )
      {
        var parts = rgbString.match( /^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/ );

        if ( !parts || null === parts ) {
          return 'rgb(0,0,0)';
        }

        parts.splice( 0, 1 );
        for ( var i = 1; i < 3; ++i ) {
          parts[i] = parseInt( parts[i], 10 );
        }
        var rgb = 'rgb(';
        $.each( parts, function ( idx, item )
        {
          rgb += (255 - item) + ',';
        } );
        rgb = rgb.slice( 0, -1 );
        rgb += ')';
        return rgb;
      }

      return $t.init();

    })();
  }

  /**
   * Write a cookie to debug the javascript library versions
   * Use this cookie from PHP for debug.
   */
  +function writeCookieVersion() {
    var cookie = [],
      version,
      versions =
    {
      'jQuery'                    : WPDKjQuery.jQueryVersion(),
      'jQuery UI'                 : WPDKjQuery.jQueryUIVersion(),
      'WPDK'                      : WPDK.version
    };

    for ( version in versions ) {
      cookie.push( sprintf( '"%s":"v.%s"', version, versions[version] ) );
    }

    var json = sprintf( '{%s}', cookie.join(',') );

    $.cookie( 'wpdk_javascript_library_versions', json, { path : '/' } );
  }();

  // Fire when WPDK is loaded
  $( document ).trigger( 'WPDK' );

});