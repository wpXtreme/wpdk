/**
 * WPDK (core) Javascript
 *
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date               2014-02-15
 * @version            1.2.1
 */

// Stability
if (typeof jQuery === 'undefined') { throw new Error('jQuery is not loaded or missing!') }

// ---------------------------------------------------------------------------------------------------------------------
// Extends Javascript with a several useful function PHP style
// ---------------------------------------------------------------------------------------------------------------------

+function()
{
  "use strict";

  if ( typeof window.WPDK_FILTERS === 'undefined' ) {

    // List of filters
    window.WPDK_FILTERS = {};

    // List of actions
    window.WPDK_ACTIONS = {};

    /**
     * Used to add an action or filter. Internal use only.
     *
     * @param {string}   type             Type of hook, 'action' or 'filter'.
     * @param {string}   tag              Name of action or filter.
     * @param {Function} function_to_add  Function hook.
     * @param {integer}  priority         Priority.
     *
     * @since 1.6.1
     */
    window._wpdk_add = function( type, tag, function_to_add, priority )
    {
      var lists = ( 'filter' == type ) ? WPDK_FILTERS : WPDK_ACTIONS;

      // Defaults
      priority = ( priority || 10 );

      if( !( tag in lists ) ) {
        lists[ tag ] = [];
      }

      if( !( priority in lists[ tag ] ) ) {
        lists[ tag ][ priority ] = [];
      }

      lists[ tag ][ priority ].push( {
        func : function_to_add,
        pri  : priority
      } );

    };

    /**
     * Hook a function or method to a specific filter action.
     *
     * WPDK offers filter hooks to allow plugins to modify various types of internal data at runtime in a similar
     * way as php `add_filter()`
     *
     * The following example shows how a callback function is bound to a filter hook.
     * Note that $example is passed to the callback, (maybe) modified, then returned:
     *
     * <code>
     * function example_callback( example ) {
     * 	// Maybe modify $example in some way
     * 	return example;
     * }
     * add_filter( 'example_filter', example_callback );
     * </code>
     *
     * @param {string}   tag             The name of the filter to hook the function_to_add callback to.
     * @param {Function} function_to_add The callback to be run when the filter is applied.
     * @param {integer}  priority        Optional. Used to specify the order in which the functions
     *                                   associated with a particular action are executed. Default 10.
     *                                   Lower numbers correspond with earlier execution,
     *                                   and functions with the same priority are executed
     *                                   in the order in which they were added to the action.
     * @return {boolean}
     */
    window.wpdk_add_filter = function( tag, function_to_add, priority )
    {
      _wpdk_add( 'filter', tag, function_to_add, priority );
    };

    /**
     * Hooks a function on to a specific action.
     *
     * Actions are the hooks that the WPDK core launches at specific points during execution, or when specific
     * events occur. Plugins can specify that one or more of its Javascript functions are executed at these points,
     * using the Action API.
     *
     * @since 1.6.1
     *
     * @uses _wpdk_add() Adds an action. Parameter list and functionality are the same.
     *
     * @param {string}   tag             The name of the action to which the $function_to_add is hooked.
     * @param {Function} function_to_add The name of the function you wish to be called.
     * @param {integer}  priority        Optional. Used to specify the order in which the functions associated with a
     *                                   particular action are executed. Default 10.
     *                                   Lower numbers correspond with earlier execution, and functions with the same
     *                                   priority are executed in the order in which they were added to the action.
     *
     * @return bool Will always return true.
     */
    window.wpdk_add_action = function( tag, function_to_add, priority )
    {
      _wpdk_add( 'action', tag, function_to_add, priority );
    };

    /**
     * Do an action or apply filters.
     *
     * @param {string} type Type of "do" to do 'action' or 'filter'.
     * @param {Array} args Optional. Original list of arguments. This array could be empty for 'action'.
     * @returns {*}
     */
    window._wpdk_do = function( type, args )
    {
      var hook, lists = ( 'action' == type ) ? WPDK_ACTIONS : WPDK_FILTERS;
      var tag = args[ 0 ];

      if( !( tag in lists ) ) {
        return args[ 1 ];
      }

      // Remove the first argument
      [].shift.apply( args );

      for( var pri in lists[ tag ] ) {

        hook = lists[ tag ][ pri ];

        if( typeof hook !== 'undefined' ) {

          for( var f in hook ) {
            var func = hook[ f ].func;

            if( typeof func === "function" ) {

              if( 'filter' === type ) {
                args[ 0 ] = func.apply( null, args );
              }
              else {
                func.apply( null, args );
              }
            }
          }
        }
      }

      if( 'filter' === type ) {
        return args[ 0 ];
      }

    };

    /**
     * Call the functions added to a filter hook and the filtered value after all hooked functions are applied to it.
     *
     * The callback functions attached to filter hook $tag are invoked by calling this function. This function can be
     * used to create a new filter hook by simply calling this function with the name of the new hook specified using
     * the tag parameter.
     *
     * The function allows for additional arguments to be added and passed to hooks.
     * <code>
     * // Our filter callback function
     * function example_callback( my_string, arg1, arg2 ) {
     *	// (maybe) modify my_string
     *	return my_string;
     * }
     * wpdk_add_filter( 'example_filter', example_callback, 10 );
     *
     * // Apply the filters by calling the 'example_callback' function we
     * // "hooked" to 'example_filter' using the wpdk_add_filter() function above.
     * // - 'example_filter' is the filter hook tag
     * // - 'filter me' is the value being filtered
     * // - arg1 and arg2 are the additional arguments passed to the callback.
     *
     * var value = wpdk_apply_filters( 'example_filter', 'filter me', arg1, arg2 );
     * </code>
     *
     * @param {string} tag     The name of the filter hook.
     * @param {*}      value   The value on which the filters hooked to <tt>tag</tt> are applied on.
     * @param {...*}   varargs Optional. Additional variables passed to the functions hooked to <tt>tag</tt>.
     *
     * @return {*}
     */
    window.wpdk_apply_filters = function( tag, value, varargs )
    {
      return _wpdk_do( 'filter', arguments );
    };

    /**
     * Execute functions hooked on a specific action hook.
     *
     * This function invokes all functions attached to action hook tag. It is possible to create new action hooks by
     * simply calling this function, specifying the name of the new hook using the <tt>tag</tt> parameter.
     *
     * You can pass extra arguments to the hooks, much like you can with wpdk_apply_filters().
     *
     * @since 1.6.1
     *
     * @param {string} tag  The name of the action to be executed.
     * @param {...*}   args Optional. Additional arguments which are passed on to the functions hooked to the action.
     *                      Default empty.
     *
     */
    window.wpdk_do_action = function( tag, args )
    {
      _wpdk_do( 'action', arguments );
    };
  }

  if ( typeof( window.empty ) === 'undefined' ) {

    /**
     * Like PHP empty()
     *
     * @since 1.0.0.b3
     *
     * @param {*} mixed_var
     *
     * @return {Boolean}
     */
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

      if( typeof jQuery !== 'undefined' && jQuery.isArray( mixed_var ) ) {
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

  if ( typeof( window.isset ) === 'undefined' ) {

    /**
     * Like PHP isset()
     *
     * @since 1.0.0.b3
     *
     * @return {Boolean}
     */
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

  if ( typeof( window.sprintf ) === 'undefined' ) {

    /**
     * Do a sprintf()
     *
     * @since 1.0.0.b3
     *
     * @return {*|void}
     */
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

  if ( typeof( window.join ) === 'undefined' ) {

    /**
     * Porting of PHP join function
     *
     * @param {string} glue
     * @param {Array} pieces
     * @return {*}
     */
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

  if ( typeof( window.implode ) === 'undefined' ) {

    /**
     * Porting of PHP implode
     *
     * @param {string} glue
     * @param {Array} pieces
     * @return {*}
     */
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

  if ( typeof window.wpdk_is_bool === 'undefined' ) {

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
    window.wpdk_is_bool = function ( mixed )
    {
      // Stability
      if ( true === mixed ) {
        return true;
      }

      var undef, i, len;
      var emptyValues = [undef, null, false, 0, "", "0", 'n', 'no', 'off', 'false'];
      for ( i = 0, len = emptyValues.length; i < len; i++ ) {
        if ( mixed === emptyValues[i] || ( 'string' === typeof( mixed ) && mixed.toLowerCase() == emptyValues[i] ) ) {
          return false;
        }
      }
      return true;
    };
  }

  if ( typeof window.WPDKGlyphIcons === 'undefined' ) {

    /**
     * Manage the Glyph Icon
     */
    window.WPDKGlyphIcons = (function ()
    {
      /**
       * @type {object}
       * @private
       */
      var _WPDKGlyphIcons = {
        version         : '1.0.3',
        display         : _display,
        html            : _html,

        // Glyph constants
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

        // Since 1.4.5
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

        // Since 1.4.7
        GOOGLE_PLUS     : 'wpdk-icon-gplus',

        // Since 1.4.21
        FIREFOX         : 'wpdk-icon-firefox',
        CHROME          : 'wpdk-icon-chrome',
        OPERA           : 'wpdk-icon-opera',
        IE              : 'wpdk-icon-ie',
        TAG             : 'wpdk-icon-tag',
        TAGS            : 'wpdk-icon-tags',
        DOC_INV         : 'wpdk-icon-doc-inv',

        // since 1.5.0
        HELP_CIRCLED    : 'wpdk-icon-help-circled',
        INFO_CIRCLED    : 'wpdk-icon-info-circled',

        // since 1.7.3
        CALENDAR_EMPTY    : 'wpdk-icon-calendar-empty',
        CALENDAR          : 'wpdk-icon-calendar',
        CANCEL            : 'wpdk-icon-cancel',
        DOC               : 'wpdk-icon-doc',
        FILE_IMAGE        : 'wpdk-icon-file-image',
        FOLDER_EMPTY      : 'wpdk-icon-folder-empty',
        FOLDER_OPEN_EMPTY : 'wpdk-icon-folder-open-empty',
        FOLDER_OPEN       : 'wpdk-icon-folder-open',
        FOLDER            : 'wpdk-icon-folder',
        MINUS_CIRCLED     : 'wpdk-icon-minus-circled',
        OK_CIRCLED2       : 'wpdk-icon-ok-circled2',
        OK_SQUARED        : 'wpdk-icon-ok-squared',
        PIN               : 'wpdk-icon-pin',
        PLUS_CIRCLED      : 'wpdk-icon-plus-circled'

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

      return _WPDKGlyphIcons;

    })();
  }

  if ( typeof window.WPDKUIComponents === 'undefined' ) {

    /**
     * Manage the Components
     */
    window.WPDKUIComponents = (function(){

      var _WPDKUIComponents = {
        version       : '1.0.0',

        // See WPDKUIComponents in php
        ALERT         : 'wpdk-alert',
        BUTTON        : 'wpdk-button',
        CONTROLS      : 'wpdk-controls',
        DYNAMIC_TABLE : 'wpdk-dynamic-table',
        MODAL         : 'wpdk-modal',
        POPOVER       : 'wpdk-popover',
        PREFERENCES   : 'wpdk-preferences',
        PROGRESS      : 'wpdk-progress',
        RIBBONIZE     : 'wpdk-ribbonize',
        TOOLTIP       : 'wpdk-tooltip',
        TRANSITION    : 'wpdk-transition',

        // TODO deprecated since 1.5.6 - use WPDKUIComponentEvents instead
        REFRESH_ALERT               : 'refresh.wpdk.wpdkAlert',
        REFRESH_POPOVER             : 'refresh.wpdk.wpdkPopover',
        REFRESH_TOOLTIP             : 'refresh.wpdk.wpdkTooltip',
        REFRESH_SWIPE               : 'refresh.wpdk.swipe',
        REFRESH_JQUERY_DATEPICKER   : 'refresh.wpdk.jquery.datepicker',
        REFRESH_JQUERY_AUTOCOMPLETE : 'refresh.wpdk.jquery.autocomplete',
        REFRESH_JQUERY_TABS         : 'refresh.wpdk.jquery.tabs'
      };

      return _WPDKUIComponents;

    })();
  }

  if ( typeof window.WPDKUIComponentEvents === 'undefined' ) {

    /**
     * @type {object}
     */
    window.WPDKUIComponentEvents = {
      version                     : '1.0.0',

      // Refresh event
      REFRESH_ALERT               : 'refresh.wpdk.wpdkAlert',
      REFRESH_POPOVER             : 'refresh.wpdk.wpdkPopover',
      REFRESH_TOOLTIP             : 'refresh.wpdk.wpdkTooltip',
      REFRESH_SWIPE               : 'refresh.wpdk.swipe',
      REFRESH_JQUERY_DATEPICKER   : 'refresh.wpdk.jquery.datepicker',
      REFRESH_JQUERY_AUTOCOMPLETE : 'refresh.wpdk.jquery.autocomplete',
      REFRESH_JQUERY_TABS         : 'refresh.wpdk.jquery.tabs',

      // Swipe
      SWIPE                       : 'swipe.wpdk',
      SWIPE_CHANGE                : 'change.wpdk.swipe',
      SWIPE_CHANGED               : 'changed.wpdk.swipe',

      // INPUT CLEAR
      CLEAR_INPUT                 : 'clear.wpdk.input',

      // Toggle Button
      TOGGLE_BUTTON               : 'toggle.wpdk.button',

      // Popover
      SHOW_POPOVER                : 'show.wpdk.wpdkPopover'
    };

  }

}();


// ---------------------------------------------------------------------------------------------------------------------
// WPDK (core) classes
// ---------------------------------------------------------------------------------------------------------------------

// On document ready
jQuery( function ( $ )
{
  "use strict";

  /**
   * jQuery Cookie Plugin v1.4.1
   * https://github.com/carhartl/jquery-cookie
   *
   * Copyright 2006, 2014 Klaus Hartl
   * Released under the MIT license
   */
  (function ( factory )
  {
    if ( typeof define === 'function' && define.amd ) {
      // AMD
      define( ['jquery'], factory );
    }
    else if ( typeof exports === 'object' ) {
      // CommonJS
      factory( require( 'jquery' ) );
    }
    else {
      // Browser globals
      factory( jQuery );
    }
  }( function ( $ )
  {

    var pluses = /\+/g;

    function encode( s )
    {
      return config.raw ? s : encodeURIComponent( s );
    }

    function decode( s )
    {
      return config.raw ? s : decodeURIComponent( s );
    }

    function stringifyCookieValue( value )
    {
      return encode( config.json ? JSON.stringify( value ) : String( value ) );
    }

    function parseCookieValue( s )
    {
      if ( s.indexOf( '"' ) === 0 ) {
        // This is a quoted cookie as according to RFC2068, unescape...
        s = s.slice( 1, -1 ).replace( /\\"/g, '"' ).replace( /\\\\/g, '\\' );
      }

      try {
        // Replace server-side written pluses with spaces.
        // If we can't decode the cookie, ignore it, it's unusable.
        // If we can't parse the cookie, ignore it, it's unusable.
        s = decodeURIComponent( s.replace( pluses, ' ' ) );
        return config.json ? JSON.parse( s ) : s;
      } catch (e) {
      }
    }

    function read( s, converter )
    {
      var value = config.raw ? s : parseCookieValue( s );
      return $.isFunction( converter ) ? converter( value ) : value;
    }

    var config = $.cookie = function ( key, value, options )
    {

      // Write

      if ( arguments.length > 1 && !$.isFunction( value ) ) {
        options = $.extend( {}, config.defaults, options );

        if ( typeof options.expires === 'number' ) {
          var days = options.expires, t = options.expires = new Date();
          t.setTime( +t + days * 864e+5 );
        }

        return (document.cookie = [
          encode( key ),
          '=',
          stringifyCookieValue( value ),
          options.expires ? '; expires=' + options.expires.toUTCString() : '', // use expires attribute, max-age is not supported by IE
          options.path ? '; path=' + options.path : '',
          options.domain ? '; domain=' + options.domain : '',
          options.secure ? '; secure' : ''
        ].join( '' ));
      }

      // Read

      var result = key ? undefined : {};

      // To prevent the for loop in the first place assign an empty array
      // in case there are no cookies at all. Also prevents odd result when
      // calling $.cookie().
      var cookies = document.cookie ? document.cookie.split( '; ' ) : [];

      for ( var i = 0, l = cookies.length; i < l; i++ ) {
        var parts = cookies[i].split( '=' );
        var name = decode( parts.shift() );
        var cookie = parts.join( '=' );

        if ( key && key === name ) {
          // If second argument (value) is a function it's a converter...
          result = read( cookie, value );
          break;
        }

        // Prevent storing a cookie that we couldn't decode.
        if ( !key && (cookie = read( cookie )) !== undefined ) {
          result[name] = cookie;
        }
      }

      return result;
    };

    config.defaults = {};

    $.removeCookie = function ( key, options )
    {
      if ( $.cookie( key ) === undefined ) {
        return false;
      }

      // Must not alter options, thus extending a fresh object...
      $.cookie( key, '', $.extend( {}, options, {expires : -1} ) );
      return !$.cookie( key );
    };

  } ));

  /**
   * This class manage all jQuery enhancer and hacks
   *
   * @class           WPDKjQuery
   * @author          =undo= <info@wpxtre.me>
   * @copyright       Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
   * @date            2014-03-02
   * @version         1.2.1
   */
  if ( typeof window.WPDKjQuery === 'undefined' ) {
    window.WPDKjQuery = (function ()
    {

      /**
       * This object
       *
       * @type {{version: string, jQueryVersion: _jQueryVersion, jQueryUIVersion: _jQueryUIVersion, init: _init}}
       * @private
       */
      var _WPDKjQuery = {
        version         : '1.2.1',
        jQueryVersion   : _jQueryVersion,
        jQueryUIVersion : _jQueryUIVersion,
        init            : _init
      };

      /**
       * Init
       *
       * @returns {{version: string, jQueryVersion: _jQueryVersion, jQueryUIVersion: _jQueryUIVersion, init: _init}}
       * @private
       */
      function _init ()
      {
        _initDatePicker();
        _initTabs();
        _initAutocomplete();
        _initCopyPaste();

        // Wrap the date picker with my pwn class
        $( '#ui-datepicker-div' ).wrap( '<div class="wpdk-jquery-ui"/>' );

        __initAutocomplete();

        return _WPDKjQuery;
      }

      /**
       * Initialize the Date Picker
       *
       * @private
       */
      function _initDatePicker()
      {
        // Fires to request the jQuery date picker refresh.
        wpdk_add_action( WPDKUIComponents.REFRESH_JQUERY_DATEPICKER, _initDatePicker );

        // Enable Date Picker on wpdk input class
        $( 'input.wpdk-form-date' ).datepicker();

        // Locale
        if ( $().datetimepicker ) {

          // Init the datetime picker addon
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

          var $date_controls = $( 'input.wpdk-form-datetime, input.wpdk-form-date' );

          // Init surrogate for Date and Datetime controls.
          $date_controls.on( 'change', function ()
          {

            var timestamp = $( this ).datepicker( 'getDate' );

            if( typeof timestamp === 'undefined' || timestamp === null ) {
              return;
            }

            // Fix the GMT
            timestamp.setMinutes( timestamp.getMinutes() - timestamp.getTimezoneOffset() );

            var name = $( this ).data( 'surrogate_name' );

            $( 'input[name="' + name + '"]' ).val( ( timestamp / 1000 ) );

          } );

          // Init surrogate clear
          $date_controls.on( WPDKUIComponentEvents.CLEAR_INPUT, function ()
          {
            var name = $( this ).data( 'surrogate_name' );
            $( 'input[name="' + name + '"]' ).val( '' );
          } );

          // TODO - Check for minimal date #not used becaues doesn't work
          //$( 'input.wpdk-form-date[data-date_type="start"], input.wpdk-form-datetime[data-date_type="start"]' ).each( function ()
          //{
          //  $( this ).datetimepicker( {
          //    minDate : new Date( 2010, 11, 20, 8, 30 ),
          //    maxDate : new Date( 2010, 11, 31, 17, 30 )
          //  } ).datetimepicker( 'refresh' );
          //
          //} );

        }
        else {
          if ( typeof window.console !== 'undefined' ) {
            //alert( 'Date Time Picker not loaded' );
          }
        }

        // Date Picker defaults
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
        // Attach event for refresh
        $( document ).on( WPDKUIComponents.REFRESH_JQUERY_TABS, _initTabs );

        // Get tabs
        var $tabs = $( '.wpdk-tabs' );

        // Get layout
        var layout = $tabs.data( 'layout' ) || 'horizontal';

        // Build layout
        if( 'vertical' == layout ) {
          $tabs.tabs().addClass( "ui-tabs-vertical ui-helper-clearfix" );
          $tabs.find( 'li').removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );
        }
        else {
          $tabs.tabs();
        }

        if ( document.location.href.indexOf( '#' ) > 0 ) {
          // OoO
        }
        // Enable cookie tabs remember
        else {
          $tabs.each( function ()
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
       * Select all input with data-autocomplete attribute and init the right autocomplete subset
       *
       * @private
       */
      function _initAutocomplete()
      {
        // Attach event for refresh
        $( document ).on( WPDKUIComponents.REFRESH_JQUERY_AUTOCOMPLETE, _initAutocomplete );

        $( 'input[data-autocomplete]' ).each( function ( index, element )
        {
          // Clear target on change and lost focus
          $( element ).on( 'blur change', function ()
          {
            if ( empty( $( this ).val() ) ) {
              $( $( element ).data( 'target' ) ).val( '' );
            }
          } );

          switch ( $( element ).data( 'autocomplete' ) ) {

            // Posts
            case 'posts':
              _initAutocompletePosts( element );
              break;

            // Users
            case 'users':
              _initAutocompleteUsers( element );
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
       * Attach an autocomplete Ajax event when an input has the `data-autocomplete="users"` attribute.
       * Usually you will use an input text. When you digit something an Ajax call 'wpdk_action_autocomplete_users'
       * is made.
       *
       * @private
       */
      function _initAutocompleteUsers( element )
      {
        //var id = ( typeof current_site_id !== 'undefined' ) ? '&site_id=' + current_site_id : '';

        // Calculate position
        var position = { offset : '0, -1' };
        if ( typeof isRtl !== 'undefined' && isRtl ) {
          position.my = 'right top';
          position.at = 'right bottom';
        }

        // Do autocomplete
        $( element ).autocomplete( {
          delay     : $( element ).data( 'delay' ) || 500,
          minLength : $( element ).data( 'min_length' ) || 2,
          position  : position,

          // Source
          source : function( request, response ) {
            $.post( wpdk_i18n.ajaxURL,
              {
                action      : 'wpdk_action_autocomplete_users',
                avatar      : $( element ).data( 'avatar' ) || true,
                avatar_size : $( element ).data( 'avatar_size' ) || 32,
                query       : $( element ).data( 'query' ) || ["user_login","user_nicename","user_email"],
                term        : request.term
              }, function( data ) {
                response( data );
              });
          },

          // Select
          select : function ( event, ui )
          {
            if ( typeof ui.item.href !== 'undefined' ) {
              document.location = ui.item.href;
            }
            else {
              var target = $( element ).data( 'target' );
              var as_text = $( element ).data( 'as_text' );
              var as_value = $( element ).data( 'as_value' );

              if ( !empty( target ) ) {
                $( target  ).val( ui.item.id );
              }

              if ( !empty( as_value ) ) {
                $( element ).data( 'value', ui.item[as_value] );
              }

              if ( !empty( as_text ) ) {
                if( $.isPlainObject( as_text ) ) {

                  var stack = [];

                  for( var property in as_text ) {
                    var str = sprintf( as_text[property], ui.item[property] );
                    stack.push( str );
                  }

                  $( element ).data( 'text', implode( '', stack  ) );
                }
                else {
                  $( element ).data( 'text', ui.item[as_text] )
                }
              }

            }
          },

          // Open
          open    : function ()
          {
            $( this ).addClass( 'wpdk-autocomplete-open' );
          },
          close   : function ()
          {
            $( this ).removeClass( 'wpdk-autocomplete-open' );
          }
        } ).data( "ui-autocomplete" )._renderItem = function( ul, item ) {
            return $( '<li>' )
            .append( '<a class="clearfix">' + item.label + '</a>' )
            .appendTo( ul );
            };
      }

      /**
       * Attach an autocomplete Ajax event when an input has the `data-autocomplete="posts"` attribute.
       * Usually you will use an input text. When you digit something an Ajax call 'wpdk_action_autocomplete_posts'
       * is made.
       *
       * @param element DOM element
       *
       * @private
       */
      function _initAutocompletePosts( element )
      {
        // Init
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
                    // The data attribute post type contains the post type id
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
                  response( data );
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

        // This is a hack to send in POST/GET the new value in the multiple select tag
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

        // Copy & Paste
        $( document ).on( 'click', '.wpdk-form-button-copy-paste', false, function ()
        {

          var options,
              copy, paste,
              element_copy_type, element_paste_type,
              value, text;

          // Options
          options = $( this ).attr( 'data-options' ) ? $( this ).attr( 'data-options' ).split( ' ' ) : [];

          // @todo add event/filter

          copy = $( '#' + $( this ).attr( 'data-copy' ) );
          paste = $( '#' + $( this ).attr( 'data-paste' ) );

          // Copy from and paste to...
          element_copy_type = copy.get( 0 ).tagName;

          // Check source HTML element
          switch ( element_copy_type.toLowerCase() ) {

            // INPUT
            case 'input':
              value = copy.data( 'value' ) || copy.val();
              text  = copy.data( 'text' ) || copy.val();
              if ( $.inArray( 'clear_after_copy', options ) !== false ) {
                copy.val( '' );
              }
              break;

            // SELECT
            case 'select':
              value = $( 'option:selected', copy ).val();
              text = $( 'option:selected', copy ).text();
              break;
          }

          if ( value != '' ) {

            // Paste to...
            element_paste_type = paste.get( 0 ).tagName;

            // Check target HTML element
            switch ( element_paste_type.toLowerCase() ) {

              // SELECT
              case 'select':
                paste.append( '<option class="wpdk-form-option" value="' + value + '">' + text + '</option>' );
                break;
            }
          }
        } );

        // Remove
        $( document ).on( 'click', '.wpdk-form-button-remove', false, function ()
        {
          var remove_from = $( this ).attr( 'data-remove_from' );
          $( 'option:selected', '#' + remove_from ).remove();
        } );
      }

      // ---------------------------------------------------------------------------------------------------------------
      // Utility
      // ---------------------------------------------------------------------------------------------------------------

      /**
       * Return the jQuery version.
       *
       * @return {string}
       */
      function _jQueryVersion()
      {
        return $().jquery;
      }

      /**
       * Return the jQuery UI version. Return false if jQuery UI is not loaded
       *
       * @returns {string|boolean}
       */
      function _jQueryUIVersion()
      {
        if ( $.ui && $.ui.version ) {
          return $.ui.version;
        }
        return false;
      }

      // ---------------------------------------------------------------------------------------------------------------
      // Deprecated
      // ---------------------------------------------------------------------------------------------------------------

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

      return _init();

    })();
  }

  /**
   * Utility to manage the php WPDKAjaxResponse
   *
   * @class           WPDKAjaxResponse
   * @author          =undo= <info@wpxtre.me>
   * @copyright       Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
   * @date            2014-03-26
   * @version         1.0.3
   * @since           1.4.0
   *
   * @param {string} response JSON response
   * @constructor
   */
  if ( typeof window.WPDKAjaxResponse === 'undefined' ) {
    window.WPDKAjaxResponse = function ( response ) {

      //console.log( 'WPDKAjaxResponse construct' );

      // Resolve conflict
      //var $ = window.jQuery;

      this.__version = '1.0.3';
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

  if ( typeof window.WPDKUIControlDateTime === 'undefined' ) {
    // TODO Prototype
    window.WPDKUIControlDateTime = {
      // TODO
      mySQLDateTime : function ( $e ) {},

      // TODO
      mySQLDate     : function ( $e )
      {
        var d = $e.val();
        var date = new Date( d );

        var result = date.getFullYear() + '-' +
          date.getMonth() + '-' +
          date.getDate();

        return result;

      },

      /**
       * Set the right date and time in input hidden field and surrogate.
       *
       * @param {object} $e Element.
       * @param {int}    d  Date in timestamp: PHP time().
       */
      setDate : function ( $e, d )
      {

        // Get the surrogate
        var $surrogate = $( 'input[name="' + $e.attr( 'name' ) + '-surrogate' + '"]' );

        if ( empty( d ) ) {
          $surrogate.val( '' );
          $e.val( '' );
        }
        else {
          var date = new Date( ( d * 1000 ) );
          $surrogate.datepicker( 'setDate', date );
          $e.val( d );
        }
      }
    };
  }

  /**
   * The main WPDK (core) class
   *
   * @class           WPDK
   * @author          =undo= <info@wpxtre.me>
   * @copyright       Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
   * @date            2014-02-10
   * @version         1.0.0
   *
   */
  if ( typeof window.WPDK === 'undefined' ) {
    window.WPDK = (function () {

      /**
       * This object
       *
       * @type {{version: string, init: _init, loading: _loading, reloadDocument: _reloadDocument, refresh: *}}
       * @private
       */
      var _WPDK = {
        version        : '1.7.3',
        init           : _init,
        loading        : _loading,
        reloadDocument : _reloadDocument
      };

      /**
       * Initialize all Javascript hook.
       * Initialize all Javascript hook.
       *
       * @returns {{version: string, init: _init, loading: _loading, reloadDocument: _reloadDocument, refresh: *}}
       * @private
       */
      function _init ()
      {
        // Hack WordPress 3.8 menu
        _hackMenu();

        return _WPDK;
      }

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
      }

      /**
       * Reload current document with clear and waiting effects
       *
       * @since 1.0.0.b3
       *
       * @return {Boolean}
       */
      function _reloadDocument ()
      {
        if ( 0 == arguments.length ) {
          $( '<div id="wpdk-mask" />' ).appendTo( 'body' );
        }
        document.location.reload( true );
      }

      /**
       * Remove the A tag to create a separator item for wpXtreme menu.
       *
       * @private
       */
      function _hackMenu()
      {
        $( 'ul#adminmenu .wp-submenu a[href*="wpdk_menu_divider"]' ).each( function ()
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
       * @param {string} rgbString
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

      return _init();

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
      'jQuery'    : WPDKjQuery.jQueryVersion(),
      'jQuery UI' : WPDKjQuery.jQueryUIVersion(),
      'WPDK'      : WPDK.version
    };

    for ( version in versions ) {
      cookie.push( sprintf( '"%s":"v.%s"', version, versions[version] ) );
    }

    var json = sprintf( '{%s}', cookie.join(',') );

    jQuery.cookie( 'wpdk_javascript_library_versions', json, { path : '/' } );
  }();

  /**
   * Fires when WPDK is loaded.
   */
  wpdk_do_action( 'WPDK' );

});