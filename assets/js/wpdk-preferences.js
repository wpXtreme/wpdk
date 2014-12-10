/**
 * This class manage the Preferences view
 *
 * @class           WPDKPreferences
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date            2014-12-03
 * @version         1.1.0
 *
 * @history         1.1.0 - Added combo select click event in order to redirect to create/edit a post type.
 */

jQuery( function ( $ )
{
  "use strict";

  if ( typeof( window.WPDKPreferences ) === 'undefined' ) {
    window.WPDKPreferences = (function ()
    {

      /**
       * This object
       *
       * @type {{version: string, init: _init}}
       * @private
       */
      var _WPDKPreferences = {
        version : '1.1.0',
        init    : _init
      };


      /**
       * Init
       *
       * @returns {{version: string, init: _init}}
       * @private
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

        // Create/edit post button
        $( '.wpdk-preferences-create-edit-post-button' ).click( function( e )
        {
          e.preventDefault();
          var post_stype = $( $( this ).data( 'post_type' ) ).val();
          var url = $( this ).data( 'url' );
          document.location = url + '&post_type=' + post_stype;
          return false;
        } );

        return _WPDKPreferences;
      };

      return _init();

    })();
  }

} );