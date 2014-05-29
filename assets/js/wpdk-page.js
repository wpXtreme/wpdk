/**
 * WPDK UI Page View
 *
 * @class           WPDKUIPageView
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date            2014-05-28
 * @version         1.0.0
 */

jQuery( function ( $ )
{
  "use strict";

  if ( typeof( window.WPDKUIPageView ) === 'undefined' ) {
    window.WPDKUIPageView = (function ()
    {

      // Internal object
      var _WPDKUIPageView = {
        version : '1.0.0',
        init    : _init
      };

      /**
       * Return a singleton instance of WPDKUIPageView class
       *
       * @returns {{}}
       */
      function _init()
      {
        //
        $( 'a.wpdk-ui-navigator-bullet' ).on( 'click', function() {

          // Width of a view
          var width = $( '.wpdk-ui-page-view-container[data-view="0"]' ).width();

          // Current index
          var index = parseInt( $( this ).data( 'bullet' ) );

          // Animate main container
          $( '.wpdk-ui-page-view-mask' ).css( { marginLeft: -( width * index ) + 'px' } );

          // Remove class current from all bullet
          $( 'a.wpdk-ui-navigator-bullet' ).removeClass( 'current' );

          // Add current to this
          $( this ).addClass( 'current' );

        } );


        return _WPDKUIPageView;
      }

      return _init();

    })();
  }

} );