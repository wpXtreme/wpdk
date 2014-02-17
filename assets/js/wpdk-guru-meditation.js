/**
 * Tribute to Amiga - Software failure / Guru Meditation
 * Usage: GuruMeditation.display( 'Your custom message' );
 *
 * @class           GuruMeditation
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date            2014-02-04
 * @version         1.0.2
 */

if ( typeof( window.GuruMeditation ) === 'undefined' ) {
  window.GuruMeditation = (function ()
  {
    var $t = {
      version : '1.0.2',
      display : _display,
      hide    : _hide
    }, div, timer;

    /**
     * Display Guru Meditation
     *
     * @param {string} $error Your message
     */
    function _display( $error )
    {
      _htmlMarkup( $error );

      if ( !$( 'body > #guru-meditation' ).length ) {

        $( 'body' ).prepend( div );

        var $guru_meditation = $( '#guru-meditation' );

        // Blink
        timer = setInterval( function () { $guru_meditation.toggleClass( 'red' ); }, 1000 );

        // If click hide
        $guru_meditation.on( 'click', _hide );
      }
    };

    /**
     * Hide
     */
    function _hide()
    {
      if ( $( 'body > #guru-meditation' ).length ) {
        clearInterval( timer );
        $( '#guru-meditation' ).remove();
        $( '#guru-meditation-style' ).remove();
      }
    };

    /**
     * Prepare HTML markup
     *
     * @param {string} $error Your message
     * @private
     */
    function _htmlMarkup( $error )
    {

      if ( 'undefined' == typeof( $error ) ) {
        $error = '#00000025.65045330';
      }

      div = '<style id="guru-meditation-style" type="text/css">' +
        '#guru-meditation {' +
        'height:120px;' +
        'background-color:#111;' +
        'border:6px solid #111;' +
        'text-align:center;' +
        '}' +
        '#guru-meditation.red {' +
        'border-color:#b00' +
        '}' +
        '#guru-meditation p {' +
        'font-size:18px;' +
        'font-family: \'Times New Roman\';' +
        'margin:24px 0;' +
        'color: #b00;' +
        'text-align:center;' +
        '}' +
        '</style>' +
        '<div id="guru-meditation">' +
        '<p>Software Failure. Press left mouse button to continue.</p>' +
        '<p>Guru meditation <span>' +
        $error +
        '</span></p>' +
        '</div>';
    }

    return $t;

  })();
}