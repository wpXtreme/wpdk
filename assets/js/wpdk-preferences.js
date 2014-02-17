/**
 * This class manage the Preferences view
 *
 * @class           WPDKPreferences
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date            2014-02-09
 * @version         1.0.2
 */

jQuery( function ( $ )
{
  "use strict";

  if ( typeof( window.WPDKPreferences ) === 'undefined' ) {
    window.WPDKPreferences = (function ()
    {

      /**
       * Internal class pointer
       *
       * @var {object} $t
       */
      var $t = {
        version : '1.0.2',
        init    : _init
      };

      /**
       * Return an instance of WPDKPreferences class
       *
       * @return {object}
       */
      function _init()
      {
        // Display a confirm dialog box before reset a specified branch to default values
        $( 'input[name=wpdk_preferences_reset_all]' ).click( function ()
        {
          return confirm( $( this ).data( 'confirm' ) );
        } );

        // Display a confirm dialog box before reset a specified branch to default values
        $( 'input[name=reset-to-default-preferences]' ).click( function ()
        {
          return confirm( $( this ).data( 'confirm' ) );
        } );

        return $t;
      };

      return $t.init();

    })();
  }

} );