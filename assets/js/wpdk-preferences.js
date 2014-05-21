/**
 * This class manage the Preferences view
 *
 * @class           WPDKPreferences
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date            2014-02-25
 * @version         1.0.3
 */

jQuery( function ( $ )
{
  "use strict";

  if ( typeof( window.WPDKPreferences ) === 'undefined' ) {
    window.WPDKPreferences = (function ()
    {

      // This object
      var _WPDKPreferences = {
        version : '1.0.3',
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
        $( 'input[name="wpdk_preferences_reset_all"]' ).click( function ()
        {
          return confirm( $( this ).data( 'confirm' ) );
        } );

        // Display a confirm dialog box before reset a specified branch to default values
        $( 'input[name="reset-to-default-preferences"]' ).click( function ()
        {
          return confirm( $( this ).data( 'confirm' ) );
        } );

        return _WPDKPreferences;
      };

      return _init();

    })();
  }

} );